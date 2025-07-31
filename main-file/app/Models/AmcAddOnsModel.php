<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmcAddOnsModel extends Model
{
    use HasFactory;

    protected $table = 'amc_add_ons';
    protected $fillable = ['amc_id', 'name', 'price'];
    public $timestamps = false;

    static public function get_single($id){
        return self::find($id);
    }

    static public function get_add_ons($id){
        $return = self::select('amc_add_ons.*')
        ->where('amc_id', '=', $id )
        ->orderBy('id', 'asc');

        $return = $return->paginate(20);

        return $return;
    }
}
