<?php
use LPAY\Adapter\Alipay\Bill;
include __DIR__."/../Bootstarp.php";
include_once 'alipay.config.php';
$config=\LPAY\Adapter\Alipay\Config::arr($alipay_config);
$bill= new Bill($config);
$bill->set_trans_code(array(
	'3012',
	'5004',
	'5103',
	'6001',
));
$bill->set_date("2016-12-27")->exec();
while ($result=LPAY\Utils::each_result($bill)){
	bill_callback($bill,$result);
}