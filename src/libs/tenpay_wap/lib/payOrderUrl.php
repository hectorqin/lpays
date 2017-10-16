<?php
//---------------------------------------------------------
//财付通订单查后台调用示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");

/* 商户号 */
$partner = "1900000109";


/* 密钥 */
$key = "8934e7d15453e97507ef794cf7b0519d";




/* 创建支付请求对象 */
$reqHandler = new RequestHandler();

//通信对象
$httpClient = new TenpayHttpClient();

//应答对象
$resHandler = new ClientResponseHandler();

//-----------------------------
//设置请求参数
//-----------------------------
$reqHandler->init();
$reqHandler->setKey($key);

$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/normalorderquery.xml");
$reqHandler->setParameter("partner", $partner);
//out_trade_no和transaction_id至少一个必填，同时存在时transaction_id优先
$reqHandler->setParameter("out_trade_no", "0920157372");
//$reqHandler->setParameter("transaction_id", "2000000501201004300000000442");			



//-----------------------------
//设置通信参数
//-----------------------------
$httpClient->setTimeOut(5);
//设置请求内容
$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call()) {
	//设置结果参数
	$resHandler->setContent($httpClient->getResContent());
	$resHandler->setKey($key);

	//判断签名及结果
	//只有签名正确并且retcode为0才是请求成功
	if($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == "0" ) {
		//取结果参数做业务处理
		//商户订单号
		$out_trade_no = $resHandler->getParameter("out_trade_no");
		
		//财付通订单号
		$transaction_id = $resHandler->getParameter("transaction_id");
		
		//金额,以分为单位
		$total_fee = $resHandler->getParameter("total_fee");
		
		//支付结果
		$trade_state = $resHandler->getParameter("trade_state");
		
		//支付成功
		if($trade_state == "0") {
		}
		
		echo "OK,trade_state=" . $trade_state . ",is_split=" . $resHandler->getParameter("is_split") . ",is_refund=" . $resHandler->getParameter("is_refund") . "<br>";
		
		
	} else {
		//错误时，返回结果可能没有签名，记录retcode、retmsg看失败详情。
		echo "验证签名失败 或 业务错误信息:retcode=" . $resHandler->getParameter("retcode"). ",retmsg=" . $resHandler->getParameter("retmsg") . "<br>";
	}
	
} else {
	//后台调用通信失败
	echo "call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	//有可能因为网络原因，请求已经处理，但未收到应答。
}


//调试信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
/*
echo "<br>------------------------------------------------------<br>";
echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
echo "req:" . htmlentities($reqHandler->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
echo "res:" . htmlentities($resHandler->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
echo "reqdebug:" . $reqHandler->getDebugInfo() . "<br><br>" ;
echo "resdebug:" . $resHandler->getDebugInfo() . "<br><br>";
*/


?>