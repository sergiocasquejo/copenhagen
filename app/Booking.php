<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Booking extends Model
{   
    const CHECK_IN_TIME = '02:00:00 PM';
    const CHECK_OUT_TIME = '12:00:00 PM';
    const BOOKING_SUCCESS = 'success';
    const BOOKING_PENDING = 'pending';
    const BOOKING_CANCEL = 'cancel';

    private $apiUsername = 'philweb1';
	private $apiPassword = 'test';
	private $apiKey = '2iOZd7SHg5'; 
	private $apiUrl = 'https://124.107.133.218';
	private $apiPort8001 = 8001;
	private $apiPort8003 = 8003;
	private $apiReservationAgent = 'PHILWEB';
	private $apiReservationName = 'PHILWEB';
    private $apiInfoSourceDesc = 'INTERNET/ONLINE';
	private $apiCompanyCode = 'WEBSITE';
    private $apiSessionID = null;
    private $apiSelectedPort = null;
    private $apiPaymentType = 'DB';
    

    protected $table = 'bookings';
    protected $appends = array(
        'title', 
        'arrival', 
        'departure', 
        'totalAmountFormatted', 
        'roomRateFormatted',
        'lastPayment');
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customerID', 'id');
    }

    public function room()
    {
        return $this->belongsTo('App\Room', 'roomID', 'id');
    }

    public function payment() {
        return $this->hasMany('App\Payment', 'bookingID', 'id');
    }

    public static function lazyLoad() {
         return self::with('customer', 'room');
     }

    public $rules = array(
        'roomID' => 'required',
        //'customerID' => 'required',
        'checkIn' => 'required',
        //'checkInTime' => 'required',
        'checkOut' => 'required',
        // 'checkOutTime' => 'required',
        'noOfRooms' => 'required|numeric',
        'noOfNights' => 'required|numeric',
        'noOfAdults' => 'required|numeric',
        'noOfChild' => 'numeric',
        'roomRate' => 'required|numeric',
        'totalAmount' => 'required|numeric',
        //'status' => 'required',
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

    public $step1Rules = array(
        'roomId' => 'required',
        'rateId' => 'required',
        'checkIn' => 'required',
        'checkOut' => 'required',
        'noOfRooms' => 'required|numeric',
        'noOfAdults' => 'required|numeric',
        'noOfChild' => 'numeric',
    );

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
        return strtotime($this->checkIn);
    }

    public function getDepartureAttribute() {
        return strtotime($this->checkOut);
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

    // Count total Nights
    public function countTotalNights($checkIn, $checkOut) {
        return intval(abs(strtotime($checkOut) - strtotime($checkIn)) / 86400);
    }
    /**
     * calculateTotaPrice 
     * Calculate total price 
     *@param $roomRate - The room calculateTotaPrice
     *@param $noOfRooms - Total room selected
     *@param $totalNights - Total nights
     */
    public function calculateTotaPrice($roomRate, $noOfRooms, $totalNights) {
        return $roomRate * ($noOfRooms * $totalNights);
    }

    public function setupPrimeSoftData(Booking $booking) {
        // Login to primesoft
        $this->loginToPrimeSoft();

        $specialInstructions = 
        $this->specialInstructions.', '.
        $booking->customer->salutation.', '.
        $booking->customer->firstName.', '.
        $booking->customer->lastName.', '.
        $booking->customer->email.', '.
        $booking->customer->address1.', '.
        $booking->customer->address2.', '.
        $booking->customer->city.', '.
        $booking->customer->zipcode.', '.
        $booking->customer->state.', '.
        $booking->customer->country.', '.
        $booking->customer->contact;
        
		$data = array( 
				'SessionID' 			=> $this->apiSessionID,
				'reservation_name'		=> $this->apiReservationName,
				'reservation_agent' 	=> $this->apiReservationAgent,
				'arrival_date'			=> $booking->checkIn,
				'arrival_time' 			=> self::CHECK_IN_TIME,
				'departure_date' 		=> $booking->checkOut,
				'departure_time'	 	=> self::CHECK_IN_TIME,
				'market_segment_code'	=> self::CHECK_OUT_TIME,
				'info_source_desc' 		=> $this->apiInfoSourceDesc,
				'payment_type' 			=> $this->apiPaymentType,
				'special_instructions' 	=> $specialInstructions,
				'billing_instructions' 	=> $booking->billingInstructions,
				'rate_code' 			=> $booking->rateCode,
				'num_rooms' 			=> $booking->noOfRooms,
				'num_adults' 			=> $booking->noOfAdults,
				'num_children' 			=> $booking->noOfChild,
				'meal_type' 			=> $booking->mealType,
				'room_type_code' 		=> $booking->roomTypeCode,
				'company_code' 			=> $this->apiCompanyCode,
			);

		$port = strtolower($booking->building) == 'main' ? $this->apiPort8003 : $this->apiPort8001;
		$url = $this->apiUrl.':'.$port.'/transactions/hotel/newReservation';
        
        // Post booking to primesoft
        $result = $this->postToPrimeSoftAPI($url, $data);
        // Logoout to primesoft
        $this->logoutToPrimeSoft();

        return $result;
    }

    private function postToPrimeSoftAPI($url, $data = array())
	{
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::NO);
			
		//curl_setopt($ch, CURLOPT_CAINFO, "/path/to/CA.crt");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, self::NO);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, self::YES);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}

    private function loginToPrimeSoft() 
	{
		$data = array(
            'UserName' => $this->apiUsername, 
            'Password' => $this->apiPassword, 
            'ApplicationKey' => $this->apiKey
        );
		$building = $building ? $building : self::PORT_8001;
		$url = $this->apiUrl.':'.$building.'/login';
		$resultPost = json_decode($this->postToPrimeSoftAPI($url, $data));
		$this->apiSessionID = $resultPost->result->SessionID;

	}

    private function logoutToPrimeSoft()
	{
		$data = array('SessionID' => $this->apiSessionID);
		$url = $this->apiUrl.':'.$this->apiSelectedPort.'/logout';
		return $this->postToPrimeSoftAPI($url, $data);
	}

    public function calculateTotalPrice($checkIn, $checkOut, $totalRooms, $roomId, $rateId) {
        $room = \App\Room::join('room_rates', 'rooms.id', '=', 'room_rates.roomID')
            ->where(['rooms.id' => $roomId, 'room_rates.rateID' => $rateId, 'room_rates.isActive' => 1])
            ->select(\DB::raw('room_rates.price'))->first();


        $subTotal = 0;
        $date = $checkIn;
        while(strtotime($date) <= strtotime($checkOut)) {
            $date = date('Y-m-d', strtotime($date));
            $calendar = \App\Calendar::join('calendar_rates', 'calendar.id', '=', 'calendar_rates.calendarID')
            ->where(['calendar.selectedDate' => $date, 'calendar.roomID' => $roomId, 'calendar_rates.rateID' => $rateId, 'calendar_rates.active' => 1])
            ->select(\DB::raw('calendar_rates.price'))->first();
            if ($calendar->price) {
                $subTotal += (double)$calendar->price;
            } else {
                $subTotal += (double)$room->price;
            }

            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        return $subTotal * $totalRooms;
    }
}
