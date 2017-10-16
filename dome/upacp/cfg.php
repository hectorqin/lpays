<?php
return array(
		'merid'=>'700000000000001',
		'sign_cert'=>__DIR__.'/PM_700000000000001_acp.pfx',
		'sign_pwd'=>'000000',
		'encrypt_cert'=>__DIR__.'/RSA2048_PROD_index_22.cer',
		'verify_cert_dir'=>__DIR__.'/',
		'mode'=>'sandbox',//正式环境注释掉
		
		
		'pay_notify_url'=>'http://www.test.tk/Cache/Temp/ipn.php',
		'pay_return_url'=>CALLBACK_PATH.'upacp/return.php',
		
		//
		'refund_notify_url'=>'http://www.test.tk/Cache/Temp/ipn.php',
);