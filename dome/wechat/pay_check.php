<?php
use LPAY\Pay\PayRender;
use LPAY\Pay\QueryParam;
use LPAY\PayUtils\Pay;
use LPAY\Pay\PayResult;
include __DIR__."/../Bootstarp.php";
//验证登录等...
$order=PayRender::qrcode_get_sn();
if (!$order)PayRender::qrcode_output(false);

/*检查数据库记录是否被支付*/
$status=false;
//未被支付,调用API 检查支付状态
if (!$status){
	include_once './WxPay.Config.php';
	$pay=Pay::wechat_pc(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());
	$param=new QueryParam($order, null, null);
	$pstatus=$pay->query($param);
	if($pstatus->get_status()==PayResult::STATUS_SUCC){
		$status=true;
		ob_start();
		result_callback($pstatus);//支付成功,回写数据库
		ob_end_clean();
	}
}
if($status){
	PayRender::qrcode_output(true);
}else{
	PayRender::qrcode_output(false,'发生错误');	
}