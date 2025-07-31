<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function appointment_list()
    {
        $data['getrecord'] = Appointment::where('user_id', Auth::id())
            ->orderBy('appointment_date', 'asc')
            ->get();
            return view('vendor.appointments.list', $data);
    }

    public function appointment_add()
    {
        return view('vendor.appointments.add');
    }

    public function appointment_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 1,
        ]);

        return redirect('vendor/appointments/list')->with('success', 'Appointment successfully created.');
    }

    public function appointment_edit($id)
    {
        $data['getrecord'] = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('vendor.appointments.edit', $data);
    }

    public function appointment_update($id, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $appointment->update([
            'title' => $request->title,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return redirect('vendor/appointments/list')->with('success', 'Appointment successfully updated.');
    }

    public function appointment_delete($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $appointment->delete();

        return redirect('vendor/appointments/list')->with('success', 'Appointment successfully deleted.');
    }
}
