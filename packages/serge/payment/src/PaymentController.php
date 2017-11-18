<?php
namespace Serge\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Reservation;
use Illuminate\Support\Facades\Mail;
use Serge\Primesoft;
use Illuminate\Database\Eloquent\Model;
class PaymentController extends Controller {    
    public function pesopay(Request $request) {
        $orderRef = $request->session()->pull('orderRef', null);
        if ($orderRef) {
            $order = \App\Booking::where(['refId' => $orderRef])->firstOrFail();
            if ($order) {
                $pesopay = new PesopayPayment;
                $default = config('payment.default');
                $merchantId = config('payment.'.$default . '.merchantID');
                $paymentType = config('payment.type');
                $currencyCode = config('payment.currencyCode');
                $secureHashSecret= config('payment.'.$default . '.secureHashSecret');
                $orderRef = $order->refId;
                $totalPrice = (float)$order->totalAmount;
                $remarks = $order->specialInstructions;
                // Generate securehas
                $secureHash = $pesopay->generatePaymentSecureHash(
                                $merchantId, 
                                $orderRef, 
                                $currencyCode, 
                                $totalPrice, 
                                $paymentType, 
                                $secureHashSecret);

                $data = array(
                        'url' => config('payment.'.$default . '.paymentUrl'),
                        'orderRef' => $orderRef,
                        'amount' => $totalPrice,			
                        'merchantId' => $merchantId, 				 
                        'payMethod' => config('payment.method'), 
                        'payType'	=> $paymentType,
                        'currCode' => $currencyCode,
                        'lang' => config('payment.language'),			
                        'successUrl' => config('payment.successUrl'),
                        'failUrl' => config('payment.failUrl'),
                        'cancelUrl' => config('payment.cancelUrl'),
                        'remark' => $remarks,					
                        'secureHash' => $secureHash
                    );
                return view('payment::pesopay')->with('data', $data);
            }
        } 

        return response()->json('OrderRef no found!', 400, [], JSON_UNESCAPED_UNICODE);
    }
    /*
    public function status(Request $request, $status) {
        $ref 			= $request->input('Ref');
        $booking = \App\Booking::where(['refId' => $ref])->firstOrFail();
        
        if ($booking) {
            $payment = new \App\Payment;
            $payment->method = 'pesopay';
            $payment->referenceID = $ref;
            $payment->customData = [];

            switch($status) {
                case 'fail':
                    $payment->status = $status;
            }
            
            
            $booking->payment()->save($payment);
        }
    }*/

    public function datafeed(Request $request) {
        try {
            
        
        
            $src 			= $request->input('src');
            $prc 			= $request->input('prc');
            $ord 			= $request->input('Ord');
            $holder 		= $request->input('Holder');
            $successCode 	= $request->input('successcode');
            $ref 			= $request->input('Ref');
            $payRef 		= $request->input('PayRef');
            $amt 			= $request->input('Amt');
            $cur 			= $request->input('Cur');
            $remark 		= $request->input('remark', '');
            $authId 		= $request->input('AuthId');
            $eci 			= $request->input('eci');
            $payerAuth 		= $request->input('payerAuth');
            $sourceIp 		= $request->input('sourceIp');
            $ipCountry 		= $request->input('ipCountry');
            $secureHash 	= $request->input('secureHash');
    
            
                
                $booking = \App\Booking::where(['refId' => $ref])->first();
            
                if ($booking) {
                    
                    $pesopay = new PesopayPayment;
                    if ($booking->customer->email == 'serg.casquejo@gmail.com') {
                        $default = 'sandbox';
                    } else {
                        $default = config('payment.default');
                    }
            
                    $verified = $pesopay->verifyPaymentDatafeed(
                            $src, 
                            $prc, 
                            $successCode, 
                            $ref, 
                            $payRef, 
                            $cur, 
                            $amt, 
                            $payerAuth, 
                            config('payment.'.$default . '.secureHashSecret'), 
                            $secureHash);
                            
            
                $payment = new \App\Payment;
        
                if ($successCode == 0 && $verified) {
                    // Transaction Accepted
                    // *** Add the Security Control here, to check the currency, amount with the
                    // *** merchant’s order reference from your database, if the order exist then
                    // *** accepted otherwise rejected the transaction.
                    // Update your database for Transaction Accepted and send email or notify your
                    // customer.
                    $payment->status = $payment->paymentStatusPaid;
                   
        
                    // In case if your database or your system got problem, you can send a void
                    // transaction request. See API guide for more details
                } else {
                    // Transaction Rejected
                    // Update your database for Transaction Rejected
                    $payment->status = $payment->paymentStatusRejected;
                }
                
        
                $booking->status = $booking->bookingStatusSuccess;
                if ($booking->save()) {
                    //Update room availability
                    \App\Calendar::updateAvailability(
                        $booking->roomID, 
                        $booking->noOfRooms, 
                        $booking->checkIn, 
                        $booking->checkOut
                    );
        
                    $payment->totalAmount = $amt;
                    $payment->method = 'pesopay';
                    $payment->referenceID = $payRef;
                    $payment->customData = serialize($request->input());
                    
                    $booking->payment()->save($payment);
                    // if ($payment->status == $payment->paymentStatusPaid) {
                    //    $primeSoft = new Primesoft\Primesoft($booking);
                    //    $primeSoft->setupPrimeSoftData();
                    // }
                    
                }
                
     
                
                $data = array(
                    'name' => 'Admin',
                    'pageHeading' => 'New Reservation',
                    'message' => 'Please see below information',
                    'amountPaid' => $booking->nf($booking->lastPayment->totalAmount, true),
                    'refId' => $booking->refId,
                    'checkIn' => date('l F d Y', strtotime($booking->checkIn)) .' ' . $booking->checkInTime,
                    'checkOut' => date('l F d Y', strtotime($booking->checkOut)) .' ' .  $booking->checkOutTime,
                    'noOfAdults' => $booking->noOfAdults,
                    'extraPerson' => $booking->extraPerson,
                    'noOfChild' => $booking->noOfChild ? $booking->noOfChild : 0,
                    'roomRate' => $booking->nf($booking->roomRate, true),
                    'noOfNights' => $booking->noOfNights,
                    'noOfRooms' => $booking->noOfRooms,
                    'totalAmount' => $booking->nf($booking->totalAmount, true),
                    'status' => $booking->status,
                    'lastPayment' => $booking->lastPayment ?  true : false,
                    'paymentMethod' => $booking->lastPayment->method,
                    'amountPaid' => $booking->lastPayment->totalAmount,
                    'paymentStatus' => $booking->lastPayment->status,
                    'customerName' => $booking->customer->salutation .' '. $booking->customer->firstName .' '. $booking->customer->middleName .' '. $booking->customer->lastName,
                    'customerEmail' => $booking->customer->email,
                    'customerContact' => $booking->customer->contact,
                    'customerAddress1' => $booking->customer->address1,
                    'customerAddress2' => $booking->customer->address2,
                    'customerState' => $booking->customer->state,
                    'customerCity' => $booking->customer->city,
                    'customerZipcode' => $booking->customer->zipcode,
                    'customerCountryCode' => $booking->customer->countryCode,
                    'specialInstructions' => $booking->specialInstructions,
                    'billingInstructions' => $booking->billingInstructions
                );
    
             
                Mail::to(Config('mail.emails.info'))
                ->send(new Reservation($data));
              
                $data['name'] = $booking->customer->firstName;
                $data['pageHeading'] = 'You have successfully booked';
     
                
                Mail::to($booking->customer->email)
                ->send(new Reservation($data));
                
            }
        } catch (\Swift_TransportException  $e) {
            \Log::info('ERROR: '.$e->getMessage());
            // return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
        }
        // Print out 'OK' to notify pesopay you have received the payment result
        return "OK";
    }
}


