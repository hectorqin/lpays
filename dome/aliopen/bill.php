<?php
use LPAY\Adapter\AliOpen\Bill;
include __DIR__."/../Bootstarp.php";
include_once 'alipay.config.php';
$config=\LPAY\Adapter\AliOpen\Config::arr($alipay_config);
$bill= new Bill($config);
$bill->set_date("2016-12-27")->exec();
while ($result=LPAY\Utils::each_result($bill)){
	bill_callback($bill,$result);
}