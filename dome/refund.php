<?php

use LPAY\Utils;

require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/refund_public.php";
$name=@$_GET['name'];
$order_no=@$_GET['pay_sn'];
$pay_no=@$_GET['pay_no'];
$create_time=@$_GET['carete_time'];

//一般从数据库里查询出来...
$name='lpay_alipay_wap';//支付类型
$order_no='PD161227181618164245';//订单号
$pay_no='2016122721001004910237058479';//支付号
$total='27.500';//支付总额
$refund='0.49';//退款额

$pay_utils = LPAY\PayUtils\Refund::instance()->get_pay()->find_refund($name);

if (!$pay_utils){
	die('不支持退款');
}
//支付回调出错情况下手动同步订单状态....
//不要以次条件为是否扣款依据.只记录状态,失败最好人工介入
$param= new \LPAY\Pay\RefundParam($order_no, $pay_no, $total,$refund);
$param->set_return_no(Utils::snno_create(\LPAY\Pay\RefundParam::$sn_prefix));//设置退款号,不设置自动生成
$result=$pay_utils->refund($param);
if ($result->get_status()==LPAY\Pay\RefundResult::STATUS_SUCC){
	print_r($result);//完成退款
}else if ($result->get_status()==LPAY\Pay\RefundResult::STATUS_ING){
	print_r($result);//退款申请中,可能退款还没完成..
}else{
	echo "退款失败:";
	print_r($result->get_msg());//完成退款
}


