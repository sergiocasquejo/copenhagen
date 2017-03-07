<?php 
namespace Serge\Payment;

abstract class Payment {
    
    public function __constructor() {

    }

    public function formatNumber($number, $decimals = 2, $dp = '.', $sp = ',') {
        return number_format($number, $decimals, $dp, $sp);
    }
}


class PesopayPayment extends Payment {
    
    public function generatePaymentSecureHash(
        $merchantId, 
        $merchantReferenceNumber, 
        $currencyCode, 
        $amount, 
        $paymentType, 
        $secureHashSecret) {

        $buffer = $merchantId . '|' . 
        $merchantReferenceNumber . '|' . 
        $currencyCode . '|' . 
        $amount . '|' . 
        $paymentType . '|' . 
        $secureHashSecret;

        return sha1($buffer);
    }

    public function verifyPaymentDatafeed(
        $src, 
        $prc, 
        $successCode, 
        $merchantReferenceNumber, 
        $paydollarReferenceNumber, 
        $currencyCode, 
        $amount, 
        $payerAuthenticationStatus, 
        $secureHashSecret, 
        $secureHash) {

        $buffer = $src . '|' . $prc . '|' . $successCode . '|' . $merchantReferenceNumber . '|' . $paydollarReferenceNumber . '|' . $currencyCode . '|' . $amount . '|' . $payerAuthenticationStatus . '|' . $secureHashSecret;
        $verifyData = sha1($buffer);

        if ($secureHash == $verifyData) {
            return true;
        }
        return false;
    }


    function pay(
        $orderRef, 
        $cardNo,
        $securityCode,
        $cardHolder,
        $epMonth,
        $epYear,
        $totalPrice, 
        $remarks) {
        $default = config('payment.default');

        $secureHash = $this->generatePaymentSecureHash(
            config('payment.'.$default . '.merchantID'), 
            $orderRef, config('payment.currencyCode'), 
            $totalPrice, config('payment.type'), 
            config('payment.'.$default . '.secureHashSecret'));

        return $this->postToPesoPay(
            config('payment.'.$default . '.paymentUrl'),
            array(
				'orderRef' => $orderRef,
                'cardNo' => $cardNo,
                'securityCode' => $securityCode,
                'cardHolder' => $cardHolder,
                'epMonth' => $epMonth,
                'epYear' => $epYear,
				'amount' => $totalPrice,			
		    	'merchantId' => config('payment.'.$default . '.merchantID'), 				 
				'payMethod' => config('payment.method'), 
				'payType'	=> config('payment.type'),
				'currCode' => config('payment.currencyCode'),
				'lang' => config('payment.language'),			
				'successUrl' => config('payment.successUrl'),
				'failUrl' => config('payment.failUrl'),
				'cancelUrl' => config('payment.cancelUrl'),						
				'secureHash' => $secureHash,//config('payment.'.$default . 'secureHashSecret'),
				'remark' => $remarks
          	));
              
    }

    function postToPesoPay($url, $data) {
        $paypeso_args_array = array();
			foreach($data as $key => $value){
				$paypeso_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
			}
			
          	echo '<form action="' . $url . '" method="post" id="paydollar_payment_form">
            	' . implode('', $paypeso_args_array) . '           	
            	<!--<script type="text/javascript">
    				jQuery("#paydollar_payment_form").submit();    					
    			</script>-->
            </form>';
    }
		
}

