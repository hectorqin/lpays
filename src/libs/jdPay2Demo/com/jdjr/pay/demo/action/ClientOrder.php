<?php
namespace com\jdjr\pay\demo\action;

use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\SignUtil;
use com\jdjr\pay\demo\common\TDESUtil;
include '../common/ConfigUtil.php';
include '../common/SignUtil.php';
include '../common/TDESUtil.php';
class ClientOrder{
	public function execute(){
		session_start();
		$param;
		$param["version"]=$_POST["version"];
		$param["merchant"]=$_POST["merchant"];
		$param["device"]=$_POST["device"];
		$param["tradeNum"]=$_POST["tradeNum"];
		$param["tradeName"]=$_POST["tradeName"];
		$param["tradeDesc"]=$_POST["tradeDesc"];
		$param["tradeTime"]= $_POST["tradeTime"];
		$param["amount"]= $_POST["amount"];
		$param["currency"]= $_POST["currency"];
		$param["note"]= $_POST["note"];
		
		$param["callbackUrl"]= $_POST["callbackUrl"];
		$param["notifyUrl"]= $_POST["notifyUrl"];
		$param["ip"]= $_POST["ip"];
		$param["specCardNo"]= $_POST["specCardNo"];
		$param["specId"]= $_POST["specId"];
		$param["specName"]= $_POST["specName"];
		$param["userType"]= $_POST["userType"];
		$param["userId"]= $_POST["userId"];
		$param["expireTime"]= $_POST["expireTime"];
		$param["orderType"]= $_POST["orderType"];
		$param["industryCategoryCode"]= $_POST["industryCategoryCode"];
		$unSignKeyList = array ("sign");
		$oriUrl = $_POST["saveUrl"];
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$sign = SignUtil::signWithoutToHex($param, $unSignKeyList);
		//echo $sign."<br/>";
		$param["sign"] = $sign;
		$keys = base64_decode($desKey);
		
		if($param["device"] != null && $param["device"]!=""){
			$param["device"]=TDESUtil::encrypt2HexStr($keys, $param["device"]);
		}
		$param["tradeNum"]=TDESUtil::encrypt2HexStr($keys, $param["tradeNum"]);
		if($param["tradeName"] != null && $param["tradeName"]!=""){
			$param["tradeName"]=TDESUtil::encrypt2HexStr($keys, $param["tradeName"]);
		}
		if($param["tradeDesc"] != null && $param["tradeDesc"]!=""){
			$param["tradeDesc"]=TDESUtil::encrypt2HexStr($keys, $param["tradeDesc"]);
		}
		
		$param["tradeTime"]=TDESUtil::encrypt2HexStr($keys, $param["tradeTime"]);
		$param["amount"]=TDESUtil::encrypt2HexStr($keys, $param["amount"]);
		$param["currency"]=TDESUtil::encrypt2HexStr($keys, $param["currency"]);
		$param["callbackUrl"]=TDESUtil::encrypt2HexStr($keys, $param["callbackUrl"]);
		$param["notifyUrl"]=TDESUtil::encrypt2HexStr($keys, $param["notifyUrl"]);
		$param["ip"]=TDESUtil::encrypt2HexStr($keys, $param["ip"]);
		
		
		
		if($param["note"] != null && $param["note"]!=""){
			$param["note"]=TDESUtil::encrypt2HexStr($keys, $param["note"]);
		}
		if($param["userType"] != null && $param["userType"]!=""){
			$param["userType"]=TDESUtil::encrypt2HexStr($keys, $param["userType"]);
		}
		if($param["userId"] != null && $param["userId"]!=""){
			$param["userId"]=TDESUtil::encrypt2HexStr($keys, $param["userId"]);
		}
		if($param["expireTime"] != null && $param["expireTime"]!=""){
			$param["expireTime"]=TDESUtil::encrypt2HexStr($keys, $param["expireTime"]);
		}
		if($param["orderType"] != null && $param["orderType"]!=""){
			$param["orderType"]=TDESUtil::encrypt2HexStr($keys, $param["orderType"]);
		}
		if($param["industryCategoryCode"] != null && $param["industryCategoryCode"]!=""){
			$param["industryCategoryCode"]=TDESUtil::encrypt2HexStr($keys, $param["industryCategoryCode"]);
		}
		if($param["specCardNo"] != null && $param["specCardNo"]!=""){
			$param["specCardNo"]=TDESUtil::encrypt2HexStr($keys, $param["specCardNo"]);
		}
		if($param["specId"] != null && $param["specId"]!=""){
			$param["specId"]=TDESUtil::encrypt2HexStr($keys, $param["specId"]);
		}
		if($param["specName"] != null && $param["specName"]!=""){
			$param["specName"]=TDESUtil::encrypt2HexStr($keys, $param["specName"]);
		}
		print_r($param);exit;
		$_SESSION['param'] = $param;
		$_SESSION['payUrl'] = $oriUrl;
		header("location:../page/autoSubmit.php");
	}
	
}
error_reporting(0);
$m = new ClientOrder();
$m->execute();

?>