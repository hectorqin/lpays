<?php
namespace com\jdjr\pay\demo\action;

use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\TDESUtil;
use com\jdjr\pay\demo\common\SignUtil;
use com\jdjr\pay\demo\common\RSAUtils;
include '../common/ConfigUtil.php';
include '../common/TDESUtil.php';
include '../common/SignUtil.php';

class CallBack{
	public function execute(){
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$keys = base64_decode($desKey);
		$param;
		if($_POST["tradeNum"] != null && $_POST["tradeNum"]!=""){
			$param["tradeNum"]=TDESUtil::decrypt4HexStr($keys, $_POST["tradeNum"]);
		}
		if($_POST["amount"] != null && $_POST["amount"]!=""){
			$param["amount"]=TDESUtil::decrypt4HexStr($keys, $_POST["amount"]);
		}
		if($_POST["currency"] != null && $_POST["currency"]!=""){
			$param["currency"]=TDESUtil::decrypt4HexStr($keys, $_POST["currency"]);
		}
		if($_POST["tradeTime"] != null && $_POST["tradeTime"]!=""){
			$param["tradeTime"]=TDESUtil::decrypt4HexStr($keys, $_POST["tradeTime"]);
		}
		if($_POST["note"] != null && $_POST["note"]!=""){
			$param["note"]=TDESUtil::decrypt4HexStr($keys, $_POST["note"]);
		}
		if($_POST["status"] != null && $_POST["status"]!=""){
			$param["status"]=TDESUtil::decrypt4HexStr($keys, $_POST["status"]);
		}
		
		$sign =  $_POST["sign"];
		$strSourceData = SignUtil::signString($param, array());
		echo "strSourceData=".htmlspecialchars($strSourceData)."<br/>";
		//$decryptBASE64Arr = base64_decode($sign);
		$decryptStr = RSAUtils::decryptByPublicKey($sign);
		echo "decryptStr=".htmlspecialchars($decryptStr)."<br/>";
		$sha256SourceSignString = hash ( "sha256", $strSourceData);
		echo "sha256SourceSignString=".htmlspecialchars($sha256SourceSignString)."<br/>";
		if($decryptStr!=$sha256SourceSignString){
			echo "验证签名失败！";
		}else{
			$_SESSION["tradeResultRes"]=$param;
			header("location:../page/success.php");
		}
		
	}
	
	
}
error_reporting(0);
$m = new CallBack();
$m->execute();
?>