<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Customer extends Model
{
    
    protected $table = 'customers';
    protected $fillable = array('email');
    public function bookings()
    {
        return $this->hasMany('App\Booking', 'customerID', 'id');
    }

    public $rules = array(
        'salutation' => 'required',
        'firstname' => 'required',
        'middleName' => 'required',
        'lastname' => 'required',
        'email' => 'required|unique:customers,email',
        'address1' => 'required',
        'address2' => 'required',
        'city' => 'required',
        'state' => 'required',
        'zipcode' => 'required',
        'country' => 'required',
        'contact' => 'required',
    );

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = Validator::make($data, $rules);
        // return the result
        return $v;
    }
}
