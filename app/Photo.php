<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'rooms_photos';
    public $timestamps = false;
}
