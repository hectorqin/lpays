<?php
use LPAY\Pay\PayParam;

require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/pay_public.php";
/*
 * 执行支付
 */

$name=@$_GET['name'];
$order_no=isset($_GET['order_no'])?$_GET['order_no']:LPAY\Utils::snno_create('MY');
$amount=isset($_GET['amount'])?$_GET['amount']:1;

$pay_param = new PayParam($amount,$order_no);
$pay=$pay_utils->get_pay()->find_pay($name);
if (!$pay){
	die('支付方式错误');
}
$res=$pay->pay_render($pay_param);
echo $res;

//自定义前端JS处理...
$type=$res->get_out();
include_once 'js.php';

