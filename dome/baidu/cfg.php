<?php
return array(
	'sp_no'=>'9000100005',
	'key_file'=>__DIR__.'/sp.key',
	//url
	'pay_pc_notify_url'=>CALLBACK_PATH.'baidu/pay_pc_notify.php',
	'pay_pc_return_url'=>CALLBACK_PATH.'baidu/pay_pc_return.php',
	'pay_wap_notify_url'=>CALLBACK_PATH.'baidu/pay_wap_notify.php',
	'pay_wap_return_url'=>CALLBACK_PATH.'baidu/pay_wap_return.php',
		
	//refund
	'refund_notify_url'=>CALLBACK_PATH.'baidu/refund_notify.php',
);