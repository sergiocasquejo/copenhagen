<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $table = 'bed';
    public $timestamps = false;
    protected $fillable = [
        'roomID', 'qty', 'type'
    ];

    public function room()
    {
        return $this->belongsTo('App\Room', 'roomID', 'id');
    }
}
