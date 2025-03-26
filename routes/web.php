<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\FuelTaxController;
use App\Http\Controllers\Admin\PlansController;
use App\Http\Controllers\Admin\UsersController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\DriversController;
use App\Http\Controllers\Company\DriverVehiclesController;
use App\Http\Controllers\Company\VehiclesController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TestNotificationController;
use App\Models\CompanyContactUs;
use App\Models\FcmToken;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/testsocket', [AdminController::class, 'socket']);
Route::get('/testftp', [AdminController::class, 'testftp']);
Route::get('/get-fcm-token', function () {
    $token = DB::table('fcm_tokens')->where('user_id', auth()->id())->value('token');
    return response()->json(['fcm_token' => $token]);
});
Route::get('/test-log', function () {
    Log::channel('cloudwatch')->info('This is a test log from Laravel.');
    return "Log sent to CloudWatch!";
});
Route::post('/store-fcm-token', function (Request $request) {
    $request->validate(['fcm_token' => 'required|string']);

    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    $findToken = FcmToken::where('user_id', $user->id)->first();
    if($findToken){
      DB::table('fcm_tokens')->where('user_id', $user->id)->update(['token' => $request->fcm_token]);

    }else{
        FcmToken::create([
          'user_id'=>$user->id,
          'token'=>$request->fcm_token
        ]);
    }
    // Store the token in the database

    return response()->json(['message' => 'Token stored successfully']);
});
Route::get('/notifications/count', function () {
  $count = Notification::where('user_id', Auth::id())->where('is_read', false)->count();
  return response()->json(['count' => $count]);
})->middleware('auth');
Route::get('/notifications/latest', function () {
  $notifications = App\Models\Notification::where('user_id', auth()->id())
      ->orderBy('created_at', 'desc')
      ->take(10)
      ->get();

  return view('includes.notifications', compact('notifications'));
})->middleware('auth');
Route::get('/send-test-notification', function () {


  $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
$messaging = $factory->createMessaging();
$token = FcmToken::where('user_id',159)->pluck('token')->first();
$message = [
    'notification' => [
        'title' => 'Test Notification',
        'body' => 'Hello from Firebase!',
    ],
    'token' => $token,
];

$messaging->send($message);
dd("done");
});
Route::get('/testmail', function(){
  Mail::to('gulraizazam00@gmail.com');
});
Route::get('maptest', function () {
  return view('maptest');
});
Route::get('terms-and-conditions', function () {
  return view('terms-and-conditions');
});
Route::get('testroute', function () {
  return view('testsocket');
});
Route::get('/read-dat-file', [UsersController::class, 'readDatFile'])->name('read.dat.file');
Route::get('/subscription', [AdminController::class, 'subscription']);
Route::get('/buy/{plan}', [AdminController::class, 'buy'])->name('buy');
Route::post('/paynow', [AdminController::class, 'pay'])->name('pay.demo');
Route::get('login', function () {
  return view('login');
});
Route::get('company/register', [CompanyController::class, 'create'])->name('company.register');
Route::post('company/register', [CompanyController::class, 'store'])->name('register');
Route::post('login', [AdminController::class, 'login'])->name('login');
Route::get('/company/remove-vehicle/{vehicle}', [VehiclesController::class, 'removeVehicleByCompany']);
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.updatepas');
Route::middleware('auth')->group(function () {
  Route::group(
    [
       'prefix' => LaravelLocalization::setLocale(),
      'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
      Route::get('/', [AdminController::class, 'index'])->name('dashboard')->middleware('auth');
      Route::get('/get-theme', [ThemeController::class, 'getTheme']);
      Route::post('/update-theme', [ThemeController::class, 'update'])->name('user.theme.update');
      Route::get('profile', [UsersController::class, 'profile'])->name('profile');
      Route::post('profile/update', [UsersController::class, 'profileUpdate'])->name('profile.update');
      Route::get('password/change', [UsersController::class, 'changePasswordUpdate'])->name('password.change');
      Route::post('password/change', [UsersController::class, 'passwordUpdate'])->name('passwords.updatePass');
      Route::get('logout', [AdminController::class, 'logout'])->name('logout');
      Route::get('companies', [CompaniesController::class, 'index'])->name('companies');
      Route::get('companies/edit/{id}', [CompaniesController::class, 'edit'])->name('companies.edit');
      Route::post('companies/update/{id}', [CompaniesController::class, 'update'])->name('companies.update');
      Route::get('companies/delete/{id}', [CompaniesController::class, 'delete'])->name('companies.delete');
      Route::post('companies/change-password/{id}', [CompaniesController::class, 'changePassword'])->name('companies.changePassword');
      Route::get('plans', [PlansController::class, 'index'])->name('plans');
      Route::get('plans/create', [PlansController::class, 'create'])->name('plans.create');
      Route::post('plans/store', [PlansController::class, 'store'])->name('plans.store');
      Route::get('plans/edit/{id}', [PlansController::class, 'edit'])->name('plans.edit');
      Route::post('plans/update/{id}', [PlansController::class, 'update'])->name('plans.update');
      Route::get('plans/delete/{id}', [PlansController::class, 'delete'])->name('plans.delete');
      Route::get('fuel_taxes', [FuelTaxController::class, 'index'])->name('fuel_taxes');
      Route::get('fuel_taxes/create', [FuelTaxController::class, 'create'])->name('fuel_taxes.create');
      Route::post('fuel_taxes/store', [FuelTaxController::class, 'store'])->name('fuel_taxes.store');
      Route::get('fuel_taxes/edit/{id}', [FuelTaxController::class, 'edit'])->name('fuel_taxes.edit');
      Route::post('fuel_taxes/update/{id}', [FuelTaxController::class, 'update'])->name('fuel_taxes.update');
      Route::get('fuel_taxes/delete/{id}', [FuelTaxController::class, 'delete'])->name('fuel_taxes.delete');
      Route::get('contactus/all', [AdminController::class, 'contactUsForms'])->name('admin.contactus');
      Route::get('contactform/read/{id}', [AdminController::class, 'readForm'])->name('contactform.detail');
      Route::get('contactform/delete/{id}', [AdminController::class, 'deleteForm'])->name('contactform.delete');
      ////
      Route::group(['middleware' => ['check.subscription']], function () {
        Route::get('vehicles/all', [VehiclesController::class, 'index'])->name('allvehicles');
        Route::get('vehicles/create', [VehiclesController::class, 'create'])->name('vehicles.create');
        Route::post('vehicles/store', [VehiclesController::class, 'store'])->name('vehicle.store');
        Route::get('vehicles/edit/{id}', [VehiclesController::class, 'edit'])->name('vehicle.edit');
        Route::post('vehicles/update/{id}', [VehiclesController::class, 'update'])->name('vehicle.update');
        Route::get('vehicles/delete/{id}', [VehiclesController::class, 'delete'])->name('vehicle.delete');
        Route::get('/vehicles/import', [VehiclesController::class, 'importForm'])->name('vehicles.importform');
        Route::post('/vehicles/import', [VehiclesController::class, 'import'])->name('vehicle.import');
        Route::post('/vehicles/check-vin', [VehiclesController::class, 'checkVin'])->name('vehicle.checkVin');
        Route::get('/driver/vehicles', [DriverVehiclesController::class, 'index'])->name('driver_vehicles');
        Route::get('driver/vehicles/add', [DriverVehiclesController::class, 'create'])->name('driver_vehicles.add');
        Route::post('driver/vehicles/store', [DriverVehiclesController::class, 'store'])->name('driver_vehicles.store');
        Route::post('driver/vehicles/reassign', [DriverVehiclesController::class, 'reassign'])->name('driver_vehicles.reassign');
        Route::get('driver/vehicles/edit/{id}', [DriverVehiclesController::class, 'edit'])->name('driver_vehicles.edit');
        Route::post('driver/vehicles/update/{id}', [DriverVehiclesController::class, 'update'])->name('driver_vehicles.update');
        Route::get('driver/vehicles/delete/{id}', [DriverVehiclesController::class, 'destroy'])->name('driver_vehicles.delete');
        Route::post('/driver-vehicles/check-driver-assignment', [DriverVehiclesController::class, 'checkDriverAssignment'])->name('driver_vehicles.check_driver_assignment');
        Route::post('/driver-vehicles/check-vehicle-assignment', [DriverVehiclesController::class, 'checkVehicleAssignment'])->name('driver_vehicles.check_vehicle_assignment');
        Route::post('/driver-vehicles/check-vehicle-already-assignment', [DriverVehiclesController::class, 'checkVehicleAlreadyAssignment'])->name('driver_vehicles.check_vehicle_already_assignment');

        Route::get('drivers/all', [DriversController::class, 'index'])->name('drivers.all');
        Route::get('drivers/create', [DriversController::class, 'create'])->name('drivers.create');
        Route::post('drivers/store', [DriversController::class, 'store'])->name('driver.store');
        Route::get('drivers/edit/{id}', [DriversController::class, 'edit'])->name('driver.edit');
        Route::post('drivers/update/{id}', [DriversController::class, 'update'])->name('driver.update');
        Route::get('drivers/delete/{id}', [DriversController::class, 'delete'])->name('driver.delete');
        Route::get('drivers/track/{id}', [DriversController::class, 'track'])->name('driver.track');
        Route::get('/drivers/import', [DriversController::class, 'importForm'])->name('drivers.importform');
        Route::post('/drivers/import', [DriversController::class, 'import'])->name('drivers.import');

        Route::get('fleet', [CompanyController::class, 'fleet'])->name('fleet');
        Route::get('subscribe', [CompanyController::class, 'showPlans'])->name('subscribe');

        ////

      });
      Route::get('payment-methods', [PaymentMethodsController::class, 'index'])->name('payment-methods.all');
      Route::get('/add-payment-method', [PaymentMethodsController::class, 'addPaymentMethod'])->name('payment_method.add');
      Route::post('/store-payment-method', [PaymentMethodsController::class, 'storePaymentMethod'])->name('store-payment-method');
      Route::get('/set-default-payment-method/{id}', [PaymentMethodsController::class, 'setDefaultPaymentMethod'])->name('make_default');
      Route::get('payments', [PaymentController::class, 'allPayments'])->name('payments');
      Route::get('company/contactus/all', [CompanyController::class, 'contactUsForms'])->name('company.contactus');
      Route::get('company/contactform/read/{id}', [CompanyController::class, 'readForm'])->name('company.contactform.detail');
    }
  );
  Route::get('contactus', [CompanyController::class, 'contactus'])->name('contactus');
  Route::post('contactus', [CompanyController::class, 'submitContactUs'])->name('company.contactus.submit');
  ////


  Route::get('/checkout', [PaymentController::class, 'showCheckoutForm'])->name('checkout.form');
  Route::post('/checkout', [PaymentController::class, 'processPayment'])->name('checkout.process');

  Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
  Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');
  Route::get('purchase/{id}', [PaymentController::class, 'purchase'])->name('purchase');

  Route::post('pay', [PaymentController::class, 'subscribe'])->name('pay');

  Route::get('/cancel-subscription/{id}', [PaymentController::class, 'cancel'])->name('cancel.subscription');

  Route::post('/messages', [ContactUsController::class, 'store'])->name('messages.store');
  Route::get('/messages/{contact_id}', [ContactUsController::class, 'fetchMessages'])->name('messages.fetch');
  //Route::post('/messages/read/{id}', [ContactUsController::class, 'markAsRead'])->name('messages.markAsRead');


  Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
});
