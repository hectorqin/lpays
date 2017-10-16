<?php
//---------------------------------------------------
//财付通手机对账单下载示例，商户按照此文档进行开发即可
//---------------------------------------------------
require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
require_once ("classes/DownloadBillRequestHandler.class.php");


/* 商户号 */
$partner = "1900000109";

/* 密钥 */
$key = "8934e7d15453e97507ef794cf7b0519d";


/* 创建支付请求对象 */
$reqHandler = new DownloadBillRequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
$reqHandler->setGateUrl("http://mch.tenpay.com/cgi-bin/mchdown_real_new.cgi");



$httpClient = new TenpayHttpClient();
//应答对象
$resHandler = new ClientResponseHandler();
//----------------------------------------
//设置支付参数 
//----------------------------------------

$reqHandler->setParameter("spid", $partner);  //商户号
$reqHandler->setParameter("trans_time", "2013-09-12");  //下载的交易单的日期
$reqHandler->setParameter("stamp", time());  //当前时间，UNIX时间戳
$reqHandler->setParameter("cft_signtype", "0");  //默认值
$reqHandler->setParameter("mchtype", "0");  //下载对账单类型，0：默认值，返回当日所有订单；

$httpClient->setReqContent($reqHandler->getRequestURL());

echo "The statement URL is : " . "<br/>";
echo "------------------------". "<br/>";
echo $reqHandler->getRequestURL()."<br/>";
//后台调用
if($httpClient->call()) {
	
	    //$resHandler->setContent($httpClient->getResContent());
		//$responseContent=$httpClient->getResContent();对账单内容
		echo "-------------------------------" . "<br/>";
		echo "download statement success!";
		
	}else{
		echo "The background calls communication failure!";
	}
	

?>
