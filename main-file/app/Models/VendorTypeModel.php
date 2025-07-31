<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorTypeModel extends Model
{
    protected $table = 'vendor_type';
    public $timestamps = false;  

    static public function get_record($request)
    {
        $return = self::select('vendor_type.*')
                    ->orderBy('vendor_type.id','asc')
                    ->where('is_delete', '=', 0);

                    $return =  $return->paginate(20);
          return $return;          
    }

    static public function get_single($id)
    {
        return self::find($id);
    }
}