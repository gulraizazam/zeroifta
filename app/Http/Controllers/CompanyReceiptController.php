<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyReceiptController extends Controller
{
    public function index()
    {
        $drivers = User::where('company_id', Auth::id())->pluck('id')->toArray();
        dd($drivers);
        $trips = Trip::whereIn('user_id', $drivers)->pluck('id')->toArray();
        $receipts = Receipt::with('trip')->whereIn('trip_id', $trips)->get();
        return view('company.receipts.index', compact('receipts'));

    }
}
