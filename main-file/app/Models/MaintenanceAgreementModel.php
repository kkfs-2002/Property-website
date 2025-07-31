<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceAgreementModel extends Model
{
    use HasFactory;

    protected $table = 'maintenance_agreements';
    
    protected $fillable = [
        'agreement_number',
        'client_name',
        'service_type',
        'start_date',
        'status',
        'paid_status',
        'next_maintenance_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_maintenance_date' => 'date',
        'status' => 'integer',
        'paid_status' => 'integer'
    ];
static public function get_single($id)
    {
        return self::find($id);
    }
    static public function get_record($request)
    {
        $query = self::select('*')
                    ->where('is_delete', 0)
                    ->orderBy('id', 'desc');

        if (!empty($request->client_name)) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        return $query->paginate(20);
    }

    static public function get_record_delete()
    {
        return self::where('is_delete', 1)->get();
    }
}