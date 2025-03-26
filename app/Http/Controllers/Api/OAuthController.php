<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;

class OAuthController extends Controller
{
    /**
     * Issue a token for valid client credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the client
        $client = ApiClient::where('client_id', $request->client_id)->first();

        if (!$client || !Hash::check($request->client_secret, $client->client_secret)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate JWT token
        $payload = [
            'iss' => config('app.url'),
            'sub' => $client->client_id,
            'iat' => time(),
            'exp' => time() + 900, // Token valid for 15 minutes
        ];

        $token = JWT::encode($payload, config('app.key'), 'HS256');

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 900
        ]);
    }
}
