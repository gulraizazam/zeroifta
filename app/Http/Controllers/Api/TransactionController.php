<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Store a new transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get client_id from the authenticated token
        $clientId = $request->user_api_client;

        // Generate a unique transaction ID
        $transactionId = 'txn_' . Str::random(20);

        // Create the transaction
        $transaction = ApiTransaction::create([
            'transaction_id' => $transactionId,
            'client_id' => $clientId,
            'data' => $request->data,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'transaction' => [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'created_at' => $transaction->created_at,
            ]
        ], 201);
    }
}
