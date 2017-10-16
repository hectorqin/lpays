<?php 
namespace com\jdjr\pay\demo\action;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use com\jdjr\pay\demo\common\XMLUtil;
include '../common/ConfigUtil.php';
include '../common/HttpUtils.php';
include '../common/XMLUtil.php';
class RefundOrder{
	public function execute(){
		
		
		$param["version"]=$_POST["version"];
		$param["merchant"]=$_POST["merchant"];
		$param["tradeNum"]=$_POST["tradeNum"];
		$param["oTradeNum"]=$_POST["oTradeNum"];
		$param["amount"]=$_POST["amount"];
		$param["tradeTime"]=$_POST["tradeTime"];
		$param["notifyUrl"]=$_POST["notifyUrl"];
		$param["note"]=$_POST["note"];
		$param["currency"]=$_POST["currency"];
		
// 		$param["version"]="V2.0";
// 		$param["merchant"]="22294531";
// 		$param["tradeNum"]="20160621121848";
// 		$param["oTradeNum"]="1466415142002";
// 		$param["amount"]="1";
// 		$param["tradeTime"]="20160621122924";
// 		$param["notifyUrl"]="http://localhost/jdPay2Demo/com/jdjr/pay/demo/action/AsnyNotify.php";
// 		$param["note"]="";
// 		$param["currency"]="CNY";
		
		$reqXmlStr = XMLUtil::encryptReqXml($param);
		$url = ConfigUtil::get_val_by_key("refundUrl");
		//echo "请求地址：".$url;
		//echo "----------------------------------------------------------------------------------------------";
		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($url, $reqXmlStr);
		//echo $return_content."\n";
		$resData;
		$flag=XMLUtil::decryptResXml($return_content,$resData);
		//echo var_dump($resData);
		
		if($flag){
			
			$status = $resData['status'];
			if($status=="0"){
				$resData['status']="处理中";
			}elseif($status=="1"){
				$resData['status']="成功";
			}elseif ($status=="2"){
				$resData['status']="失败";
			}
			$_SESSION["refund"]=$resData;
			header("location:../page/refundResult.php");
		}else{
			echo "验签失败";
		}
	}
	
	
}
error_reporting(0);
$m = new RefundOrder();
$m->execute();
?>