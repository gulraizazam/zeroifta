<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Imports\DriversImport;
use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class DriversController extends Controller
{
    public function index()
    {
        $drivers= CompanyDriver::with('driver','company')->where('company_id',Auth::id())->get();
        return view('company.drivers.index',get_defined_vars());
    }
    public function create()
    {
        return view('company.drivers.add');
    }
    public function store(Request $request)

    { 
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'dot' => 'required|string|max:255',
            'mc' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $driver = new User();
        $driver->name = $request->name;
        $driver->email = $request->email;
        $driver->phone	 = $request->phone;
        $driver->password= Hash::make($request->password);
        $driver->dot=$request->dot;
        $driver->role='driver';
        $driver->mc=$request->mc;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('drivers'), $imageName);
            $driver->driver_image= $imageName;
        }
        $driver->save();
        $companyDriver = new CompanyDriver();
        $companyDriver->driver_id =$driver->id;
        $companyDriver->company_id =Auth::id();
        $companyDriver->save();
        return redirect('drivers/all')->withSuccess('Driver Added Successfully');
    }
    public function edit($id)
    {
        $driver = User::find($id);
        return view('company.drivers.edit',get_defined_vars());
    }
    public function update(Request $request,$id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required',
            'phone' => 'required|string|max:20',
            'dot' => 'required|string|max:255',
            'mc' => 'required|string|max:255',
        ]);
        $driver = User::find($id);
        $driver->name = $request->name;
        $driver->email = $request->email;
        $driver->phone	 = $request->phone;
        $driver->dot=$request->dot;
        $driver->mc=$request->mc;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('drivers'), $imageName);
            $driver->driver_image= $imageName;
        }
        $driver->update();
        return redirect('drivers/all')->withSuccess('Driver Updated Successfully');
    }
    public function delete($id)
    {
        $driver = User::find($id);
        $findVehicle = CompanyDriver::where('driver_id',$id)->first();
        $findDVehicle = DriverVehicle::where('driver_id',$id)->first();
        if( $findVehicle){
            $findVehicle->delete();
        }
        if( $findDVehicle){
            $findDVehicle->delete();
        }
        $driver->delete();
        return redirect('drivers/all')->withError('Driver Deleted Successfully');
    }
    public function track($id)
    {
        return view('company.drivers.track', ['userId' => $id]);
    }
    public function importForm()
    {
       return view('company.drivers.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new DriversImport, $request->file('file'));

        return redirect('drivers/all')->with('success', 'Driver imported successfully.');
    }
}
