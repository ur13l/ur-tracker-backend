<?php

namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Point extends Eloquent
{
  protected $primaryKey = '_id';
  protected $connection = "mongodb";
}
