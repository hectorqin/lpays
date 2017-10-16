<?php
use LPAY\PayUtils\Pay;
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/pay_public.php";
$name=@$_GET['name'];
$order_no=@$_GET['pay_sn'];
$pay_no=@$_GET['pay_no'];
$create_time=@$_GET['carete_time'];

//一般从数据库里查询出来...
// $name='lpay_upacp';//支付类型
// $order_no='MY161209140438593';//订单号
// $pay_no='201612091404380817618';//支付号
// $create_time=time();//订单创建时间

$name=LPAY\Adapter\Alipay\PayWeb::NAME;//支付类型
$order_no='MY161227155854705';//订单号
$pay_no='2016122721001004910236848403';//支付号
$create_time=time();//订单创建时间

//进行付款查询.并完成收款等....
$result = Pay::instance()->query($name, $order_no, $pay_no, $create_time);
if ($result->get_status()==\LPAY\Pay\PayResult::STATUS_SUCC){
	result_callback($result);//完成交易...
}


