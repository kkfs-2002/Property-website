<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    static public function get_record($request)
    {
        $return = self::select('users.*', 'vendor_type.name as vendor_type_name', 'category.name as category_name')
                 ->join('vendor_type', 'vendor_type.id', '=', 'users.vendor_type_id', 'left')
                 ->join('category', 'category.id', '=', 'users.category_id', 'left')
                 ->orderBy('users.id','desc')
                 ->where('users.is_admin', '=', 2)
                 ->where('users.is_delete', '=', 0);

                 //search start

                  if(!empty(Request::get('id')))
                  {
                    $return =  $return->where('users.id', '=', Request::get('id'));
                  }

                  if(!empty(Request::get('name')))
                  {
                    $return =  $return->where('users.name', 'like', '%'. Request::get('name'). '%');
                  }

                  
                  if(!empty(Request::get('last_name')))
                  {
                    $return =  $return->where('users.last_name', 'like', '%'. Request::get('last_name'). '%');
                  }

                  if(!empty(Request::get('email')))
                  {
                    $return =  $return->where('users.email', 'like', '%'. Request::get('email'). '%');
                  }


                  if(!empty(Request::get('mobile') ))
                  {
                    $return =  $return->where('users.mobile', 'like', '%'. Request::get('mobile'). '%');
                  }


                  //search end
                 $return =  $return->paginate(20);
                 return $return;
    }


    static public function get_single($id)
    {
        return self::find($id);
    }

    static public function get_record_user($request)
    {
      $return = self::select('users.*')
      ->orderBy('users.id','desc')
      ->where('users.is_admin', '=', 0)
      ->where('users.is_delete', '=', 0);

      //search start

      if(!empty(Request::get('id')))
      {
        $return =  $return->where('users.id', '=', Request::get('id'));
      }

      if(!empty(Request::get('name')))
      {
        $return =  $return->where('users.name', 'like', '%'. Request::get('name'). '%');
      }

      
      if(!empty(Request::get('last_name')))
      {
        $return =  $return->where('users.last_name', 'like', '%'. Request::get('last_name'). '%');
      }

      if(!empty(Request::get('email')))
      {
        $return =  $return->where('users.email', 'like', '%'. Request::get('email'). '%');
      }


      if(!empty(Request::get('mobile') ))
      {
        $return =  $return->where('users.mobile', 'like', '%'. Request::get('mobile'). '%');
      }


      //search end

      $return = $return->paginate(20);
      return $return;
    }

    public function getImage()
    {
    if (!empty($this->profile) && file_exists(public_path('upload/profile/' . $this->profile))) {
        return url('upload/profile/' . $this->profile);
    } else {
        return "";
    }
}

}
