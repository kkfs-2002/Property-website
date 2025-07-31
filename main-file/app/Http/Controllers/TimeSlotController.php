<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceTypeModel;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeSlotController extends Controller
{
    public function time_slots_list()
    {
        $vendor_id = Auth::id();
        $timeSlots = TimeSlot::where('vendor_id', $vendor_id)
                           ->with('service')
                           ->orderBy('day_of_week')
                           ->orderBy('start_time')
                           ->get();
        
        // Convert days to a more readable format
        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $groupedSlots = $timeSlots->groupBy('day_of_week')
                                ->sortBy(function($item, $key) use ($daysOrder) {
                                    return array_search($key, $daysOrder);
                                });
        
        return view('vendor.time_slots.list', compact('timeSlots', 'groupedSlots'));
    }

    public function time_slots_create()
    {
        $vendor_id = Auth::id();
        $serviceTypes = ServiceTypeModel::where('is_delete', 0)->get();
        return view('vendor.time_slots.create', compact('serviceTypes'));
    }

    public function time_slots_store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:service_types,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Convert times to Carbon for easier comparison
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i', $request->end_time);

        // Check for overlapping slots
        $overlappingSlot = TimeSlot::where('vendor_id', Auth::id())
            ->where('day_of_week', $request->day_of_week)
            ->where('service_id', $request->service_id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime->format('H:i:s'))
                      ->where('end_time', '>', $startTime->format('H:i:s'));
                });
            })
            ->exists();

        if ($overlappingSlot) {
            return back()->withErrors(['time' => 'This time slot overlaps with an existing slot for the same service.'])->withInput();
        }

        TimeSlot::create([
            'vendor_id' => Auth::id(),
            'service_id' => $request->service_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 1 // Active
        ]);

        return redirect('vendor/time_slots/list')
                         ->with('success', 'Time slot added successfully.');
    }

    public function time_slots_delete($id)
    {
        $slot = TimeSlot::where('vendor_id', Auth::id())->findOrFail($id);
        $slot->delete();

        return redirect('vendor/time_slots/list')
                         ->with('error', 'Time slot deleted successfully.');
    }
}