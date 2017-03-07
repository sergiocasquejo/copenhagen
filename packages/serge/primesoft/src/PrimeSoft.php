<?php 
namespace Serge\PrimeSoft;

class PrimeSoft {
    private $apiSessionID = null;
    private $apiSelectedPort = null;
    private $apiUrl = null;

    public function __construct(\App\Booking $booking) {
        $this->apiSelectedPort = strtolower($booking->building) == 'main' ? config('primesoft.apiPort1') : config('primesoft.apiPort1');
        $this->apiUrl = config('primesoft.apiUrl').':'.$this->apiSelectedPort;
    }

    public function setupPrimeSoftData() {
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
        
		$data = [
				'SessionID' 			=> $this->apiSessionID,
				'reservation_name'		=> config('primesoft.apiReservationName'),
				'reservation_agent' 	=> config('primesoft.apiReservationAgent'),
				'arrival_date'			=> $booking->checkIn,
				'arrival_time' 			=> $booking->checkInTime,
				'departure_date' 		=> $booking->checkOut,
				'departure_time'	 	=> $booking->checkOutTime,
				'market_segment_code'	=> config('primesoft.apiMarketSegmentCode'),
				'info_source_desc' 		=> config('primesoft.apiInfoSourceDesc'),
				'payment_type' 			=> config('primesoft.apiPaymentType'),
				'special_instructions' 	=> $specialInstructions,
				'billing_instructions' 	=> $booking->billingInstructions,
				'rate_code' 			=> $booking->rateCode,
				'num_rooms' 			=> $booking->noOfRooms,
				'num_adults' 			=> $booking->noOfAdults,
				'num_children' 			=> $booking->noOfChild,
				'meal_type' 			=> $booking->mealType,
				'room_type_code' 		=> $booking->roomTypeCode,
				'company_code' 			=> config('primesoft.apiCompanyCode'),
        ];

        // Post booking to primesoft
        $result = $this->postToPrimeSoftAPI(
            $this->apiUrl.'/transactions/hotel/newReservation', 
            $data
        );

        // Logoout to primesoft
        $this->logoutToPrimeSoft();

        return $result;
    }

    private function postToPrimeSoftAPI($url, $data = array())
	{
		$dataString = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
		//curl_setopt($ch, CURLOPT_CAINFO, "/path/to/CA.crt");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$dataString);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}

    private function loginToPrimeSoft() 
	{
		$data = [
            'UserName' => $this->apiUsername, 
            'Password' => $this->apiPassword, 
            'ApplicationKey' => $this->apiKey
        ];
		
		
		$resultPost = json_decode(
            $this->postToPrimeSoftAPI(
                $this->apiUrl.'/login', 
                $data
            )
        );

		$this->apiSessionID = $resultPost->result->SessionID;

	}

    private function logoutToPrimeSoft()
	{
		return $this->postToPrimeSoftAPI(
            $this->apiUrl.'/logout', 
            ['SessionID' => $this->apiSessionID]
            );
	}
}