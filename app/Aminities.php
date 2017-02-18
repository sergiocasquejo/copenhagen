<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aminities extends Model
{
    protected $table = 'aminities';
    public $timestamps = false;

    public function rooms() {
         return $this->belongsToMany('App\Room', 'rooms_aminities');
    }

    public function getNameAttribute($value) {
        return strtolower($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }
}
