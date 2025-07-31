<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTypeModel extends Model
{
    protected $table = 'service_types';
    public $timestamps = false;  

    static public function get_record($request)
    {
        $return = self::select('service_types.*')
                    ->orderBy('service_types.id','asc')
                    ->where('is_delete', '=', 0);

                    $return =  $return->paginate(20);
          return $return;          
    }

    static public function get_single($id)
    {
        return self::find($id);
    }

    static public function get_record_delete()
    {
        return self::where('is_delete', 0)->get();
    }
}