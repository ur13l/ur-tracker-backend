<?php

namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Travel extends Eloquent
{
  protected $primaryKey = '_id';
  protected $connection = "mongodb";

  public function points () {
    return $this->hasMany('App\Point');
  }
}
