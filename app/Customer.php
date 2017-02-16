<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
    public function customer()
    {
        return $this->hasOne('App\User', 'userID', 'id');
    }
}
