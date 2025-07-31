<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookServiceModel extends Model
{
    protected $table = 'book_service';


    protected $casts = [
        'service_completion_date' => 'datetime',
    ];

    static public function getBookService($b_id, $request)
    {
        $return = self::select('book_service.*')
                 ->where('book_service.user_id', '=', $b_id)
                 ->orderBy('book_service.id', 'asc');

                 $return  = $return->paginate(40);
                 return $return;
    }

    public function get_service_type_name()
    {
        return $this->belongsTo(ServiceTypeModel::class, 'service_type_id');
    }

    public function  get_category_name()
        {
            return $this->belongsTo(CategoryModel::class, 'category_id'); 
        }
    
    public function get_sub_category_name()
        {
            return $this->belongsTo(SubCategoryModel::class, 'sub_category_id'); 
        }

        public function get_amc_free_service()
        {
            return $this->belongsTo(AmcFreeServiceModel::class, 'amc_free_service_id');  
        }
}