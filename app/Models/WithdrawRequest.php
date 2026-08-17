<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class WithdrawRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'company_id',
        'amount',
        'bank_name',
        'account_holder_name',
        'iban',
        'status',
        'proof_file',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    protected $appends = [
        'has_invoice',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Whether a proof-of-transfer file exists to download. The file itself lives on
     * the private "local" disk (financial document — not publicly linkable), so the
     * frontend fetches it through the authenticated `/withdraw-requests/{id}/invoice`
     * route rather than a raw storage URL.
     */
    public function getHasInvoiceAttribute(): bool
    {
        return ! empty($this->proof_file);
    }

    /**
     * Approve this request. The amount was already reserved (deducted from the
     * company's balance) when the request was created, so no balance change here —
     * $proofPath is the admin-uploaded transfer receipt, stored under
     * withdraw_proofs/ on the default (private) disk.
     */
    public function approve(User $reviewer, string $proofPath): void
    {
        $this->update([
            'status'      => self::STATUS_APPROVED,
            'proof_file'  => $proofPath,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /** Reject this request and refund the reserved amount back to the company's balance. */
    public function reject(User $reviewer, string $reason): void
    {
        DB::transaction(function () use ($reviewer, $reason) {
            User::whereKey($this->company_id)->increment('balance', $this->amount);

            $this->update([
                'status'           => self::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'reviewed_by'      => $reviewer->id,
                'reviewed_at'      => now(),
            ]);
        });
    }
}
