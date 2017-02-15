<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar';
    protected $fillable = array('roomID', 'selectedDate', 'roomOnly', 'singlePrice', 'doublePrice', 'roomOnlyPrice', 'minStay', 'maxStay', 'availability', 'isActive', 'onCreateSetup', 'created_at', 'updated_at');
    public function rooms() {
         return $this->hasMany('App\Room', 'roomID', 'id');
    }
}
