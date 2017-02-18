<?php 

return array(
    'live' => array(
        'merchantID' => '18139701',
        'secureHashSecret' => 'C7JADIypDIXv95W7Mh53GD3cOI4RhFjb',
        'paymentUrl' => 'https://www.pesopay.com/b2c2/eng/payment/payForm.jsp'
    ),
    'sandbox' => array(
        'merchantID' => '18061337',
        'secureHashSecret' => 'OItXPjWKTVsKqUc0U8E0S2dirFSl3Elk',
        'paymentUrl' => 'https://test.pesopay.com/b2cDemo/eng/payment/payForm.jsp'
    ),
    'method' => 'ALL',
	'type' => 'N',
	'currencyCode' => 'PHP',
	'language' => 'E',
    'default' => 'sandbox',
    'successUrl' => 'http://cop.local/booking/complete',
    'failUrl' => 'http://cop.local/booking/payment-fail',
    'cancelUrl' => 'http://cop.local/booking/payment-cancel',
);