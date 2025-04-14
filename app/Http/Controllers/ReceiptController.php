<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReceiptController extends Controller
{
    public function index(Request $request)
{
    // Query receipts for the given driver_id, only select necessary columns
    $receipts = Receipt::where('driver_id', $request->driver_id)
        ->select('id', 'driver_id', 'receipt_image','fuel_station_name', 'price_per_gallon', 'gallons_bought', 'location') // Only select necessary columns
        ->get();

    // Check if any receipts are found
    if ($receipts->isNotEmpty()) {
        // Prepend the base URL to receipt_image using array_map for better performance
        $receipts->transform(function ($receipt) {
            if (isset($receipt->receipt_image)) {
                $receipt->receipt_image = url('/receipts/' . $receipt->receipt_image);
            }
            return $receipt;
        });

        // Return the result
        return response()->json(['status' => 200, 'message' => 'Receipts found', 'data' => $receipts], 200);
    }

    // Return response when no receipts found
    return response()->json(['status' => 404, 'message' => 'Receipts not found', 'data' => []], 404);
}

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id'=>'required|exists:trips,id',
            'driver_id'=>'required|exists:users,id',
            'fuel_station_name' => 'required',
            'price_per_gallon' => 'required',
            'gallons_bought' => 'required',
            //'location' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422,'message' => $validator->errors()->first(),'data'=>(object)[]], 422);
        }
        $receipt = new Receipt();
        $receipt->trip_id = (int)$request->trip_id;
        $receipt->driver_id = (int)$request->driver_id;
        $receipt->fuel_station_name = $request->fuel_station_name;
        $receipt->price_per_gallon = $request->price_per_gallon;
        $receipt->gallons_bought = $request->gallons_bought;
        $receipt->location = $request->location;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('receipts'), $imageName);
            $receipt->receipt_image= $imageName;
        }
        $receipt->save();

        return response()->json(['status'=>200,'message'=>'receipts saved','data'=>$receipt],200);
    }
}
