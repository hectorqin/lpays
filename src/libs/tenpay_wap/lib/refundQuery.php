<?php
//---------------------------------------------------------
//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
/* 商户号 */
$partner = "1900000109";

/* 密钥 */
$key = "8934e7d15453e97507ef794cf7b0519d";


/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/normalrefundquery.xml");


$httpClient = new TenpayHttpClient();
//应答对象
$resHandler = new ClientResponseHandler();
//----------------------------------------
//设置支付参数 
//----------------------------------------
//$reqHandler->setParameter("ver", "2.0");  //版本号
$reqHandler->setParameter("partner", $partner);  //商户号

//transaction_id、sp_billno两项有一项必填，都存在则以transaction_id为准
$reqHandler->setParameter("out_trade_no", "1711217684");  //商户订单号
//$reqHandler->setParameter("sp_billno", "201010200898845817");  //商户内部产生的订单号
//$reqHandler->setParameter("attach", iconv('UTF-8','GBK',"中文"));  //商户附加信息，如果包含中文字符，需要转换为GBK
//$reqHandler->setParameter("charset", 1);  

//设置请求返回的等待时间
   $httpClient->setTimeOut(5);	
    
    //设置发送类型POST
   $httpClient->setMethod("POST");

$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call()) {
	$resHandler->setContent($httpClient->getResContent());
	$resHandler->setKey($key);

	if ($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == 0 ) {
		
		$refund_count = $resHandler->getParameter("refund_count");
		echo "退款笔数: ".$refund_count." <br/>";
		for ($i=0; $i<$refund_count; $i++){
			$refund_state_n = "refund_state_".$i;
		    $out_refund_no_n = "out_refund_no_".$i;
		    $refund_fee_n = "refund_fee_".$i;
			
			echo "第".$i."笔：".$refund_state_n."=".$resHandler->getParameter($refund_state_n).",".$out_refund_no_n."=".$resHandler->getParameter($out_refund_no_n).",".$refund_fee_n."=".$resHandler->getParameter($refund_fee_n)." <br/>";
		}		
	} else {
		//错误时，返回结果可能没有签名，记录retcode、retmsg看失败详情。
		echo "验证签名失败 或 业务错误信息:retcode=" . $resHandler->getParameter("retcode"). ",retmsg=" . $resHandler->getParameter("retmsg") . "<br>";
	}
	
} else {
	//后台调用通信失败
	echo "call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	//有可能因为网络原因，请求已经处理，但未收到应答。
}