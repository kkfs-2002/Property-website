<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AMCModel;
use App\Models\User;
use Mail;
use App\Mail\ProfileRegisterMail;
use Str;
use File;

class VendorProfileController extends Controller
{
 public function vprofile_list(Request $request)
 {
     $data['getrecode'] = User::get_record_user($request);
    return view('vendor.vprofile.list', $data);
 }

 public function vprofile_add (Request $request)
 {
    $data['getAMC'] = AMCModel::get_record_delete();
    return view('vendor.vprofile.add',$data);
 }

 public function vprofile_store(Request $request)
 {
    //dd($request->all());
    $user = request()->validate([
       'name' => 'required',
       'email' => 'required|unique:users',
       'amc_id' => 'required',
       'mobile' => 'required',

    ]);

     $DataAmc = AMCModel::where('id', '=', $request->amc_id)->first();

     $UserDesc = User::orderBy('id', 'DESC')->where('amc_id', '=', $request->amc_id)->first();


    $user = new User;
    $user->name        = trim($request->name);
    $user->last_name   = trim($request->last_name);
    $user->email       = trim($request->email);
    $user->mobile      = trim($request->mobile);
    $user->address     = trim($request->address );
    $user->amc_business_category_name = trim($request->amc_business_category_name);

    if(!empty($request->file('profile')))
    {
        $file      =$request->file('profile');
        $randomStr =Str::random(30);
        $filename  =$randomStr . '.' .$file->getClientOriginalExtension();
        $file->move('upload/profile/', $filename);
        $user->profile = $filename;

    }
      
    if(!empty($UserDesc))
    {
        $user->account_number = (int)$UserDesc->account_number + 1;
    }else{
        $user->account_number = trim( $DataAmc->series);
    }

    $user->amc_id = trim($request->amc_id);
    $user->is_admin = 0;
    $user->user_type = 'user';
    $user->remember_token = Str::random(50);
    $user->forgot_token = Str::random(50);
    $user->save();

}
 public function vprofile_edit($id, Request $request)
 {
   $data['getrecord'] = User::get_single($id);
   $data['getAMC'] = AMCModel::get_record_delete();
   return view('vendor.vprofile.edit', $data);
 }

 public function vprofile_update(Request $request, $id)
 {
    $insert_r = $request->validate([
       'name'    => 'required',
        'email'  => 'required|unique:users,email,' . $id,
    ]);

    $insert_r = User::get_single($id);
    $insert_r->name = trim($request->name);
    $insert_r->last_name = trim($request->last_name);
    $insert_r->email = trim($request->email);
    $insert_r->mobile = trim($request->mobile);
  
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
        $insert_r->profile = $filename;

    }
    $insert_r->address = trim($request->address);
    $insert_r->save();

    return redirect('vendor/vprofile/list')->with('success', 'User successfully Update.');
 }

 public function vprofile_delete($id)
 {
   $user = User::get_single($id);
  $user->is_delete = 1;
  $user->save();

  return redirect('vendor/vprofile/list')->with('error', 'Record successfully delete.');

}
}