<?php 
namespace Serge\Primesoft;

class Primesoft {
    private $apiSessionID = null;
    private $apiSelectedPort = null;
    private $apiUrl = null;
    private $data = null;

    public function __construct(\App\Booking $booking) {
        $this->data = $booking;
        $this->apiSelectedPort = strtolower($this->data->building) == 'main' ? config('primesoft.apiPort1') : config('primesoft.apiPort1');
        $this->apiUrl = config('primesoft.apiUrl').':'.$this->apiSelectedPort;
    }

    public function setupPrimeSoftData() {
        // Login to primesoft
        if (!$this->loginToPrimeSoft()) {
            return false;
        }

        $specialInstructions = 
        $this->specialInstructions.', '.
        $this->data->customer->salutation.', '.
        $this->data->customer->firstName.', '.
        $this->data->customer->lastName.', '.
        $this->data->customer->email.', '.
        $this->data->customer->address1.', '.
        $this->data->customer->address2.', '.
        $this->data->customer->city.', '.
        $this->data->customer->zipcode.', '.
        $this->data->customer->state.', '.
        $this->data->customer->country.', '.
        $this->data->customer->contact;
        
		$data = [
				'SessionID' 			=> $this->apiSessionID,
				'reservation_name'		=> config('primesoft.apiReservationName'),
				'reservation_agent' 	=> config('primesoft.apiReservationAgent'),
				'arrival_date'			=> $this->data->checkIn,
				'arrival_time' 			=> $this->data->checkInTime,
				'departure_date' 		=> $this->data->checkOut,
				'departure_time'	 	=> $this->data->checkOutTime,
				'market_segment_code'	=> config('primesoft.apiMarketSegmentCode'),
				'info_source_desc' 		=> config('primesoft.apiInfoSourceDesc'),
				'payment_type' 			=> config('primesoft.apiPaymentType'),
				'special_instructions' 	=> $specialInstructions,
				'billing_instructions' 	=> $this->data->billingInstructions,
				'rate_code' 			=> $this->data->rate()->rateCode,
				'num_rooms' 			=> $this->data->noOfRooms,
				'num_adults' 			=> $this->data->noOfAdults,
				'num_children' 			=> $this->data->noOfChild,
				'meal_type' 			=> $this->data->rate()->mealType,
				'room_type_code' 		=> $this->data->rate()->roomCode,
				'company_code' 			=> config('primesoft.apiCompanyCode'),
        ];

        // Post booking to primesoft
        $result = null; /*$this->postToPrimeSoftAPI(
            $this->apiUrl.'/transactions/hotel/newReservation', 
            $data
        );*/
        

        // Logoout to primesoft
        $this->logoutToPrimeSoft();
        
        return $result;
    }

    private function postToPrimeSoftAPI($url, $data = array())
	{
		$dataString = json_encode($data);

		$ch = curl_init();
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_CAINFO, "/path/to/CA.crt");
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POSTFIELDS,$dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}

    private function loginToPrimeSoft() 
	{
		$data = [
            'UserName' => config('primesoft.apiUsername'), 
            'Password' => config('primesoft.apiPassword'), 
            'ApplicationKey' => config('primesoft.apiKey')
        ];
		
	
		$resultPost = json_decode(
            $this->postToPrimeSoftAPI(
                $this->apiUrl.'/login', 
                $data
            )
        );
        

		$this->apiSessionID = $resultPost ? $resultPost->result->SessionID : null;
	}

    private function logoutToPrimeSoft()
	{
		return $this->postToPrimeSoftAPI(
            $this->apiUrl.'/logout', 
            ['SessionID' => $this->apiSessionID]
            );
	}
}