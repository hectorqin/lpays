<?php
//---------------------------------------------------------
//财付通退款后台调用示例，商户按照此文档进行开发即可
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

//通信对象
$httpClient = new TenpayHttpClient();

//应答对象
$resHandler = new ClientResponseHandler();

//-----------------------------
//设置请求参数
//-----------------------------
$reqHandler->init();
$reqHandler->setKey($key);

$reqHandler->setGateUrl("https://mch.tenpay.com/refundapi/gateway/refund.xml");
$reqHandler->setParameter("partner", $partner);

//out_trade_no和transaction_id至少一个必填，同时存在时transaction_id优先
$reqHandler->setParameter("out_trade_no", "201309170922391065");
$reqHandler->setParameter("transaction_id", "1900000109201309179356184750");
//必须保证全局唯一，同个退款单号财付通认为是同笔请求
$reqHandler->setParameter("out_refund_no", "2013091700002");
$reqHandler->setParameter("total_fee", "1");
$reqHandler->setParameter("refund_fee", "1");
$reqHandler->setParameter("op_user_id", "1900000109");
//操作员密码,MD5处理
$reqHandler->setParameter("op_user_passwd", md5("111111"));		
//接口版本号,取值1.1
$reqHandler->setParameter("service_version", "1.1");


//-----------------------------
//设置通信参数
//-----------------------------
//设置PEM证书，pfx证书转pem方法：openssl pkcs12 -in 2000000501.pfx  -out 2000000501.pem
//证书必须放在用户下载不到的目录，避免证书被盗取
$httpClient->setCertInfo("E:\\1900000109.pem", "1900000109");
//设置CA
$httpClient->setCaInfo("E:\\cacert.pem");
$httpClient->setTimeOut(5);
$httpClient->setMethod("POST"); 
//设置请求内容
$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call()) {
	//设置结果参数
	$resHandler->setContent($httpClient->getResContent());
	$resHandler->setKey($key);

	//判断签名及结果
	//只有签名正确并且retcode为0才是请求成功
	if($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == 0 ) {
		//取结果参数做业务处理
		//商户订单号
		$out_trade_no = $resHandler->getParameter("out_trade_no");	
		//财付通订单号
		$transaction_id = $resHandler->getParameter("transaction_id");	
		//商户退款单号
		$out_refund_no = $resHandler->getParameter("out_refund_no");	
		//财付通退款单号
		$refund_id = $resHandler->getParameter("refund_id");	
		//退款金额,以分为单位
		$refund_fee = $resHandler->getParameter("refund_fee");		
		//退款状态
		$refund_status = $resHandler->getParameter("refund_status");
		
		
		
		echo "OK,refund_status=" . $refund_status . ",out_refund_no=" . $resHandler->getParameter("out_refund_no") . ",refund_fee=" . $resHandler->getParameter("refund_fee") . "<br>";
		
		
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


 <!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN"
 "http://www.wapforum.org/DTD/wml_1.1.xml">
   <wml>
     <head>
       <meta http-equiv="Cache-Control" content="max-age=0" forua="true"/>
       <meta http-equiv="Cache-control" content="must-revalidate" />
       <meta http-equiv="Cache-control" content="private" />
       <meta http-equiv="Cache-control" content="no-cache" />
     </head>
     <card id="wappay" title="财付通wap手机退款示例">
     
   </wml>
