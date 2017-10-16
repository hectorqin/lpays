<?php

//---------------------------------------------------------
//财付通即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require ("classes/ResponseHandler.class.php");
require ("classes/WapNotifyResponseHandler.class.php");

/* 商户号 */
$partner = "1900000109";

/* 密钥 */
$key = "8934e7d15453e97507ef794cf7b0519d";


/* 创建支付应答对象 */
$resHandler = new WapNotifyResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//商户订单号
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	
	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	
	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");

	if( "0" == $pay_result  ) {
		echo 'success';
	}
	else
	{
		echo 'fail';
	} 
	
} else {
	//回调签名错误
	echo "fail";
}


?>