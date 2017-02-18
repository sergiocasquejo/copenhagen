<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar';
    protected $fillable = array('roomID', 'selectedDate', 'roomOnly', 'singlePrice', 'doublePrice', 'roomOnlyPrice', 'minStay', 'maxStay', 'availability', 'isActive', 'onCreateSetup', 'created_at', 'updated_at');
    
    public $rules = array(
        'roomID' => 'required',
        'selectedDate' => 'required',
        'minimumPrice' => 'required|numeric',
        'availability' => 'required|numeric'
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


    public function rooms() {
         return $this->hasMany('App\Room', 'roomID', 'id');
    }

    public function rates() {
        return $this->belongsToMany('App\Rate', 'calendar_rates', 'calendarID', 'rateID');
    }
}
