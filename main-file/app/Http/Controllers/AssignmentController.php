<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function assignments_list()
    {
        $vendorId = Auth::id();
        $assignments = Assignment::where('vendor_id', $vendorId)->get();
        return view('vendor.assignments.list', compact('assignments'));
    }

    public function assignments_create()
    {
        return view('vendor.assignments.create');
    }

    public function assignments_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 1,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'vendor_id' => Auth::id(),
        ]);

        return redirect('vendor/assignments/list')->with('success', 'Assignment created successfully.');
    }

    public function assignments_show($id)
    {
        $vendorId = Auth::id();
        $assignment = Assignment::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        return view('vendor.assignments.show', compact('assignment'));
    }

    public function assignments_edit($id)
    {

    $vendorId = Auth::id();
    $getrecord = Assignment::where('id', $id)
        ->where('vendor_id', $vendorId)
        ->firstOrFail();

    return view('vendor/assignments/edit', compact('getrecord'));
}
    

   public function assignments_update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    try {
        $vendorId = Auth::id();
        $assignment = Assignment::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 1,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect('vendor/assignments/list')->with('success', 'Assignment updated successfully.');
        
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error updating assignment: ' . $e->getMessage());
    }
}
    public function assignments_delete($id)
    {
        $vendorId = Auth::id();
        $assignment = Assignment::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        $assignment->delete();

        return redirect('vendor/assignments/list')->with('success', 'Assignment deleted successfully.');
    }
}