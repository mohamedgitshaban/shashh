<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WithdrawRequestController extends Controller
{
    /**
     * GET /api/company/withdraw-requests
     *
     * The authenticated company's own withdrawal requests (pending/approved/rejected).
     * Optional filter: ?status=pending|approved|rejected
     */
    public function index(Request $request): JsonResponse
    {
        $query = WithdrawRequest::where('company_id', $request->user()->id)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json(['withdraw_requests' => $query->get()]);
    }

    /**
     * POST /api/company/withdraw-requests
     *
     * Request a payout. The requested amount is reserved (deducted from `balance`)
     * immediately so it can't be double-requested; it's refunded back if an admin
     * later rejects the request.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount'               => 'required|numeric|min:1',
            'bank_name'            => 'required|string|max:255',
            'account_holder_name'  => 'required|string|max:255',
            'iban'                 => 'required|string|max:34',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $withdrawRequest = DB::transaction(function () use ($data, $request) {
            /** @var User $user */
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

            if ($data['amount'] > $user->balance) {
                return null;
            }

            $user->decrement('balance', $data['amount']);

            return WithdrawRequest::create([
                'company_id'           => $user->id,
                'amount'               => $data['amount'],
                'bank_name'            => $data['bank_name'],
                'account_holder_name'  => $data['account_holder_name'],
                'iban'                 => $data['iban'],
                'status'               => WithdrawRequest::STATUS_PENDING,
            ]);
        });

        if (! $withdrawRequest) {
            return response()->json(['message' => 'Requested amount exceeds available balance.'], 422);
        }

        return response()->json([
            'message'          => 'Withdrawal request submitted.',
            'withdraw_request' => $withdrawRequest,
        ], 201);
    }

    /**
     * GET /api/company/withdraw-requests/{id}/invoice
     *
     * Streams the admin-uploaded proof-of-transfer for an approved request. Financial
     * document — kept on the private disk and only reachable by its owning company.
     */
    public function invoice(Request $request, int $id): \Symfony\Component\HttpFoundation\Response
    {
        $withdrawRequest = WithdrawRequest::where('company_id', $request->user()->id)->findOrFail($id);

        if (! $withdrawRequest->proof_file || ! Storage::exists($withdrawRequest->proof_file)) {
            abort(404, 'No invoice available for this request.');
        }

        return Storage::download($withdrawRequest->proof_file, "payout-{$withdrawRequest->id}-invoice");
    }
}
