<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'availabilities';

   protected $fillable = [
    'user_id',
    'available_date',
    'start_time',
    'end_time',
    'day',
    'status',
    'is_booked',
];
    protected $dates = ['available_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}