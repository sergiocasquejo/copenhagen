<?php
namespace Serge\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Reservation;
use Illuminate\Support\Facades\Mail;
use Serge\PrimeSoft;

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

    public function datafeed(Request $request) {
        // Print out 'OK' to notify pesopay you have received the payment result 
        echo "OK";

        $src 			= $request->input('src');
        $prc 			= $request->input('prc');
        $ord 			= $request->input('Ord');
        $holder 		= $request->input('Holder');
        $successCode 	= $request->input('successcode');
        $ref 			= $request->input('Ref');
        $payRef 		= $request->input('PayRef');
        $amt 			= $request->input('Amt');
        $cur 			= $request->input('Cur');
        $remark 		= $request->input('remark');
        $authId 		= $request->input('AuthId');
        $eci 			= $request->input('eci');
        $payerAuth 		= $request->input('payerAuth');
        $sourceIp 		= $request->input('sourceIp');
        $ipCountry 		= $request->input('ipCountry');
        $secureHash 	= $request->input('secureHash');

        $default = config('payment.default');

        $verified = $verifyPaymentDatafeed(
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
        $booking = App\Booking::where(['refId' => $ref])->firstOrFail();
        $payment = new Payment;

        if ($successCode == 0 && $verified) {
            // Transaction Accepted
            // *** Add the Security Control here, to check the currency, amount with the
            // *** merchant’s order reference from your database, if the order exist then
            // *** accepted otherwise rejected the transaction.
            // Update your database for Transaction Accepted and send email or notify your
            // customer.
            $payment->status = 'paid';
           

            // In case if your database or your system got problem, you can send a void
            // transaction request. See API guide for more details
        } else {
            // Transaction Rejected
            // Update your database for Transaction Rejected
            $payment->status = 'rejected';
        }

        $booking->status = 'completed';
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

            $primeSoft = new PrimeSoft($booking);
            $primeSoft->setupPrimeSoftData();
        }
        try {
            Mail::to(Config('mail.emails.reservation'))
            ->cc(Config('mail.emails.info'))
            ->send(new Reservation($booking));
        } catch (\Swift_TransportException  $e) {
            return response()->json('Failed to send your message. Please try later or contact the administrator by another method.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    }
}


