<?php 
use LPAY\PayUtils\Pay;
use LPAY\Pay\PayResult;
include __DIR__."/../Bootstarp.php";
include_once './WxPay.Config.php';
$pay=Pay::wechat_wap(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());
$result=$pay->pay_callback();
switch ($result->get_status()){
	case PayResult::STATUS_SUCC:
		pay_callback($pay,$result);
		echo "支付成功";
	break;
	default:
	echo $result->get_msg();	
}