<?php

namespace App\Http\Controllers;

use App\Models\BookingSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingSyncController extends Controller
{
   public function booking_sync_list(Request $request)
{
    $vendorId = Auth::id();

    $query = BookingSync::where('vendor_id', $vendorId);

    // Apply filters
    if ($request->has('status') && $request->status === 'active') {
        $query->where('is_active', true);
    }

    if ($request->has('source') && !empty($request->source)) {
        $query->where('source_type', $request->source);
    }

    // Paginate with filter query parameters appended
    $syncs = $query->latest()->paginate(10)->appends($request->query());

    // Stats for dashboard cards (unfiltered)
    $stats = [
        'total' => BookingSync::where('vendor_id', $vendorId)->count(),
        'active' => BookingSync::where('vendor_id', $vendorId)->where('is_active', true)->count(),
        'google' => BookingSync::where('vendor_id', $vendorId)->where('source_type', 'google_calendar')->count(),
        'outlook' => BookingSync::where('vendor_id', $vendorId)->where('source_type', 'outlook')->count(),
        'successful' => BookingSync::where('vendor_id', $vendorId)
                            ->where('last_sync_status', 'like', 'Success%')
                            ->count(),
    ];

    return view('vendor.booking_sync.list', compact('syncs', 'stats'));
}


    public function booking_sync_create()
    {
        return view('vendor/booking_sync/create', [
            'sourceTypes' => BookingSync::getSourceTypes()
        ]);
    }

    public function booking_sync_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'source_type' => ['required', Rule::in(array_keys(BookingSync::getSourceTypes()))],
            'credentials' => 'required|array',
            'sync_frequency' => ['required', Rule::in(array_keys(BookingSync::getFrequencyOptions()))],
            'custom_frequency' => 'nullable|integer|min:5|required_if:sync_frequency,custom',
            'is_active' => 'sometimes|boolean'
        ]);

        $bookingSync = BookingSync::create([
            'vendor_id' => Auth::id(),
            'name' => $validated['name'],
            'source_type' => $validated['source_type'],
            'credentials' => $validated['credentials'],
            'sync_frequency' => $validated['sync_frequency'],
            'custom_frequency' => $validated['sync_frequency'] === 'custom' ? $validated['custom_frequency'] : null,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect('vendor/booking_sync/list')
    ->with('success', 'Booking sync configuration updated successfully.');
    }

    public function booking_sync_edit(BookingSync $bookingSync)
    {
        $this->authorize('update', $bookingSync);

        return view('vendor.booking_sync.edit', [
            'bookingSync' => $bookingSync,
            'sourceTypes' => BookingSync::getSourceTypes()
        ]);
    }

 

   public function booking_sync_delete($id)
{
    $bookingSync = BookingSync::where('id', $id)
        ->where('vendor_id', Auth::id())
        ->firstOrFail();

    $bookingSync->delete();

    return redirect('vendor/booking_sync/list')
        ->with('success', 'Booking sync configuration deleted successfully.');
}
    public function credentials_form(Request $request)
    {
        $sourceType = $request->input('source_type');
        $credentials = $request->input('credentials', []);

        return view('vendor/booking_sync/partials/credentials', [
            'sourceType' => $sourceType,
            'credentials' => $credentials
        ]);
    }
}