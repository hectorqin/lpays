<?php
header ( 'Content-type:text/html;charset=utf-8' );

$dir=$_SERVER ['DOCUMENT_ROOT']."/src/libs/upacp_apple_pay/";

include_once $dir. 'sdk/acp_service.php';

/**
 * 多证书demo
 * 【【【尽量和业务部门申请使用证书共享，尽量不要用多证书。】】】
 * 
 */
 
$params = array(
		
		'version' => '5.0.0',		  //版本号
		'encoding' => 'utf-8',		  //编码方式
		'signMethod' => '01',		  //签名方法
		'txnType' => '00',		      //交易类型	
		'txnSubType' => '00',		  //交易子类
		'bizType' => '000000',		  //业务类型
		'accessType' => '0',		  //接入类型
		'channelType' => '07',		  //渠道类型
		'orderId' => date('YmdHis'),  //订单号，演示用
		'merId' => '777290058110048', //商户代码，演示用
		'txnTime' => date('YmdHis'),  //订单发送时间，演示用
		
	);

$cert_path = $dir.'certs/acp_test_sign.pfx';
$cert_pwd = '123456';
com\unionpay\acp\sdk\AcpService::sign ( $params, $cert_path, $cert_pwd ); // 签名
//此地址为示例这里要替换成做具体交易的地址（具体交易地址请查看对应示例中用到的地址）
$url = com\unionpay\acp\sdk\JF_SDK_SINGLE_QUERY_URL;

$result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url);

if(count($result_arr)<=0) { //没收到200应答的情况
	printResult ( $url, $params, array() );
	echo "POST请求失败，具体报错请看下日志。<br>\n" ;
	return;
}

printResult ($url, $params, $result_arr ); //页面打印请求应答数据


$cert_path = $dir.'certs/acp_test_sign.pfx';
$cert_pwd = '000000';
com\unionpay\acp\sdk\AcpService::sign ( $params, $cert_path, $cert_pwd ); // 签名

$result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url);

if(count($result_arr)<=0) { //没收到200应答的情况
	printResult ( $url, $params, array() );
	echo "POST请求失败，具体报错请看下日志。<br>\n" ;
	return;
}

printResult ($url, $params, $result_arr ); //页面打印请求应答数据


/**
 * 打印请求应答
 *
 * @param
 *        	$url
 * @param
 *        	$req
 * @param
 *        	$resp
 */
function printResult($url, $req, $resp) {
	echo "=============<br>\n";
	echo "地址：" . $url . "<br>\n";
	echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $req, false, true ) ) ) . "<br>\n";
	echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $resp , false, true )) ) . "<br>\n";
	echo "=============<br>\n";
}

