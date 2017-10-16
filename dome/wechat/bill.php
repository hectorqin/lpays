<?php
use LPAY\Adapter\Wechat\Bill;
include __DIR__."/../Bootstarp.php";
include_once './WxPay.Config.php';
$config=\LPAY\Adapter\Wechat\PayCodeConfig::arr(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());
$bill= new Bill($config);
$bill->set_date("2017-1-9")->exec();
while ($result=LPAY\Utils::each_result($bill)){
	bill_callback($bill,$result);
}