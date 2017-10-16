<?php
$alipay_config=array();
$alipay_config['app_id']		= '';
$alipay_config['private_key_path']	= __DIR__.'/rsa_private_key.pem';
$alipay_config['ali_public_key_path']= __DIR__.'/alipay_public_key.pem';
$alipay_config['input_charset']= 'utf-8';
$alipay_config['cacert']    = __DIR__.'/cacert.pem';


//支付用到
$alipay_config['pay_pc_notify_url']    = CALLBACK_PATH.'aliopen/pay_pc_notify.php';
$alipay_config['pay_pc_return_url']    = CALLBACK_PATH.'aliopen/pay_pc_return.php';
$alipay_config['pay_wap_notify_url']    = 'http://www.test.tk/Cache/Temp/ipn.php';//CALLBACK_PATH.'aliopen/pay_wap_notify.php';
$alipay_config['pay_wap_return_url']    = CALLBACK_PATH.'aliopen/pay_wap_return.php';
$alipay_config['pay_app_notify_url']    = CALLBACK_PATH.'aliopen/pay_app_notify.php';

//退款
$alipay_config['refund_notify_url']    = CALLBACK_PATH.'aliopen/refund_notify.php';

//提款用到
$alipay_config['seller_name'] ='';
$alipay_config['transfers_notify_url']    = CALLBACK_PATH.'aliopen/transfers_notify.php';


//额外配置
