<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Booking extends Model
{   
    public $bookingStatusSuccess = 'success';
    public $bookingStatusPending = 'pending';
    public $bookingStatusCancel = 'cancel';
    public $bookingCheckInTime = '2:00:00 pm';
    public $bookingCheckOutTime = '2:00:00 pm';
    
    protected $table = 'bookings';
    protected $appends = [
        'title', 
        'arrival', 
        'departure', 
        'totalAmountFormatted', 
        'roomRateFormatted',
        'lastPayment'];
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customerID');
    }

    public function room()
    {
        return $this->belongsTo('App\Room', 'roomID', 'id');
    }

    public function payment() {
        return $this->hasMany('App\Payment', 'bookingID', 'id');
    }

    public function rate() {
        return $this->hasOne('App\Rate', 'id', 'rateId');
    }

    public static function lazyLoad() {
         return self::with('customer', 'room', 'rate');
     }

    public $rules = [
        'roomId' => 'required',
        'checkIn' => 'required',
        'checkOut' => 'required',
        'noOfRooms' => 'required|numeric',
        'noOfNights' => 'required|numeric',
        'noOfAdults' => 'required|numeric',
        'noOfChild' => 'numeric',
        'roomRate' => 'required|numeric',
        'totalAmount' => 'required|numeric'
    ];


    public $step1Rules = [
        'roomId' => 'required',
        'rateId' => 'required',
        'checkIn' => 'required',
        'checkOut' => 'required',
        'noOfRooms' => 'required|numeric|min:1',
        'noOfAdults' => 'required|numeric|min:1',
        'noOfChild' => 'numeric',
        'noOfNights' => 'required|numeric|max:30|min:1'
    ];

    public $step2Rules = [
        'roomId' => 'required',
        'rateId' => 'required',
        'checkIn' => 'required',
        'checkOut' => 'required',
        'noOfRooms' => 'required|numeric',
        'noOfAdults' => 'required|numeric',
        'noOfChild' => 'numeric',
    ];

    public $step3Rules = [
        'agree' => 'required',
        'paymentMethod' => 'required'
    ];
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
            'max' => ':attribute must not exists :max days',
            'min' => ':attribute minimum of :min'
        ];
    }

    
    public function validateStep1($data, $rules) {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = \Validator::make($data, $rules, $this->messages());
        $v->sometimes('noOfNights', 'required', function ($input) {
            return $input->noOfNights <= 30;
        });
        // return the result
        return $v;
    }

    public function validate($data, $rules = false)
    {
        if (!$rules) {
            $rules = $this->rules;
        }
        // make a new validator object
        $v = \Validator::make($data, $rules, $this->messages());
        // return the result
        return $v;
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            //$model->setupPrimeSoftData($model);
        });

        static::updating(function($model)
        {
            //
        });
    }

    public function getTitleAttribute()
    {
        return $this->customer->salutation . '. '. $this->customer->firstName .' '. $this->customer->lastName;
    }

    public function getArrivalAttribute() {
        return strtotime($this->checkIn . ' '. $this->checkInTime);
    }

    public function getDepartureAttribute() {
        return strtotime($this->checkOut . ' '. $this->checkOutTime);
    }

    
    public function getTotalAmountFormattedAttribute($value) {
        return number_format($this->totalAmount, 2, '.', ', ');
    }

    public function getRoomRateFormattedAttribute($value) {
        return number_format($this->roomRate, 2, '.', ', ');
    }
    public function getLastPaymentAttribute() {
        return $this->payment()->orderBy('id', 'desc')->first();
    }

    public function nf($n, $s = false, $d = 2, $p = '.', $t = ', ') {
        return  ($s ? config('payment.currSymbol') : '') . number_format($n, $d, $p, $t);
    }

    // Count total Nights
    public function countTotalNights($checkIn, $checkOut) {
        return intval(abs(strtotime($checkOut) - strtotime($checkIn)) / 86400);
    }



    public function calculateTotalPrice($checkIn, $checkOut, $roomId, $rateId) {
        $subTotal = 0;
        $datetime1 = new \DateTime($checkIn);
        $datetime2 = new \DateTime($checkOut);
        $interval = $datetime1->diff($datetime2);
        // echo $interval->format('%y years %m months and %d days');
        
        $room = \App\Room::with(array('rates' => function($q) use($rateId) {
            $q->where('isMonthly', 1);
            $q->wherePivot('isActive', 1);
            $q->orWhere(function($q) use($rateId) {
                $q->where('rates.id', $rateId);
            });
            
        }))->find($roomId)->toArray();
        $roomPriceRate = array();


        if (count($room['rates']) != 0) {
            foreach($room['rates'] as $rate) {
                if ($rate['isMonthly']) {
                    $roomPriceRate['monthly'] = $rate['pivot']['price'];
                } else {
                    $roomPriceRate['daily'] = $rate['pivot']['price'];
                }
            }
        }

        if ($interval->format('%m') > 0) {
            $subTotal += $interval->format('%m') * $roomPriceRate['monthly'];
            $checkIn = date ("Y-m-d", strtotime("+". $interval->format('%m')  ." month", strtotime($checkIn)));
        }

        
        $date = $checkIn;
        while(strtotime($date) < strtotime($checkOut)) {
            $date = date('Y-m-d', strtotime($date));
            $calendar = \App\Calendar::join('calendar_rates', 'calendar.id', '=', 'calendar_rates.calendarID')
            ->where(['calendar.selectedDate' => $date, 'calendar.roomID' => $roomId, 'calendar_rates.rateID' => $rateId, 'calendar_rates.active' => 1])
            ->select(\DB::raw('calendar_rates.price'))->first();
            // use calendar price if it has set
            if (isset($calendar->price)) {
                $subTotal += (double)$calendar->price;
            } else {
                // Use default price
                $subTotal += (double)$roomPriceRate['daily'];
            }

            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        return $subTotal;
    }
}
