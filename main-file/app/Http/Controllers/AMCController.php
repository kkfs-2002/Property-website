<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AMCModel;
use App\Models\AmcAddOnsModel;
use App\Models\AmcFreeServiceModel;
use Hash;
use Str;
use Auth;
use PDF;

class AMCController extends Controller
{
    public function amc_list(Request $request)
    {
        $data['getrecord'] = AMCModel::get_record($request);
        return view('admin.amc.list', $data);
    }

    public function amc_add(Request $request)
    {
        return view('admin.amc.add');
    }

    public function amc_insert(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:amc_models,name',
            'amount' => 'required|numeric',
            'series' => 'required',
            'business_category' => 'nullable|in:0,1'
        ]);
    
        $amc = new AMCModel;
        $amc->name = trim($validated['name']);
        $amc->amount = trim($validated['amount']);
        $amc->business_category = $request->business_category ?? null;
        $amc->series = trim($validated['series']);
        
        try {
            $amc->save();
        } catch (\Exception $e) {
            dd("DB Error: " . $e->getMessage()); 
        }
    
        return redirect('admin/amc/list')->with('success', 'AMC has been successfully added.');
    }

    public function amc_edit($id, Request $request)
    {
        $data['getrecord'] = AMCModel::get_single($id);
        return view('admin.amc.edit', $data);
    }

    public function amc_update($id, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:amc_models,name,'.$id,
            'amount' => 'required|numeric',
            'series' => 'required',
            'business_category' => 'nullable|in:0,1'
        ]);
    
        $amc = AMCModel::get_single($id);
        $amc->name = trim($validated['name']);
        $amc->amount = trim($validated['amount']);
        $amc->business_category = $request->business_category ?? null;
        $amc->series = trim($validated['series']);
        
        try {
            $amc->save();
        } catch (\Exception $e) {
            dd("DB Error: " . $e->getMessage()); 
        }
    
        return redirect('admin/amc/list')->with('success', 'AMC has been successfully Update.');
    }

    public function amc_delete($id, Request $request)
    {
        $delete_record = AMCModel::get_single($id);
        $delete_record->is_delete = 1;
        $delete_record->save();
        return redirect()->back()->with('error','Record successfully Deleted!');
    }

    public function amc_add_ons_list($id, Request $request)
    {
        $data['getrecord'] = AMCModel::get_single($id);
        $data['get_add_ons'] = AmcAddOnsModel::get_add_ons($id);
        return view('admin.amc.add_ons_list', $data);
    }

    public function amc_add_add_ons($id, Request $request)
    {
        $data['getrecord'] = AMCModel::get_single($id);
        return view('admin.amc.add_ons_add', $data);
    }

    public function show_add_ons_form($id) {
        $getrecord = AMCModel::findOrFail($id);
        return view('admin.amc.add_ons_add', compact('getrecord'));
    }

    public function amc_store_add_ons(Request $request)
    {
        $request->validate([
            'amc_id' => 'required',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);
    
        $addon = new AmcAddOnsModel();
        $addon->amc_id = trim($request->amc_id);
        $addon->name = trim($request->name);
        $addon->price = $request->price ?? 0;
        $addon->save();
    
        return redirect('admin/amc/add_ons/'.$request->amc_id)->with('success', 'Record Successfully Created');
    }

    public function amc_edit_add_ons($id, Request $request)
    {
        $data['getrecord'] = AmcAddOnsModel::get_single($id);
        return view('admin.amc.add_ons_edit', $data);
    }

    public function amc_edit_add_ons_update($id, Request $request)
    {
        $update = AmcAddOnsModel::get_single($id);
        $update->name = trim($request->name);
        $update->price = !empty($request->price) ? $request->price : '0';
        $update->save();

        return redirect('admin/amc/add_ons/' . $update->amc_id)
               ->with('success', 'Record Successfully Updated');
    }

    public function delete_add_ons($id, Request $request)
    {
        $delete_record = AmcAddOnsModel::get_single($id);
        $delete_record->delete();
        return redirect()->back()->with('error', 'Record Successfully deleted');
    }

    public function amc_free_service($id, Request $request)
    {
        $data['getrecord'] = AMCModel::get_single($id);
        $data['get_free_service'] = AmcFreeServiceModel::where('amc_id', $id)->paginate(10);
        return view('admin.amc.free_service_list', $data);
    }

    public function amc_add_free_service($id)
    {
        $data['getrecord'] = AMCModel::get_single($id);
        return view('admin.amc.free_service_add', $data);
    }

    public function amc_store_free_service(Request $request)
    {
        $request->validate([
            'amc_id' => 'required',
            'name' => 'required',
            'limit' => 'required',
            'price' => 'required',
        ]);
            
        $insert_r = new AmcFreeServiceModel;
        $insert_r->amc_id = trim($request->amc_id);
        $insert_r->name = trim($request->name);
        $insert_r->limit = trim($request->limit);
        $insert_r->price = trim($request->price);
        $insert_r->save();

        return redirect('admin/amc/free_service/'.$request->amc_id)->with('success', 'AMC Free Service Successfully saved');
    }

    public function amc_edit_free_service($id)
    {
        $data['getrecord'] = AMCFreeServiceModel::get_single($id);
        return view('admin.amc.free_service_edit', $data);
    }

    public function amc_update_free_service($id, Request $request)
    {
        $update = AMCFreeServiceModel::get_single($id);
        $update->name = trim($request->name);
        $update->limit = trim($request->limit);
        $update->price = trim($request->price);
        $update->save();

        return redirect('admin/amc/free_service/'.$update->amc_id)->with('success', ' Free Service Update Successfully');
    }

    public function amc_delete_free_service($id, Request $request)
    {
        $delete_record =  AMCFreeServiceModel::get_single($id);
        $delete_record->delete();

        return redirect()->back()->with('error', 'Record Successfully deleted');
    }

    public function amc_report()
{
    $data['getrecord'] = AMCModel::orderBy('id', 'desc')->get(); // Adjust with your model
    $data['header'] = 'Annual Maintenance Contract Report';
    $data['date'] = date('Y-m-d');
    
    $pdf = PDF::loadView('admin.amc.report', $data);
    return $pdf->download('AMC_Report_'.date('Y-m-d').'.pdf');
}
}