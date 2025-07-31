<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AMCModel extends Model
{
    protected $table = 'amc_models';
    public $timestamps = false;  

    static public function get_record($request)
    {
        $return = self::select('amc_models.*')
                    ->orderBy('id','desc')
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

    public function option() {
       return $this->hasMany(AmcFreeServiceModel::class, 'amc_id');
    }
}