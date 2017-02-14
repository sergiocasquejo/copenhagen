<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar';

    public function rooms() {
         return $this->hasMany('App\Room', 'roomID', 'id');
    }
}
