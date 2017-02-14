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
}
