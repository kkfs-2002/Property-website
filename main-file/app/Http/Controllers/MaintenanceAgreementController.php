<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceAgreementModel;

class MaintenanceAgreementController extends Controller
{
    public function maintenance_agreement_list(Request $request)
    {
        $data['getrecord'] = MaintenanceAgreementModel::get();
        return view('user.maintenance_agreement.list', $data); 
    }

    public function maintenance_agreement_add(Request $request)
    {
        return view('user.maintenance_agreement.add'); 
    }

    public function maintenance_agreement_store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'agreement_number' => 'required|unique:maintenance_agreements',
            'client_name' => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|in:0,1',
            'paid_status' => 'required|in:0,1', // Changed to match form input
            'next_maintenance_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Create a new maintenance agreement record
        $agreement = new MaintenanceAgreementModel();
        $agreement->agreement_number = $validated['agreement_number'];
        $agreement->client_name = $validated['client_name'];
        $agreement->service_type = $validated['service_type'];
        $agreement->start_date = $validated['start_date'];
        $agreement->status = $validated['status'];
        $agreement->paid_status = $validated['paid_status'];
        $agreement->next_maintenance_date = $validated['next_maintenance_date'];
        $agreement->created_at = now();
        $agreement->updated_at = now();
        $agreement->save();

        // Redirect with success message
        return redirect('user/maintenance_agreement/list')->with('success', 'Maintenance agreement created successfully!');
    }
        public function maintenance_agreement_edit($id)
    {
        $data['getrecord'] = MaintenanceAgreementModel::get_single($id);
        return view('user.maintenance_agreement.edit', $data);
    }

    public function maintenance_agreement_update($id, Request $request)
{
    $request->validate([
        'agreement_number' => 'required|string|max:255',
        'client_name' => 'required|string|max:255',
        'service_type' => 'required|string|max:255',
        'start_date' => 'required|date',
        'next_maintenance_date' => 'required|date|after_or_equal:start_date',
        'status' => 'required|in:0,1',
        'paid_status' => 'required|in:0,1',
    ]);

    $agreement = MaintenanceAgreementModel::get_single($id);

    if (!$agreement) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    $agreement->agreement_number = $request->agreement_number;
    $agreement->client_name = $request->client_name;
    $agreement->service_type = $request->service_type;
    $agreement->start_date = $request->start_date;
    $agreement->next_maintenance_date = $request->next_maintenance_date;
    $agreement->status = $request->status;
    $agreement->paid_status = $request->paid_status;
    $agreement->save();

    return redirect('user/maintenance_agreement/list')->with('success', 'Agreement updated successfully.');
}

public function maintenance_agreement_delete($id, Request $request)
{
    // Find the agreement record
    $agreement = MaintenanceAgreementModel::find($id);
    
    // If record doesn't exist
    if (!$agreement) {
        return redirect('user/maintenance_agreement/list')->with('error', 'The maintenance agreement could not be found!');
    }

    // Perform the deletion (soft delete)
    $agreement->delete();  // Using Laravel's soft delete if enabled
    // OR for hard delete: $agreement->forceDelete();

    // Redirect with success message
    return redirect('user/maintenance_agreement/list')->with('success', 'Maintenance agreement #'.$agreement->agreement_number.' has been permanently deleted!');
}
}

