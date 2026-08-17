<?php

namespace App\Http\Controllers\api\client;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    /** GET /api/client/cities */
    public function cities(): JsonResponse
    {
        $cities = Screen::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->pluck('city');
        return response()->json(['cities' => $cities]);
    }

    /** GET /api/client/companies */
    public function companies(): JsonResponse
    {
        $companies = User::where('type', User::ROLE_COMPANY)
            ->where('approval_status', 'approved')
            ->get(['id', 'company_name']);

        return response()->json(['companies' => $companies]);
    }
}
