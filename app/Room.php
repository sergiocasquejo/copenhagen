<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
     protected $table = 'rooms';

     public function photos() {
         return $this->hasMany('App\Photo', 'roomID', 'id');
     }
}
