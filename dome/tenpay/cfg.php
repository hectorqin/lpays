<?php
return array(
	'partner'=>'1900000109',
	'key'=>'8934e7d15453e97507ef794cf7b0519d',
	
	'op_user_id'=>'1900000109',
	'op_user_passwd'=>'111111',
	'pem_path'=>'1900000109.pem',
	'pem_passwd'=>'1900000109',
	'ca_path'=>__DIR__.'/rootca.pem',
	//	
	'pay_pc_notify_url'=>CALLBACK_PATH.'tenpay/pay_pc_notify.php',
	'pay_pc_return_url'=>CALLBACK_PATH.'tenpay/pay_pc_return.php',
	'pay_wap_notify_url'=>CALLBACK_PATH.'tenpay/pay_wap_notify.php',
	'pay_wap_return_url'=>CALLBACK_PATH.'tenpay/pay_wap_return.php',
);