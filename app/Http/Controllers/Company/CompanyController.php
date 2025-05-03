<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyContactUs;
use App\Models\CompanyDriver;
use App\Models\FcmToken;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function create()
    {
        return view('company.register');
    }
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|string|max:20',
            'password'=>'required|min:8|confirmed'
        ]);
        $company = new User();
        $company->name=$request->name;
        $company->email=$request->email;
        $company->password=Hash::make($request->password);
        $company->role="company";
        $company->register_type = 'user';
        $company->phone=$request->phone;
        $company->save();
        if($request->fcm_token){
            $fcm =    FcmToken::updateOrCreate(
                ['user_id' => $company->id],
                ['token' => $request->fcm_token]
            );
        }
        Auth::loginUsingId($company->id);
        return redirect()->route('subscription');
        //return redirect('login')->withSuccess('Account created successfully. Now you can login.');

    }

    public function contactus()
    {
        return view('company.contactus');
    }
    public function submitContactUs(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',

            'phone' => 'required|string|max:20',
            'description' => 'required',


        ]);
        $contact = new CompanyContactUs();
        $contact->subject = $request->subject;
        $contact->company_id = Auth::id();
        $contact->phone = $request->phone;
        $contact->description = $request->description;
        $contact->message = $request->description;
        $contact->save();
        return redirect('company/contactus/all')->withSuccess('Information Submitted Successfully');
    }
    public function showPlans()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
        $availableFeatures = [
            'vehicles.all' => 'View All Vehicles',
            'vehicles.create' => 'Create Vehicle',
            'vehicles.edit' => 'Edit Vehicle',
            'vehicles.delete' => 'Delete Vehicle',
            'vehicles.import' => 'Import Vehicles',
            'driver_vehicles.index' => 'View Driver Vehicles',
            'driver_vehicles.create' => 'Assign Vehicles to Drivers',
            'driver_vehicles.edit' => 'Edit Driver Vehicle Assignment',
            'driver_vehicles.delete' => 'Remove Driver Vehicle Assignment',
            'drivers.all' => 'View All Drivers',
            'drivers.create' => 'Create Driver',
            'drivers.edit' => 'Edit Driver',
            'drivers.delete' => 'Delete Driver',
            'drivers.import' => 'Import Drivers',
            'drivers.track' => 'Track Drivers',
            'fleet.view' => 'View Fleet Management',
        ];

        return view('subscription', compact('plans', 'availableFeatures'));
    }
    public function contactUsForms()
    {
        $drivers = CompanyDriver::where('company_id',Auth::id())->pluck('driver_id')->toArray();
        $forms = CompanyContactUs::with('company')->whereIn('company_id',$drivers)->orderBy('company_contact_us.id','desc')->get();

        //$forms = CompanyContactUs::with('company')->where('company_id',Auth::id())->orderBy('company_contact_us.id','desc')->get();

        return view('company.contactus.index',get_defined_vars());
    }

    public function readForm($id)
    {
        $form = CompanyContactUs::with('company')->find($id);
        return view('company.contactus.read',get_defined_vars());
    }
    public function fleet()
    {
        $drivers = CompanyDriver::with('driver', 'company')
        ->where('company_id', Auth::id())
        ->get();

        // Fetch trips for all drivers in the company
        foreach ($drivers as $driver) {
            $driver->trips = Trip::where('user_id', $driver->driver->id)->where('status','active')->first();
        }
        //dd($drivers);
        return view('company.fleet',compact('drivers'));
    }

}
