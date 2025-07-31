<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use Auth;

class AvailabilityController extends Controller
{
    public function availability_list()
    {
        $data['getrecord'] = Availability::where('user_id', Auth::id())
                              ->orderBy('available_date', 'desc')
                              ->orderBy('start_time', 'asc')
                              ->get();

        return view('vendor.availability.list', $data);
    }

    public function availability_add()
    {
        return view('vendor.availability.add');
    }

    public function availability_store(Request $request)
    {
        $request->validate([
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'day' => 'required',
            'is_booked' => 'required|boolean',
        ]);

        // Check for overlapping slots
        $overlapping = Availability::where('user_id', Auth::id())
            ->where('available_date', $request->available_date)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time' => 'This slot overlaps with an existing slot'])->withInput();
        }

        $availability = new Availability();
        $availability->user_id = Auth::id();
        $availability->available_date = $request->available_date;
        $availability->start_time = $request->start_time;
        $availability->end_time = $request->end_time;
        $availability->day = $request->day;
        $availability->status = $request->status ?? 1;
        $availability->is_booked = (bool)$request->is_booked;
        $availability->save();

        return redirect('vendor/availability/list')->with('success', 'Availability added successfully.');
    }

    public function availability_edit($id)
    {
        $getrecord = Availability::where('user_id', Auth::id())->findOrFail($id);
        return view('vendor.availability.edit', compact('getrecord'));
    }

    public function availability_update(Request $request, $id)
    {
        $request->validate([
            'available_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'day' => 'required',
            'is_booked' => 'required|boolean',
        ]);

        $data = Availability::where('user_id', Auth::id())->findOrFail($id);
        $data->available_date = $request->available_date;
        $data->day = $request->day;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->status = $request->status ?? 1;
        $data->is_booked = (bool)$request->is_booked;
        $data->save();

        return redirect('vendor/availability/list')->with('success', 'Availability updated successfully!');
    }

   public function availability_delete($id)
{
    $availability = Availability::where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $availability->delete();

    return redirect('vendor/availability/list')->with('success', 'Availability successfully deleted.');
}
}