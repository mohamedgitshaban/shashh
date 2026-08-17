<?php

namespace App\Http\Controllers\api\company;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Screen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScreenController extends Controller
{
    public function index(Request $request)
    {
        $screens = Screen::where('company_id', $request->user()->id)
            ->with(['bookings' => function ($query) {
                $query->where('status', \App\Models\Booking::STATUS_LIVE);
            }])
            ->latest()
            ->get();

        return response()->json(['screens' => $screens], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'screen_type'         => 'required|string|max:100',
            'width'               => 'nullable|numeric|min:0',
            'height'              => 'nullable|numeric|min:0',
            'daily_impressions'   => 'nullable|integer|min:0',
            'description'         => 'nullable|string',

            'price_per_day'       => 'required|numeric|min:0',
            'min_booking_days'    => 'nullable|integer|min:1',
            'rotation_duration'   => 'nullable|integer|min:5|max:120',

            'active_days'         => 'nullable|array',
            'active_days.*'       => 'string|in:Sun,Mon,Tue,Wed,Thu,Fri,Sat',
            'display_from'        => 'nullable|date_format:H:i',
            'display_to'          => 'nullable|date_format:H:i',
            'is_247'              => 'nullable|boolean',
            'blackout_dates'      => 'nullable|string',

            'street_address'      => 'required|string|max:255',
            'landmark'            => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'district'            => 'nullable|string|max:100',
            'city'                => 'nullable|string|max:100',

            // 'photos'              => 'nullable|array|max:6',
            'photos'            => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'cr_document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'municipality_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle file uploads
        if ($request->hasFile('photos')) {
            $data['photos'] = $request->file('photos')->store('screens/photos');
        }

        if ($request->hasFile('cr_document')) {
            $data['cr_document'] = $request->file('cr_document')->store('screens/docs');
        }

        if ($request->hasFile('municipality_permit')) {
            $data['municipality_permit'] = $request->file('municipality_permit')->store('screens/docs');
        }

        $data['company_id']      = $request->user()->id;
        $data['approval_status'] = 'in_review';

        $screen = Screen::create($data);

        return response()->json(['screen' => $screen], 201);
    }

    public function show(Request $request, Screen $screen)
    {
        if ($screen->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['screen' => $screen], 200);
    }

    public function update(Request $request, Screen $screen)
    {
        if ($screen->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'                => 'sometimes|required|string|max:255',
            'screen_type'         => 'sometimes|required|string|max:100',
            'width'               => 'nullable|numeric|min:0',
            'height'              => 'nullable|numeric|min:0',
            'daily_impressions'   => 'nullable|integer|min:0',
            'description'         => 'nullable|string',

            'price_per_day'       => 'sometimes|required|numeric|min:0',
            'min_booking_days'    => 'nullable|integer|min:1',
            'rotation_duration'   => 'nullable|integer|min:5|max:120',

            'active_days'         => 'nullable|array',
            'active_days.*'       => 'string|in:Sun,Mon,Tue,Wed,Thu,Fri,Sat',
            'display_from'        => 'nullable|date_format:H:i',
            'display_to'          => 'nullable|date_format:H:i',
            'is_247'              => 'nullable|boolean',
            'blackout_dates'      => 'nullable|string',

            'street_address'      => 'sometimes|required|string|max:255',
            'landmark'            => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'district'            => 'nullable|string|max:100',
            'city'                => 'nullable|string|max:100',

            'photos'              => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            // 'photos.*'            => 'file|mimes:jpg,jpeg,png,webp|max:10240',
            'cr_document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'municipality_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('photos')) {
            $data['photos'] = $request->file('photos')->store('screens/photos');
        }

        if ($request->hasFile('cr_document')) {
            $data['cr_document'] = $request->file('cr_document')->store('screens/docs');
        }

        if ($request->hasFile('municipality_permit')) {
            $data['municipality_permit'] = $request->file('municipality_permit')->store('screens/docs');
        }

        // Any update resets to in_review for re-verification
        $data['approval_status'] = 'in_review';
        $data['rejection_reason'] = null;
        $data['reviewed_by']     = null;
        $data['reviewed_at']     = null;

        $screen->update($data);

        return response()->json(['screen' => $screen->fresh()], 200);
    }

    public function destroy(Request $request, Screen $screen)
    {
        if ($screen->company_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $screen->delete();

        return response()->json(['message' => 'Screen deleted successfully'], 200);
    }
    public function cities()
    {
        $cities = City::orderBy('country')->orderBy('name')->get(['id', 'name', 'country']);

        return response()->json(['cities' => $cities], 200);
    }
}
