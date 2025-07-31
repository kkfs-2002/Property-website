<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notifications_list()
    {
        $notifications = Notification::where('vendor_id', Auth::id())
                                     ->orderBy('remind_at', 'asc')
                                     ->get();

        return view('vendor.notifications.list', compact('notifications'));
    }

    public function notifications_create()
    {
        return view('vendor.notifications.create');
    }

    public function notifications_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string',
            'remind_at' => 'required|date|after_or_equal:now',
        ]);

        Notification::create([
            'vendor_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'remind_at' => $request->remind_at,
        ]);

        return redirect('vendor/notifications/list')->with('success', 'Notification created successfully.');
    }
public function notifications_delete($id)
{
    $notification = Notification::where('id', $id)
        ->where('vendor_id', Auth::id())
        ->firstOrFail();

    $notification->delete();

    return redirect()->back()->with('success', 'Notification deleted.');
}
}
