<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class DisableDate extends Model
{
    protected $table = 'disabled_dates';
    public $timestamps = false;
    protected $fillable = array('selected_date', 'created_at');
    
    public $rules = array(
        'selected_date' => 'date_format:Y-m-d|required|unique:disabled_dates',
    );

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [
            "unique" => ':attribute is already exists',
            "date_format" => ':attribute is invalid format',
            'required' => ':attribute is required',
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

    public static function getFutureDisabledDates() {
        return \App\DisableDate::where("selected_date", ">=", date('Y-m-d'))->groupBy('selected_date')->pluck('selected_date');
    }


 
}
