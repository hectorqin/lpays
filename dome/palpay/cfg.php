<?php
return array(
	'username'=>'jb-us-seller_api1.paypal.com',
	'password'=>'WX4WTU3S8MY44S7F',
	'signature'=>'AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy',
	'payment_type'=>'sale',
	'currency_code'=>'USD',
	'mode'=>'sandbox',
	//
	'pay_ipn_url'=>'http://www.test.tk/Cache/Temp/ipn.php',
	'pay_return_url'=>CALLBACK_PATH.'palpay/pay_retrun.php',
	'pay_do_url'=>CALLBACK_PATH.'palpay/pay_do.php',
);

