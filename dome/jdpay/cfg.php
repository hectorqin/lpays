<?php
return array(
	'merchantNum'=>'22294531',
	'desKey'=>'ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t',
	'device'=>'111',
	'private_key'=>__DIR__.'/seller_rsa_private_key.pem',
	'public_key'=>__DIR__.'/wy_rsa_public_key.pem',

	//url
	'pay_wap_notify_url'=>CALLBACK_PATH.'jdpay/pay_wap_notify.php',
	'pay_wap_return_url'=>CALLBACK_PATH.'jdpay/pay_wap_return.php',
	'pay_pc_notify_url'=>CALLBACK_PATH.'jdpay/pay_pc_notify.php',
	'pay_pc_return_url'=>CALLBACK_PATH.'jdpay/pay_pc_return.php',
		
	//refund
	'refund_notify_url'=>'http://www.test.cc/Cache/Temp/ipn.php',
);