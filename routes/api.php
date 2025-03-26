<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverContactUsController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\FuelStationController;
use App\Http\Controllers\IFTAController;
use App\Http\Controllers\IndependentTruckerController;
use App\Http\Controllers\LoginSessionsController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TestNotificationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/calculate-ifta', [IFTAController::class, 'findCheapestFuelStops']);
//Route::post('/findgas', [IFTAController::class, 'findFuelStations']);
Route::post('/findgas', [IFTAController::class, 'getDecodedPolyline'])->name('findgas');
Route::post('login',[AuthController::class,'login']);
Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::get('/get-fuel-stations/{user_id}', [FuelStationController::class, 'getFuelStations']);
Route::get('/user-trip/{user_id}', [TripController::class, 'getTrip']);
Route::post('get-profile',[AuthController::class,'getProfile']);
Route::post('updateUser',[AuthController::class,'updateUser']);
Route::post('dashboard',[DriverDashboardController::class,'index']);
//Route::middleware('auth:api')->group( function () {
    Route::post('profile',[AuthController::class,'profile']);


    Route::post('select-vehicle',[AuthController::class,'selectVehicle']);
    Route::post('password/change',[AuthController::class,'changePassword']);
    Route::post('profile/update',[AuthController::class,'profileUpdate']);
    ////dashboard/////


    //////vehcile
    Route::post('vehicles/all',[VehicleController::class,'allVehicles']);
    Route::post('trips/all',[VehicleController::class,'allTrips']);
    Route::post('trip/detail',[TripController::class,'tripDetail']);
    Route::post('vehicle',[VehicleController::class,'index']);

    Route::post('vehicle/update',[VehicleController::class,'update']);
    Route::delete('vehicle/{id}',[VehicleController::class,'delete']);
    ////receipts
    Route::post('receipts',[ReceiptController::class,'index']);
    Route::post('receipt/create',[ReceiptController::class,'create']);
    ////contact us
    Route::post('contactus',[DriverDashboardController::class,'contactus']);

    Route::post('contactus/all',[DriverContactUsController::class,'getContactUs']);
    Route::post('chat/get',[DriverContactUsController::class,'getChat']);
    Route::post('chat/send',[DriverContactUsController::class,'send']);

    Route::post('/trip/start', [IFTAController::class, 'getDecodedPolyline']);
    Route::post('/get-active-trip', [TripController::class, 'getActiveTrip']);
    Route::post('/check-active-trip', [TripController::class, 'checkActiveTrip']);
    Route::post('/trip/update', [IFTAController::class, 'updateTrip']);
    Route::post('/trip/deviate', [TripController::class, 'updateTrip']);
    Route::post('/trip/delete', [TripController::class, 'deleteTrip']);
    Route::post('/trip/complete', [TripController::class, 'completeTrip']);

    Route::post('/save-fuel-stations', [FuelStationController::class, 'store']);

    Route::post('stops/add',[TripController::class,'storeStop']);

    ///
    Route::post('/payment-methods', [PaymentMethodController::class, 'allPaymentMethod']);

    Route::get('/payment-methods/{id}', [PaymentMethodController::class, 'getPaymentMethod']);
    Route::put('/payment-methods/{id}', [PaymentMethodController::class, 'editPaymentMethod']);
    Route::post('/payment-methods/delete', [PaymentMethodController::class, 'deletePaymentMethod']);
    Route::post('/payment-methods/default', [PaymentMethodController::class, 'makeDefault']);
    Route::post('/payment-methods/getDetails', [PaymentMethodController::class, 'getTransactionsByPaymentMethod']);
    /////

    // Cancel subscription
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription']);

    // Get subscription details
    Route::get('/subscription/details', [SubscriptionController::class, 'getSubscriptionDetails']);

    ///
    Route::post('/remove-vehicle', [VehicleController::class, 'removeVehicleByTrucker']);
//});
Route::post('vehicle/add',[IndependentTruckerController::class,'addVehicle']);
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/resend-otp', [OtpController::class, 'resendOtp']);
Route::post('selectedplan/store', [SubscriptionController::class, 'storeSelectedPlan']);
Route::post('getSelectedPlan',[SubscriptionController::class, 'getSelectedPlan']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
//Route::post('/trip/start', [TripController::class, 'store']);
Route::post('store-login-session', [LoginSessionsController::class, 'storeLoginSession']);
///
Route::post('/payment-methods/store', [PaymentMethodController::class, 'addPaymentMethod']);
Route::post('register',[IndependentTruckerController::class,'store']);
Route::post('generatetoken',[SubscriptionController::class,'generateToken']);
///
Route::post('get-company-by-driver',[TestNotificationController::class,'getCompanyByDriver']);
Route::post('get-company-fcm-tokens',[TestNotificationController::class,'getCompanyFCMTokens']);

// Get available subscription plans
Route::post('/subscription/plans', [SubscriptionController::class, 'getPlans']);
Route::post('/oauth/token', [App\Http\Controllers\Api\OAuthController::class, 'issueToken']);
Route::post('/transactions', [App\Http\Controllers\Api\TransactionController::class, 'store'])
    ->middleware('verify.api.token');

Route::get('/reroute', [NavigationController::class, 'reroute']);


