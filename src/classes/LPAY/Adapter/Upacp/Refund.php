<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Upacp;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
use LPAY\Loger;
use LPAY\Pay\RefundNotify;
use LPAY\Utils;
class Refund extends RefundAdapter implements RefundNotify{
	const NAME="upacp";
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
		return array(Pay::NAME);
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
		
		if ($this->_config->get_mode()=='sandbox'){
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfigDev.php');
		}else{
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfig.php');
		}
		
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/common.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/secureUtil.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/httpClient.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/log.class.php');
		
		
		
		
		$msg=$refund_param->get_refund_msg();
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = intval($refund_param->get_refund_pay_money()*100);
		$total_money = intval($refund_param->get_total_pay_money()*100);
		$return_no = $refund_param->get_return_no();
		$notify_url = $this->_config->get_notify_url();
		$merid=$this->_config->get_merid();
		$sdk_sign_cert_path=$this->_config->get_sign_cert_path();
		$sdk_sign_cert_pwd=$this->_config->get_sign_cert_pwd();
		$msg  =  $refund_param->get_refund_msg();
		if (empty($msg))$msg='退款';
		$params = array(
				'version' => '5.0.0',		//版本号
				'encoding' => 'utf-8',		//编码方式
				'certId' =>getCertId ( $sdk_sign_cert_path,$sdk_sign_cert_pwd ),			//证书ID
				'signMethod' => '01',		//签名方法
				'txnType' => '04',		//交易类型
				'txnSubType' => '00',		//交易子类
				'bizType' => '000201',		//业务类型
				'accessType' => '0',		//接入类型
				'channelType' => '07',		//渠道类型
				'orderId' => $return_no,	//商户订单号，重新产生，不同于原消费
				'merId' => $merid,	//商户代码，请修改为自己的商户号
				'origQryId' => $recharge_pay_no,    //原消费的queryId，可以从查询接口或者通知接口中获取
				'txnTime' => date('YmdHis'),	//订单发送时间，重新产生，不同于原消费
				'txnAmt' => $refund_money,              //交易金额，退货总金额需要小于等于原消费
				'backUrl' => $notify_url,	   //后台通知地址
				'reqReserved' =>$msg, //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);
		// 签名
		sign ( $params ,$sdk_sign_cert_path);
		
		$result = sendHttpRequest ( $params, SDK_BACK_TRANS_URL );
	
		//返回结果展示
		$result_arr = coverStringToArray ( $result );
		
		
		$verify_cert_dir=$this->_config->get_verify_cert_dir();
		
		if(!verify ( $result_arr ,$verify_cert_dir)){
			return RefundResult::fail($this->get_name(),$return_no,RefundResult::$sign_invalid);
		}
		if ($result_arr["respCode"] == "00"){
			$out_trade_no=$result_arr[ 'orderId'];
			$trade_no=$result_arr[ 'queryId'];
			return RefundResult::success($this->get_name(),$out_trade_no,$trade_no,$result_arr);
		} else if ($result_arr["respCode"] == "03"
				|| $result_arr["respCode"] == "04"
				|| $result_arr["respCode"] == "05" ){
				$out_trade_no=$result_arr[ 'orderId'];
				$trade_no=$result_arr[ 'queryId'];
				return RefundResult::ing($this->get_name(),$out_trade_no,$result_arr,$trade_no);
		} else {
			return RefundResult::fail($this->get_name(),$return_no,$result_arr["respMsg"]);
		}
	}
	public function refund_notify(){
		ignore_user_abort(true);
		
		if ($this->_config->get_mode()=='sandbox'){
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfigDev.php');
		}else{
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfig.php');
		}
		
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/common.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/secureUtil.php');
		if(!isset($_POST ['signature']))die("fail");
		
		
		$verify_cert_dir=$this->_config->get_verify_cert_dir();
		if(!verify ( $_POST ,$verify_cert_dir)){
			return  RefundResult::unkown($this->get_name(),RefundResult::$sign_invalid);
		}

		Loger::instance(Loger::TYPE_REFUND)->add($this->get_name(),$_POST);
		
		
		if ($_POST["respCode"] == "00"){
			$out_trade_no=$_POST[ 'orderId'];
			$trade_no=$_POST[ 'queryId'];
			$result= RefundResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		} else if ($_POST["respCode"] == "03"
				|| $_POST["respCode"] == "04"
				|| $_POST["respCode"] == "05" ){
					$out_trade_no=$_POST[ 'orderId'];
					$trade_no=$_POST[ 'queryId'];
					$result=  RefundResult::ing($this->get_name(),$out_trade_no,$_POST,$trade_no);
		} else {
			$result=  RefundResult::fail($this->get_name(),$return_no,$_POST["respMsg"]);
		}
		return  $result;
	}
	
	public function refund_notify_output($status=true,$msg=null){
		if ($status){
			http_response_code(200);
			die("success");
		}else{
			http_response_code(500);
			die($msg);
		}
	}
	
}