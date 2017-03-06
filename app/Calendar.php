<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Calendar extends Model
{
    protected $table = 'calendar';
    protected $fillable = array('roomID', 'selectedDate', 'roomOnly', 'singlePrice', 'doublePrice', 'roomOnlyPrice', 'minStay', 'maxStay', 'availability', 'isActive', 'onCreateSetup', 'created_at', 'updated_at');
    protected $appends = array('calendarTitle', 'startsAt', 'calendarRates');
    public $rules = array(
        'roomID' => 'required',
        'from' => 'required',
        'to' => 'required',
        'availability' => 'required|numeric'
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


    public function rooms() {
         return $this->hasMany('App\Room', 'roomID', 'id');
    }

    public function rates() {
        return $this->belongsToMany('App\Rate', 'calendar_rates', 'calendarID', 'rateID')->withPivot('price', 'rateID', 'active');
    }

    public static function lazyLoad() {
        return self::with('rates');
    }
    public function getStartsAtAttribute() {
        return strtotime($this->selectedDate);
    }
    public function getCalendarTitleAttribute()
    {
        $title = '';
        
        if ($this->rates()) {
            foreach ($this->rates()->get() as $r) {
                if ($r->pivot->active) {
                    $title .= 'PHP ' . $r->pivot->price . ' - <i>' . $r->name . '</i><br />';
                }
            }
        }

        return $title;
    }

    public function getCalendarRatesAttribute()
    {
        $roomRates =  array();
        $x = $this->rates(function($q) {
            $q->where('isMonthly', 0);
            $q->wherePivot('active', 1);
        })->get();/*->map(function($item) use($roomRates){
            $roomRates[$item->pivot->rateID] = ['id' => $item->pivot->rateID, 'name' => $item->name, 'price' => $item->pivot->price, 'active' => $item->pivot->active];
            return json_encode($roomRates);
        });*/

        if ($x) {
            foreach ($x as $item) {
                $roomRates[$item->pivot->rateID] = ['id' => $item->pivot->rateID, 'name' => $item->name, 'price' => $item->pivot->price, 'active' => $item->pivot->active];
            }
        }

        return $roomRates;
    }

    public static function updateAvailability($roomID, $noOfRooms, $checkIn, $checkOut) {
        $date = $checkIn;
        while(strtotime($date) <= strtotime($checkOut)) {
            $date = date('Y-m-d', strtotime($date));
            $calendar = \App\Calendar::find(array('selectedDate' => $date, 'roomID' => $roomID));
            if ($calendar) {
                $availability = $calendar->availability - $noOfRooms;
                $calendar->availability = $availability;
                $calendar->isActive = $availability  <= 0 ? 0 : 1;
                $calendar->save();
            }

            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
    }
}
