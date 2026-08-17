<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'cr' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }
        $data = $data->validated();
        // Create a new company user
        $company = \App\Models\Company::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'vat_number' => $data['vat_number'] ?? null,
            'cr' => isset($data['cr']) ? $request->file('cr')->store('company_docs') : null,
            'approval_status' => 'in_review',
        ]);

        $company->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registration successful. Please verify your email before logging in.',
            'company' => $company,
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the request data
        $data = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }
        $data = $data->validated();

        // Attempt to find the company user by email
        $company = \App\Models\Company::where('email', $data['email'])->first();

        // Check if the company user exists and the password is correct
        if (!$company || !\Illuminate\Support\Facades\Hash::check($data['password'], $company->password)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        if (!$company->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }

        if ($company->approval_status === 'in_review') {
            return response()->json(['message' => 'Your company account is still in review.'], 403);
        }

        if ($company->approval_status === 'rejected') {
            return response()->json([
                'message' => 'Your company account was rejected.',
                'reason' => $company->rejection_reason,
            ], 403);
        }

        // Create a new token for the company user
        $token = $company->createToken('auth_token')->plainTextToken;

        // Return a response with the company user and token
        return response()->json(['company' => $company, 'token' => $token], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    public function profile(Request $request)
    {
        return response()->json(['company' => $request->user()], 200);
    }

    public function updateProfile(Request $request)
    {
        $company = $request->user();
        $currentEmail = $company->email;

        $data = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $company->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'sometimes|required|string|max:255',
            'company_address' => 'sometimes|required|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'cr' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }

        $validated = $data->validated();

        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        if (isset($validated['cr'])) {
            $validated['cr'] = $request->file('cr')->store('company_docs');
        }

        $emailChanged = isset($validated['email']) && $validated['email'] !== $currentEmail;

        $company->fill($validated);
        if ($emailChanged) {
            $company->email_verified_at = null;
        }
        $company->save();

        if ($emailChanged) {
            $company->sendEmailVerificationNotification();
        }

        return response()->json(['company' => $company], 200);
    }

    public function resendVerificationEmail(Request $request)
    {
        $data = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }

        $company = \App\Models\Company::where('email', $request->email)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found.'], 404);
        }

        if ($company->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.'], 200);
        }

        $company->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent successfully.'], 200);
    }
}
