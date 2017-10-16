<?php
return array(
	'merid'=>'777290058110048',
	'sign_cert'=>__DIR__.'/acp_test_sign.pfx',
	'sign_pwd'=>'000000',
	'encrypt_cert'=>__DIR__.'/acp_test_enc.cer',
	'verify_cert_dir'=>__DIR__.'/',
	'mode'=>'sandbox',//正式环境注释掉		
		
	//pay
	'pay_apple_notify_url'=>'http://www.test.tk/Cache/Temp/ipn.php',
	
	//refund
	'refund_apple_notify_url'=>'http://www.test.tk/Cache/Temp/ipn.php',
);