<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'cr' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }
        $data = $data->validated();
        // Create a new client user
        $client = \App\Models\Client::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'company_address' => $data['company_address'] ?? null,
            'vat_number' => $data['vat_number'] ?? null,
            'cr' => isset($data['cr']) ? $request->file('cr')->store('company_docs') : null,
        ]);
        $token = $client->createToken('auth_token')->plainTextToken;
        // Return a response with the created client user and token
        return response()->json(['client' => $client, 'token' => $token], 201);
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
        // Attempt to find the client user by email
        $client = \App\Models\Client::where('email', $request->email)->first();

        // Check if the client user exists and the password is correct
        if (!$client || !\Illuminate\Support\Facades\Hash::check($request->password, $client->password)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        // Create a new token for the client user
        $token = $client->createToken('auth_token')->plainTextToken;

        // Return a response with the client user and token
        return response()->json(['client' => $client, 'token' => $token], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    public function profile(Request $request)
    {
        return response()->json(['client' => $request->user()], 200);
    }
    public function updateProfile(Request $request)
    {
        $client = $request->user();
        $data = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $client->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'cr' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }
        $data = $data->validated();

        if (isset($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        if (isset($data['cr'])) {
            $data['cr'] = $request->file('cr')->store('company_docs');
        }
        $client->update($data);
        return response()->json(['client' => $client], 200);
    }
}
