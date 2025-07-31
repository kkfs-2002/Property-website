<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\VendorTypeModel;

class VendorTypeController extends Controller
{

public function Vendor_type_list(Request $request)
{
    $data['getrecord'] = VendorTypeModel::get_record($request);
    return view('admin.Vendor_type.list', $data);
}

public function Vendor_type_add(Request $request)
{
    return view('admin.Vendor_type.add');
}

public function Vendor_type_store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|unique:Vendor_type',
        
    ]);
    
        $save = new VendorTypeModel;
        $save->name = trim($request->name);
        $save->save();
    
        return redirect('admin/Vendor_type/list')->with('success', 'Record successfully Create.');
}

public function Vendor_type_edit($id, Request $request)
{
  $data['getrecord'] = VendorTypeModel ::get_single($id);
  return view('admin.Vendor_type.edit', $data);
}

public function Vendor_type_update(Request $request, $id)
{
     
   $save = request()->validate([
    'name' => 'required|unique:Vendor_type,name,'. $id, 
   ]);
   

   



    $save = VendorTypeModel::get_single($id); 
    $save->name = trim($request->name);
    $save->save(); 

   
    return redirect('admin/Vendor_type/list')->with('success', 'Record successfully update.');
}

public function Vendor_type_delete($id, Request $request)
{
    $delete = VendorTypeModel::get_single($id);
  $delete->is_delete = 1;
  $delete->save();

  return redirect('admin/Vendor_type/list')->with('success', 'Record successfully delete.');

}
}