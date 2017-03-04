<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Customer extends Model
{
    
    protected $table = 'customers';
    protected $fillable = array('id', 'userID', 'email', 'salutation', 'firstName', 'lastName', 'middleName', 'address1', 'address2', 'state', 'city', 'zipcode', 'countryCode', 'contact', 'created_at', 'updated_at');
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

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            'required' => ':attribute is required',
            'unique' => ':attribute must be unique',
            'required'  => ':attribute is required',
            'numeric'  => ':attribute must be numeric',
        ];
    }

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = Validator::make($data, $rules, $this->messages());
        // return the result
        return $v;
    }
}
