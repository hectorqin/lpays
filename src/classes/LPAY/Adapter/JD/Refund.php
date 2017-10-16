<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\JD;
use LPAY\Pay\RefundNotify;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
use LPAY\Loger;
use com\jdjr\pay\demo\common\ConfigUtil;
use com\jdjr\pay\demo\common\HttpUtils;
use com\jdjr\pay\demo\common\XMLUtil;

use LPAY\Utils;
class Refund extends RefundAdapter implements RefundNotify{
	/**
	 * @var RefundConfig
	 */
	protected $_config;
	public function __construct(RefundConfig $config){
		$this->_config=$config;
	}
	/**
	 * @return RefundConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	/**
	 * @return string
	 */
	public function support_name(){
		return array(PayWap::NAME,PayWeb::NAME);
	}
	public function enable(){
		return true;
	}
	/**
	 * refund money
	 * @param RefundParam $refund_param
	 * @return RefundResult
	 */
	public function refund(RefundParam $refund_param){
		
		$notify_url=$this->_config->get_notify_url();
		$msg=$refund_param->get_refund_msg();
		$recharge_pay_sn= $refund_param->get_pay_sn();
		$refund_money = strval(intval($refund_param->get_refund_pay_money()*100));
		$total_money = strval(intval($refund_param->get_total_pay_money()*100));
		$return_no = $refund_param->get_return_no();
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
	
		
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/HttpUtils.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/XMLUtil.php');
		
		
		$merchant=$this->_config->get_merchant();
		$deskey=$this->_config->get_deskey();
		$notify_url=$this->_config->get_notify_url();
		ConfigUtil::$config=array(
			'merchantNum' => $merchant,
			'desKey' => $deskey,
			'notifyUrl' =>$notify_url,
		);
		
		
		$param=[];
		$param["version"]='V2.0';
		$param["merchant"]=$merchant;
		$param["currency"]= 'CNY';
		$param["tradeNum"]=$return_no;
		$param["oTradeNum"]=$recharge_pay_sn;
		$param["amount"]=$refund_money;
		$param["tradeTime"]=date("YmdHis");
		$param["notifyUrl"]=$notify_url;
		$param["note"]=$msg;
		
		// 		$param["version"]="V2.0";
		// 		$param["merchant"]="22294531";
		// 		$param["tradeNum"]="20160621121848";
		// 		$param["oTradeNum"]="1466415142002";
		// 		$param["amount"]="1";
		// 		$param["tradeTime"]="20160621122924";
		// 		$param["notifyUrl"]="http://localhost/jdPay2Demo/com/jdjr/pay/demo/action/AsnyNotify.php";
		// 		$param["note"]="";
		// 		$param["currency"]="CNY";
		
		$reqXmlStr = XMLUtil::encryptReqXml($param,$private);
		$url = ConfigUtil::get_val_by_key("refundUrl");
		//echo "请求地址：".$url;
		//echo "----------------------------------------------------------------------------------------------";
		$httputil = new HttpUtils();
		@list ( $return_code, $return_content )  = $httputil->http_post_data($url, $reqXmlStr);
		//echo $return_content."\n";
		$resData;
		$flag=XMLUtil::decryptResXml($return_content,$resData,$public);
		//echo var_dump($resData);
		
		if(!$flag){
			return RefundResult::unkown($this->get_name(),RefundResult::$sign_invalid);
		}
		$status = $resData['status'];
		if($status=="0"){
			return RefundResult::ing($this->get_name(),$return_no,$resData);
		}elseif($status=="1"){
			return RefundResult::success($this->get_name(),$return_no, 'Time:'.$resData['tradeTime'],$resData);
		}elseif ($status=="2"){
			return RefundResult::fail($this->get_name(),$refund_no,jsonencode($resData));
		}
	}
	
	public function refund_notify(){
		ignore_user_abort(true);
		
		require_once Utils::lib_path('jdPay2Demo/utils/ConfigUtil.php');
		require_once Utils::lib_path('jdPay2Demo/com/jdjr/pay/demo/common/XMLUtil.php');
		
		$merchant=$this->_config->get_merchant();
		$notify_url=$this->_config->get_notify_url();
		
		$private=$this->_config->get_private_key_path();
		$public=$this->_config->get_public_key_path();
		$deskey=$this->_config->get_deskey();
		
		ConfigUtil::$config=array(
			'merchantNum' => $merchant,
			'desKey' => $deskey,
			'notifyUrl' =>$notify_url,
		);
		
		$xml=file_get_contents("php://input");
		if(empty($xml)){
			return  RefundResult::unkown($this->get_name(),RefundResult::$sign_invalid);
		}
		$resdata;
		$falg = XMLUtil::decryptResXml($xml, $resdata,$public);
		if(!falg){
			return  RefundResult::unkown($this->get_name(),RefundResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_REFUND)->add($this->support_name(),$xml);
		
		$batch_no=$resdata['tradeNum'];
		$status = $resData['status'];
		if($status=="0"){
			$result=RefundResult::ing($this->get_name(),$batch_no,$resData);
		}elseif($status=="1"){
			$result= RefundResult::success($this->get_name(),$return_no, 'Time:'.$resData['tradeTime'],$resData);
		}elseif ($status=="2"){
			$result=RefundResult::fail($this->get_name(),$refund_no,jsonencode($resData));
		}
		return   $result;
	}
	
	
	public function refund_notify_output($status=true,$msg=null){
		if ($status){
			echo "验签成功";
			die();
		}else{
			http_response_code(500);
			die($msg);
		}
	}
	
}