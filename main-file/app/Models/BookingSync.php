<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSync extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'source_type',
        'credentials',
        'sync_frequency',
        'custom_frequency',
        'last_sync_at',
        'last_sync_status',
        'is_active',
        'vendor_id'
    ];

    protected $casts = [
        'credentials' => 'array',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // Source types with labels
    public static function getSourceTypes(): array
    {
        return [
            'airbnb' => 'Airbnb',
            'booking_com' => 'Booking.com',
            'google_calendar' => 'Google Calendar',
            'outlook' => 'Outlook Calendar',
            'expedia' => 'Expedia'
        ];
    }

    // Frequency options
    public static function getFrequencyOptions(): array
    {
        return [
            'daily' => 'Daily',
            'hourly' => 'Hourly',
            'weekly' => 'Weekly',
            'manual' => 'Manual Only',
            'custom' => 'Custom'
        ];
    }
}