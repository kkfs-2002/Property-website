<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\VendorTypeModel;
use App\Models\CategoryModel;
use App\Models\User;
use Str;
use Auth;
use Mail;
use Hash;
use App\Mail\VendorRegisterMail;
use App\Http\Requests\ResetPassword;

class VendorController extends Controller
{
 public function vendor_list(Request $request)
 {
   $data['getrecord'] = User::get_record($request);
   return view('admin.vendor.list',  $data);
 }

 public function vendor_add(Request $request)
 {
    $data['getVendorType'] = VendorTypeModel::get_record($request);
    $data['getCategory'] = CategoryModel::get_record($request);
    return view('admin.vendor.add', $data);
 }

 public function vendor_store(Request $request)
 {
     //dd($request->all());
      
         $validatedData = $request->validate([
             'name' => 'required',
             'email' => 'required|email|unique:users',
             'status' => 'required',
             'category_id' => 'required',
             'vendor_type_id' => 'required'
         ]);
     
         
         $user = new User;
         $user->name            = trim($request->name);
         $user->last_name       = trim($request->last_name ?? '');
         $user->email           = trim($request->email);
         $user->category_id     = trim($request->category_id);
         $user->vendor_type_id  = trim($request->vendor_type_id); 
         $user->company_name    = trim($request->company_name ?? '');
         $user->employee_id     = trim($request->employee_id ?? '');
         $user->mobile          = trim($request->mobile  ?? '');

         if(!empty($request->file('profile')))
         {
             $file      =$request->file('profile');
             $randomStr =Str::random(30);
             $filename  =$randomStr . '.' .$file->getClientOriginalExtension();
             $file->move('upload/profile/', $filename);
             $user->profile = $filename;
     
         }

         $user->status          = trim($request->status);
         $user->always_assign   = trim($request->always_assign ?? 0);
         $user->is_admin        = 2;
         $user->user_type       = 'vendor';
         $user->remember_token  = Str::random(50); 
         $user->forgot_token    = Str::random(50);
         $user->password        = bcrypt('password'); 
         $user->save();
     
         $this->send_vendor_verification_mail($user);

         return redirect('admin/vendor/list')->with('success', 'Record successfully created.');
     }

     public function send_vendor_verification_mail($user)
{
    try {
        Mail::to($user->email)->send(new VendorRegisterMail($user));
    } catch (\Exception $e) {
        dd('Mail send error: ' . $e->getMessage());
    }
}

public function vendor_password(Request $request,$token)
{
  $user = User::where('forgot_token', '=', $token);

  if($user->count() == 0){
    abort(403);
  }

  $user =  $user->first();

  $data['token'] = $token;

  return view('admin.vendor.password', $data);
}

public function vendor_password_post($token, ResetPassword $request)
{
    $user = User::where('forgot_token', '=', $token);

    if ($user->count() == 0) {
        abort(403);
    }
    $user = $user->first();
    $user->remember_token = Str::random(50);
    $user->forgot_token = Str::random(50);
    $user->password = Hash::make($request->password);  // Hash password before saving
    $user->status = 0;
    $user->save();

    return redirect('/')->with('success', 'Password has been updated successfully.');
}

public function vendor_edit($id, Request $request)
{
    $data['getrecord'] = User::get_single($id);
    $data['getVendorType'] = VendorTypeModel::get_record($request);
    $data['getCategory'] = CategoryModel::get_record($request);
    return view('admin.vendor.edit', $data);
}

public function vendor_update($id, Request $request)
{
    // Validate data
    $validatedData = $request->validate([
        'name'             => 'required',
        'email'            => 'required|email|unique:users,email,' . $id,
        'status'           => 'required',
        'category_id'      => 'required',
        'vendor_type_id'   => 'required',
    ]);

    // Get vendor record
    $vendor = User::get_single($id);
    $vendor->name = trim($request->name);
    $vendor->last_name = trim($request->last_name);
    $vendor->email = trim($request->email);
    $vendor->mobile = trim($request->mobile);

    if(!empty($request->file('profile')))
    {
         if(!empty($insert_r->profile) && file_exists('upload/profile/'.$insert_r->profile))
         {
            unlink('upload/profile/'.$insert_r->profile);
         }

        $file      =$request->file('profile');
        $randomStr =Str::random(30);
        $filename  =$randomStr . '.' .$file->getClientOriginalExtension();
        $file->move('upload/profile/', $filename);
        $vendor->profile = $filename;

    }

    // Common fields
    $vendor->category_id = $request->category_id;
    $vendor->vendor_type_id = $request->vendor_type_id;

    // Vendor type logic
    if ($request->vendor_type_id == 1) {
        $vendor->company_name = trim($request->company_name);
        $vendor->employee_id = null;
    } elseif ($request->vendor_type_id == 2) {
        $vendor->company_name = null;
        $vendor->employee_id = null;
    } elseif ($request->vendor_type_id == 3) {
        $vendor->employee_id = trim($request->employee_id);
        $vendor->company_name = null;
    }

    $vendor->status = trim($request->status);
    $vendor->always_assign = trim($request->always_assign);

   
    $vendor->save();

    return redirect('admin/vendor/list')->with('success', 'Record successfully updated.');
}

public function vendor_delete($id, Request $request)
{
   $user = User::get_single($id);
  $user->is_delete = 1;
  $user->save();

  return redirect('admin/vendor/list')->with('error', 'Record successfully delete.');

}

// Add this method to your VendorController.php
public function download_vendor_pdf(Request $request)
{
    $vendors = User::get_record($request);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.vendor.report', compact('vendors'));
    return $pdf->download('vendor-report-'.date('Y-m-d').'.pdf');
}
}