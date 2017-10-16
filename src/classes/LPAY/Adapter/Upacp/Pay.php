<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Upacp;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Adapter\PayAdapter;
use LPAY\Utils;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Loger;

use LPAY\Pay\PayRender;
use LPAY\Pay\QueryParam;
use LPAY\Pay\PayParam;
use LPAY\Pay\Query;
use LPAY\Pay\PayAdapterNotify;
class Pay extends PayAdapter implements PayAdapterCallback,PayAdapterNotify,Query{
	const NAME="lpay_upacp";
	/**
	 * @var PayConfig
	 */
	protected $_config;
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
		$config=$this->_config;
		if(empty($config->get_merid())) return false;
		return true;
	}
	public function support_type(){
		return \LPAY\Pay::TYPE_PC|\LPAY\Pay::TYPE_WAP|\LPAY\Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return Pay::NAME;
	}
	public function match($name){
		if ($name==Pay::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		
		
		$merid=$this->_config->get_merid();
		$sdk_sign_cert_path=$this->_config->get_sign_cert_path();
		$sdk_sign_cert_pwd=$this->_config->get_sign_cert_pwd();
		
		
		$show_url=$pay_param->get_show_url();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=intval($pay_param->get_pay_money()*100);
		$subject=$pay_param->get_title();
		$body=$pay_param->get_body();
		
		$ctime=$pay_param->get_create_time();
		
		
		if ($this->_config->get_mode()=='sandbox'){
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfigDev.php');
		}else{
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfig.php');
		}
		
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/common.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/secureUtil.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/log.class.php');
		
		
		
		/**
		 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
		 */
		// 初始化日志
		$params = array(
			'version' => '5.0.0',				//版本号
			'encoding' => 'utf-8',				//编码方式
			'certId' => getCertId ( $sdk_sign_cert_path,$sdk_sign_cert_pwd ),			//证书ID
			'txnType' => '01',				//交易类型
			'txnSubType' => '01',				//交易子类
			'bizType' => '000201',				//业务类型
			'frontUrl' =>  $return_url,  		//前台通知地址
			'backUrl' => $notify_url,		//后台通知地址
			'signMethod' => '01',		//签名方法
			'channelType' => '08',		//渠道类型，07-PC，08-手机
			'accessType' => '0',		//接入类型
			'merId' => $merid,		        //商户代码，请改自己的测试商户号
			'orderId' => $out_trade_no,	//商户订单号
			'txnTime' => date('YmdHis',$ctime),	//订单发送时间
			'txnAmt' => strval($total_fee),		//交易金额，单位分
			'currencyCode' => '156',	//交易币种
			'defaultPayType' => '0001',	//默认支付方式
			'orderDesc' => $subject,  //订单描述，网关支付和wap支付暂时不起作用
			'reqReserved' =>$body, //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);
		
		// 签名
		sign ( $params,$sdk_sign_cert_path );
		
		// 前台请求地址
		$front_uri = SDK_FRONT_TRANS_URL;
		// 构造 自动提交的表单
		$html_form = create_html ( $params, $front_uri );
		return new PayRender(PayRender::OUT_HTML, $html_form);
	}
	protected function _verify(){
		if ($this->_config->get_mode()=='sandbox'){
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfigDev.php');
		}else{
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfig.php');
		}
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/common.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/secureUtil.php');
		if(!isset($_POST ['signature']))return false;
		
		$verify_cert_dir=$this->_config->get_verify_cert_dir();
		
		return verify ( $_POST,$verify_cert_dir );
	}
	public function pay_callback(){
		if(!$this->_verify()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_POST);
		if(@$_POST['respCode']!='00'){
			return PayResult::unkown($this->get_name(),@$_POST['respMsg']);
		}
		$out_trade_no=$_POST[ 'orderId'];
		$trade_no=$_POST[ 'queryId'];
		$accNo=$_POST[ 'accNo'];
		$money=$_POST[ 'txnAmt'];
		$money=$money/100;
		$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		$result->set_money($money)->set_pay_account($accNo);
		return $result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		if(!$this->_verify()){
			return  PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$_POST);
		if(@$_POST['respCode']!='00'){
			return  PayResult::unkown($this->get_name(),@$_POST['respMsg']);
		}
		$out_trade_no=$_POST[ 'orderId'];
		$trade_no=$_POST[ 'queryId'];
		$accNo=$_POST[ 'accNo'];
		$money=$_POST[ 'txnAmt'];
		$money=$money/100;
		$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		$result->set_money($money)->set_pay_account($accNo);
		return   $result;
	}
	
	public function pay_notify_output($status=true,$msg=null){
		if ($status){
			http_response_code(200);
			die("success");		//请不要修改或删除
		}else{
			http_response_code(500);
			echo "fail";
			die();
		}
	}
	
	public function query(QueryParam $param){
		if ($this->_config->get_mode()=='sandbox'){
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfigDev.php');
		}else{
			require_once Utils::lib_path('upacp_sdk_php/utf8/func/SDKConfig.php');
		}
		
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/common.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/secureUtil.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/httpClient.php');
		require_once Utils::lib_path('upacp_sdk_php/utf8/func/log.class.php');
		
		
		$merid=$this->_config->get_merid();
		$sdk_sign_cert_path=$this->_config->get_sign_cert_path();
		$sdk_sign_cert_pwd=$this->_config->get_sign_cert_pwd();
		
		$out_trade_no=$param->get_pay_sn();
		$ctime=$param->get_create_time();
		
		$params = array(
				'version' => '5.0.0',		//版本号
				'encoding' => 'utf-8',		//编码方式
				'certId' => getCertId ( $sdk_sign_cert_path,$sdk_sign_cert_pwd ),			//证书ID
				'signMethod' => '01',		//签名方法
				'txnType' => '00',		//交易类型
				'txnSubType' => '00',		//交易子类
				'bizType' => '000000',		//业务类型
				'accessType' => '0',		//接入类型
				'channelType' => '07',		//渠道类型
				'orderId' => $out_trade_no,	//商户订单号
				'merId' => $merid,		        //商户代码，请改自己的测试商户号
				'txnTime' => date('YmdHis',$ctime),	//订单发送时间
		);

		// 签名
		sign ( $params,$sdk_sign_cert_path );
		
		$result = sendHttpRequest ( $params, SDK_SINGLE_QUERY_URL );
		//返回结果展示
		$result_arr = coverStringToArray ( $result );
	
		$verify_cert_dir=$this->_config->get_verify_cert_dir();
		
		if (!verify ( $result_arr ,$verify_cert_dir)){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		if ($result_arr["respCode"] == "00"){
			if ($result_arr["origRespCode"] == "00"){
				$out_trade_no=$result_arr[ 'orderId'];
				$trade_no=$result_arr[ 'queryId'];
				return PayResult::success($this->get_name(),$out_trade_no, $trade_no,$result_arr)
					->set_money($result_arr['txnAmt']/100)
					->set_pay_account($result_arr['accNo']);
			} else if ($result_arr["origRespCode"] == "03"
					|| $result_arr["origRespCode"] == "04"
					|| $result_arr["origRespCode"] == "05"){
						$out_trade_no=$result_arr[ 'orderId'];
						$trade_no=@$result_arr[ 'queryId'];
						return PayResult::ing($this->get_name(),$out_trade_no,$result_arr,$trade_no);
			} else {
				$out_trade_no=$result_arr[ 'orderId'];
				$trade_no=@$result_arr[ 'queryId'];
				return PayResult::fail($this->get_name(),$out_trade_no, $trade_no, $result_arr["origRespMsg"]);
			}
		} else if ($result_arr["respCode"] == "03"
				|| $result_arr["respCode"] == "04"
				|| $result_arr["respCode"] == "05" ){
					return PayResult::unkown($this->get_name(),'Try later');
		} else {
			return PayResult::unkown($this->get_name(),$result_arr["respMsg"]);
		}
	}
	
}