<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
 use Illuminate\Auth\Authenticatable;
 use Illuminate\Auth\Passwords\CanResetPassword;
 use Illuminate\Foundation\Auth\Access\Authorizable;
 use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
 use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Eloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $primaryKey = '_id';
     protected $connection = "mongodb";

    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'travels'
    ];

    public function setPasswordAttribute($password)
   {
       $this->attributes['password'] = bcrypt($password);
   }

   public function save(array $options = array())
   {
       if(empty($this->api_token)) {
           $this->api_token = str_random(60);
       }
       return parent::save($options);
   }


   public function travels() {
     return $this->embedsMany('App\Travel');
   }
}
