<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Rate extends Model
{
	protected $table = 'rates';
	public $timestamps = false;
	public function rooms() {
		return $this->belongsToMany('App\Room', 'room_rates', 'roomID', 'rateID');
	}
	
	public function calendar() {
		return $this->belongsToMany('App\Calendar', 'calendar_rates', 'calendarID', 'rateID');
	}

	public $rules = array(
        'name' => 'required|unique:rates,name',
        'rateCode' => 'required|unique:rates,rateCode',
        'roomCode' => 'required',
        'mealType' => 'required'
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
