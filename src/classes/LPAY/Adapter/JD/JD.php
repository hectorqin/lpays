<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\JD;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Adapter\PayAdapter;
use LPAY\Utils;
use LPAY\Pay\PayParam;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Loger;
use LPAY\Pay\PayRender;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\TDESUtil;
use com\jdjr\pay\demo\common\SignUtil;
use com\jdjr\pay\demo\common\XMLUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use LPAY\Pay\Query;
use LPAY\Pay\QueryParam;
use com\jdjr\pay\demo\common\RSAUtils;

use LPAY\Pay\PayAdapterNotify;
abstract class JD extends PayAdapter implements PayAdapterCallback,PayAdapterNotify,Query{
	/**
	 * @var PayConfig
	 */
	protected $_config;
	protected $_pay_url;
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	/**
	 * @return PayConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function enable(){
		return true;
	}
	public function pay_render(PayParam $pay_param){
		$merchant=$this->_config->get_merchant();
		$device=$this->_config->get_device();
		$deskey=$this->_config->get_deskey();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
		
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/SignUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/TDESUtil.php');
		
		
		ConfigUtil::$config=array(
			'merchantNum' => $merchant,
			'desKey' => $deskey,
			'callbackUrl' => $return_url,
			'notifyUrl' =>$notify_url,
		);
		
		
		$show_url=$pay_param->get_show_url();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=intval($pay_param->get_pay_money()*100);
		$subject=$pay_param->get_title();
		$body=$pay_param->get_body();
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		
		
		$ctime=$pay_param->get_create_time();
		
		
		$param=[];
		$param["version"]='V2.0';
		$param["merchant"]=$merchant;
		$param["device"]=strval($device);
		$param["tradeNum"]=$out_trade_no;
		$param["tradeName"]=$subject;
		$param["tradeDesc"]=$body;
		$param["tradeTime"]= date("YmdHis",$ctime);
		$param["amount"]= strval($total_fee);
		$param["currency"]= 'CNY';
		$param["note"]= '';
		
		$param["callbackUrl"]= $return_url;
		$param["notifyUrl"]= $notify_url;
		$param["ip"]=Utils::client_ip();
		$param["specCardNo"]= '';
		$param["specId"]= '';
		$param["specName"]= '';
		$param["userType"]= '';
		$param["userId"]= '';
		$param["expireTime"]= strval($timeout-time());
		$param["orderType"]= '0';
		$param["industryCategoryCode"]= '';
		
		$unSignKeyList = array ("sign");
		$oriUrl = $this->_pay_url;
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$sign = SignUtil::signWithoutToHex($param, $unSignKeyList,$private);
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
		
		//print_r($param);
		//exit;
		
		ob_start();
		require_once Utils::lib_path("jdPay2Demo/utils/pay.php");
		$html=ob_get_contents();
		ob_end_clean();
		return new PayRender(PayRender::OUT_HTML, $html);
	}
	public function pay_callback(){
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/SignUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/TDESUtil.php');
		$merchant=$this->_config->get_merchant();
		$deskey=$this->_config->get_deskey();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
		ConfigUtil::$config=array(
				'merchantNum' => $merchant,
				'desKey' => $deskey,
				'callbackUrl' => $return_url,
				'notifyUrl' =>$notify_url,
		);
		
		
		if (!isset($_POST["tradeNum"])
			||!isset($_POST["amount"])
			||!isset($_POST["currency"])
			||!isset($_POST["tradeTime"])
			||!isset($_POST["note"])
			||!isset($_POST["status"])
			||!isset($_POST["sign"])
			){
				return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		
		
		$desKey = ConfigUtil::get_val_by_key("desKey");
		$keys = base64_decode($desKey);
		$param=[];
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
		//echo "strSourceData=".htmlspecialchars($strSourceData)."<br/>";
		//$decryptBASE64Arr = base64_decode($sign);
		$decryptStr = RSAUtils::decryptByPublicKey($sign,$public);
		//echo "decryptStr=".htmlspecialchars($decryptStr)."<br/>";
		$sha256SourceSignString = hash ( "sha256", $strSourceData);
		//echo "sha256SourceSignString=".htmlspecialchars($sha256SourceSignString)."<br/>";
		if($decryptStr!=$sha256SourceSignString){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
			//echo "验证签名失败！";
		}else{
		//	$param;
		}
		
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$param);
		// 查询订单在商户自己系统的状态
		$out_trade_no=$param["tradeNum"];
		$trade_no='Time:'.$param["tradeTime"];
		$buyer_email='';
		$total_fee=$param['amount']/100;
		if ($param["status"]=='0'){
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$param);
			$result->set_money($total_fee)->set_pay_account($buyer_email);
		}else{
			$result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,'unkown error');
		}
		return $result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/XMLUtil.php');
		
		
		$merchant=$this->_config->get_merchant();
		$deskey=$this->_config->get_deskey();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
		ConfigUtil::$config=array(
				'merchantNum' => $merchant,
				'desKey' => $deskey,
				'callbackUrl' => $return_url,
				'notifyUrl' =>$notify_url,
		);
		
		$xml=file_get_contents("php://input");
		if(empty($xml)){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		$resdata;
		$falg = XMLUtil::decryptResXml($xml, $resdata,$public);
		if(!falg){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$xml);
		
		$out_trade_no=$resdata['tradeNum'];
		$trade_no='Time:'.@$resdata['payList']['pay']['tradeTime'];
		
		if($resdata['result']['code']!='000000'){
			$result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$resdata['result']['desc']);
		}else{
			if (isset($resdata['payList']['pay']['detail']['cardNo'])){
				$buyer_email=$resdata['payList']['pay']['detail']['cardNo'].":".$resdata['payList']['pay']['detail']['cardHolderName'];
			}else{
				$buyer_email='';
			}
			$total_fee=$resdata['amount']/100;
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_GET);
			$result->set_money($total_fee)->set_pay_account($buyer_email);
		}
		return  $result;
	}
	public function pay_notify_output($status=true,$msg=null){
		if ($status){
			echo "验签成功";
			die();
		}else{
			http_response_code(500);
			die($msg);
		}
	}
	
	public function query(QueryParam $param){
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/HttpUtils.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/XMLUtil.php');
		$merchant=$this->_config->get_merchant();
		$deskey=$this->_config->get_deskey();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
		ConfigUtil::$config=array(
				'merchantNum' => $merchant,
				'desKey' => $deskey,
				'callbackUrl' => $return_url,
				'notifyUrl' =>$notify_url,
		);
		
		$pay_sn=$param->get_pay_sn();
		
		
		$param["version"]='V2.0';
		$param["merchant"]=$merchant;
		$param["tradeNum"]=Utils::snno_create('S');
		$param["oTradeNum"]=$pay_sn;
		$param["tradeType"]=0;
		$queryUrl=ConfigUtil::get_val_by_key("serverQueryUrl");
		$reqXmlStr = XMLUtil::encryptReqXml($param,$private);
		//echo $reqXmlStr."<br/>";
		$httputil = new HttpUtils();
		list ( $return_code, $return_content )  = $httputil->http_post_data($queryUrl, $reqXmlStr);
		//echo $return_content."<br/>";
		$resData1;
		$flag=XMLUtil::decryptResXml($return_content,$resData1);
		if(!$flag){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		if("0"!=$param["tradeType"]){
			return PayResult::unkown($this->get_name(),json_encode($resData1));
		}
		$status =  $resData1['status'];
		if($status=="0"){
			return PayResult::ing($this->get_name(),$pay_sn,$resData1);
		}
		if($status=="1"){
			return PayResult::ing($this->get_name(),$pay_sn,$resData1);
		}
		if($status=="2"){
			$trade_no='Time:'.@$resData1['payList']['pay']['tradeTime'];
			return PayResult::success($this->get_name(),$pay_sn, $trade_no,$resData1);
		}
		if($status=="3"){
			$trade_no='Time:'.@$resData1['payList']['pay']['tradeTime'];
			return PayResult::fail($this->get_name(),$pay_sn, $trade_no,@$resData1['result']['desc']);
		}
		return PayResult::unkown($this->get_name(),json_encode($resData1));
	}
}