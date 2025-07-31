<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryModel extends Model
{
    use HasFactory;

    protected $table = 'sub_category';
    public $timestamps = false; 

    protected $fillable = [
        'category_id',
        'name',
    ];

    static public function get_record($request)
    {
        $return = self::select('sub_category.*', 'category.name as category_name')
               ->join('category', 'category.id', '=', 'sub_category.category_id')
                ->orderBy('sub_category.id', 'asc')
                ->where('sub_category.is_delete', '=', 0);

                $return =  $return->paginate(3);
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