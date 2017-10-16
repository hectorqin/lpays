<?php
namespace com\jdjr\pay\demo\action;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use com\jdjr\pay\demo\common\XMLUtil;
include '../common/ConfigUtil.php';
include '../common/HttpUtils.php';
include '../common/XMLUtil.php';
class QueryOrder{
	public function execute(){
		$param["version"]=$_POST["version"];
		$param["merchant"]=$_POST["merchantNum"];
		$param["tradeNum"]=$_POST["tradeNum"];
		$param["oTradeNum"]=$_POST["oTradeNum"];
		$param["tradeType"]=$_POST["tradeType"];
		$queryUrl=ConfigUtil::get_val_by_key("serverQueryUrl");
		$reqXmlStr = XMLUtil::encryptReqXml($param);
		//echo $reqXmlStr."<br/>";
		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($queryUrl, $reqXmlStr);
		//echo $return_content."<br/>";
		$resData1;
		$flag=XMLUtil::decryptResXml($return_content,$resData1);
		if($flag){
			
			if("0"==$param["tradeType"]){
				$htmlStr="";
				$htmlStr=$htmlStr."<br /><lable>版本号:</lable>";
				$htmlStr=$htmlStr."<lable>".$resData1['version']."</lable><br />";
				$htmlStr=$htmlStr."<lable>返回编码:</lable>";
				$htmlStr=$htmlStr."<lable>".$resData1['result']['code']."</lable><br />";
				$htmlStr=$htmlStr."<lable>返回描述:</lable>";
				$htmlStr=$htmlStr."<lable>".$resData1['result']['desc']."</lable><br />";
				$htmlStr=$htmlStr."<lable>商户号:</lable>";
				$htmlStr=$htmlStr."<lable >".$resData1['merchant']."</lable><br />";
				$htmlStr=$htmlStr."<lable>交易流水:</lable>";
				$htmlStr=$htmlStr."<lable >".$resData1['tradeNum']."</lable><br />";
				$htmlStr=$htmlStr."<lable> 交易类型:</lable>";
				$type = $resData1['tradeType'];
				$typeStr="";
				if($type=="0"){
					$typeStr = "消费";
				}
				if($type=="1"){
					$typeStr = "退款";
				}
				$htmlStr=$htmlStr."<lable>".$typeStr."</lable><br />";
				$htmlStr=$htmlStr."<lable> 交易状态：</lable>";
				$htmlStr=$htmlStr."<lable>";
				$status =  $resData1['status'];
				if($status=="0"){
					$htmlStr=$htmlStr."创建";
				}
				if($status=="1"){
					$htmlStr=$htmlStr."处理中";
				}
				if($status=="2"){
					$htmlStr=$htmlStr."成功";
				}
				if($status=="3"){
					$htmlStr=$htmlStr."失败";
				}
				if($status=="4"){
					$htmlStr=$htmlStr."关闭";
				}
				$htmlStr=$htmlStr."</lable><br />";
				
				$payList = $resData1['payList'];
				echo count($payList);
				
				$htmlStr=$htmlStr."<lable> 支付方式: </lable>";
				$tempPayType = $payList["pay"]["payType"];
				$htmlStr=$htmlStr."<lable>";
				if ("0"==$tempPayType)
				{
					$htmlStr=$htmlStr."银行卡";
				}
				else if ("1"==$tempPayType)
				{
					$htmlStr=$htmlStr."白条";
				}
				$htmlStr=$htmlStr."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 交易金额: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["amount"]."分"."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 交易单位: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["currency"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 交易时间: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["tradeTime"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 持卡人姓名: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["detail"]["cardHolderName"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 证件类型: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["detail"]["cardHolderType"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 身份证号: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["detail"]["cardHolderId"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 卡号: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["detail"]["cardNo"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 银行编码: </lable>";
				$htmlStr=$htmlStr."<lable>".$payList["pay"]["detail"]["bankCode"]."</lable>";
				$htmlStr=$htmlStr."<br/>";
				$htmlStr=$htmlStr."<lable> 银行卡类型: </lable>";
				$tempcardType = $payList["pay"]["detail"]["cardType"];
				$htmlStr=$htmlStr."<lable>";
				if ("1"==$tempcardType)
				{
					$htmlStr=$htmlStr."借记卡";
				}
				else if ("2"==tempcardType)
				{
					$htmlStr=$htmlStr."信用卡";
				}
				$htmlStr=$htmlStr."</lable>";
				$htmlStr=$htmlStr."<br/>";
// 				for($i=0;$i<count($payList);$i++){
					
// 				}
				echo $htmlStr;
				$_SESSION['subhtml']=$htmlStr;
				$_SESSION['refund']=$resData1;
				$jsonStr=json_encode($resData1);
				$_SESSION['jsonStr']=$jsonStr;
				header("location:../page/queryResult.php");
			}else{
				
				$_SESSION['refund']=$resData1;
				$jsonStr=json_encode($resData1);
				$_SESSION['jsonStr']=$jsonStr;
				header("location:../page/queryFefundResult.php");
			}
		}else{
			echo "验签失败";
		}
	}
}
error_reporting(0);
$m=new QueryOrder();
$m->execute();
?>