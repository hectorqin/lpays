<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Baidu;
use LPAY\Pay\RefundNotify;

use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
use LPAY\Loger;
use LPAY\Utils;
class Refund extends RefundAdapter implements RefundNotify{
	/**
	 * @var RefundConfig
	 */
	protected $_config;
	/**
	 * @var \bdpay_sdk
	 */
	protected $_pay_sdk;
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
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = intval($refund_param->get_refund_pay_money()*100);
		$total_money = intval($refund_param->get_total_pay_money()*100);
		$return_no = $refund_param->get_return_no();
		
		
		require_once Utils::lib_path("bdpayrefundphp/bdpay_sdk.php");
		require_once Utils::lib_path("bdpayrefundphp/bdpay_refund.cfg.php");
		\sp_conf::$config=array(
				'sp_no'=>$this->_config->get_sp_no(),
				'key_file'=>$this->_config->get_key_file()
		);
		
		$bdpay_sdk = new \bdpay_sdk();
		/*
		 *refund.html页面获取的参数
		 */
		
		$output_type =1;
		$output_charset = 1;
		$return_url = $notify_url;
		$sp_refund_no = $return_no;
		$order_no = $recharge_pay_no;
		$return_method= 1;
		$cashback_amount = $refund_money;
		$cashback_time= date("YmdHis");
		
		// 用于测试的商户请求退款接口的表单参数，具体的表单参数各项的定义和取值参见接口文档
		$params = array (
				'service_code' => \sp_conf::BFB_REFUND_INTERFACE_SERVICE_ID,
				'input_charset' => \sp_conf::BFB_INTERFACE_ENCODING,
				'sign_method' => \sp_conf::SIGN_METHOD_MD5,
				'output_type' => $output_type,
				'output_charset' => $output_charset,
				'return_url' => $return_url,
				'return_method' => $return_method,
				'version' =>  \sp_conf::BFB_INTERFACE_VERSION,
				'sp_no' => \sp_conf::SP_NO(),
				'order_no'=>$order_no,
				'cashback_amount' => $cashback_amount,
				'cashback_time' => $cashback_time,
				'currency' => \sp_conf::BFB_INTERFACE_CURRENTCY,
				'sp_refund_no' => $sp_refund_no
		);
		
		$refund_url = $bdpay_sdk->create_baifubao_Refund_url($params, \sp_conf::BFB_REFUND_URL);
		
		$retry = 0;
		while (empty($content) && $retry < \sp_conf::BFB_QUERY_RETRY_TIME) {
			$content = $bdpay_sdk->request($refund_url);
			$retry++;
		}
		if (empty($content)) {
			return RefundResult::fail($this->get_name(),$return_no, 'call baidu api fail');
		}
		$response_arr = json_decode(
				json_encode(simplexml_load_string($content)), true);
		// 上句解析xml文件时，如果某字段没有取值时，会被解析成一个空的数组，对于没有取值的情况，都默认设为空字符串
		foreach ($response_arr as &$value) {
			if (empty($value) && is_array($value)) {
				$value = '';
			}
		}
		unset($value);
		return RefundResult::ing($this->get_name(),$return_no,$response_arr);
	}
	
	public function refund_notify(){
		ignore_user_abort(true);
		
		require_once Utils::lib_path("bdpayrefundphp/bdpay_sdk.php");
		require_once Utils::lib_path("bdpayrefundphp/bdpay_refund.cfg.php");
		\sp_conf::$config=array(
			'sp_no'=>$this->_config->get_sp_no(),
			'key_file'=>$this->_config->get_key_file()
		);
		
		$this->_pay_sdk=$bdpay_sdk = new \bdpay_sdk();
		
		if (false === $bdpay_sdk->check_bfb_refund_result_notify()) {
			return  RefundResult::unkown($this->get_name(),RefundResult::$sign_invalid);
		}
		
		Loger::instance(Loger::TYPE_REFUND)->add($this->get_name(),$_POST);
		
// 		bfb_order_no 2014081290001000051110157533474 百度钱包交易号
// 		cashback_amount 1 退款金额，以分为单位
// 		order_no 20140814170227451966 外部商户交易号
// 		ret_code 1 退款结果
// 		ret_detail 退款详情为空
// 		sp_no 9000100005 外部商户号
// 		sp_refund_no 201408141703270 外部商户退款流水号
// 		sign 0e423b4d2cc13767b74e19287dedc650 签名结果
// 		sign_method
		
		$batch_no=@$_POST['sp_refund_no'];
		$dbref=@$_POST['bfb_order_no'];
		
		$result=RefundResult::success($this->get_name(),$batch_no,$dbref,$_POST);
		return  $result;
	}
	
	/**
	 * pay notify
	 */
	public function refund_notify_output($status=true,$msg=null){
		if($this->_pay_sdk)die('fail');
		if ($status){
			$this->_pay_sdk->notify_bfb();
			die();
		}else{
			http_response_code(500);
			die($msg);
		}
	}
	
}