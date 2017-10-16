<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Pay\PayAdapterCallback;

use LPAY\Pay\PayResult;
use LPAY\Result;
use LPAY\Loger;
use LPAY\Exception;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
class PayWap extends Alipay implements PayAdapterCallback{
	const NAME="lpay_alipay_wap";
	/**
	 * @var PayConfig
	 */
	protected $_config;
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
		$this->_config->set_0001();
	}
	public function enable(){
		$alipay_config=$this->_config->as_array();
		$check=array('partner','private_key_path','ali_public_key_path');
		foreach ($check as $v){
			if(empty($alipay_config[$v])) return false;
		}
		return !Utils::user_agent(Utils::BROWSER_WECHAT);
	}
	public function support_type(){
		return Pay::TYPE_WAP;
	}
	public function support_name(){
		return PayWap::NAME;
	}
	public function match($name){
		if ($name==PayWap::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		$alipay_config=$this->_config->as_array();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		//卖家支付宝帐户
		$seller_email = $this->_config->get_seller_id();
	
		$out_trade_no=$pay_param->get_sn();
		$total_fee=$pay_param->get_pay_money();
		$subject=$pay_param->get_title();
		
		$end_url=$pay_param->get_cancel_url();
	
		//返回格式
		$format = "xml";
		//必填，不需要修改
	
		//返回格式
		$v = "2.0";
		//必填，不需要修改
	
		//请求号
		$req_id = date('Ymdhis');
		//必填，须保证每次请求都是唯一
	
		//**req_data详细信息**
	
	
		//订单名称
		//必填
	
		//请求业务参数详细
		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url
		. '</notify_url><call_back_url>' . $return_url
		. '</call_back_url><seller_account_name>' . $seller_email
		. '</seller_account_name><out_trade_no>' . $out_trade_no
		. '</out_trade_no><subject>' . $subject 
		. '</subject><total_fee>'. $total_fee 
		. '</total_fee><merchant_url>'. $end_url 
		. '</merchant_url></direct_trade_create_req>';
	
		//必填
		/************************************************************/
		//构造要请求的参数数组，无需改动
		$para_token = array(
				"service" => "alipay.wap.trade.create.direct",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		require_once Utils::lib_path("alipay_wap/lib/alipay_submit.class.php");
		
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		
		$html_text = $alipaySubmit->buildRequestHttp($para_token);

		//URLDECODE返回的信息
		$html_text = urldecode($html_text);

		//解析远程模拟提交后返回的信息
		$para_html_text = $alipaySubmit->parseResponse($html_text);
		if(!isset($para_html_text['res_data'])){
			if (is_string($para_html_text)){
				parse_str($para_html_text, $output);
				if(isset($output['res_error']))$html_text=$output['res_error'];
			}
			throw new Exception($html_text);
		}
		//获取request_token
		$request_token = @$para_html_text['request_token'];
		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
		//业务详细
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
		//必填

		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.wap.auth.authAndExecute",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		
		$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '');
		
		return new PayRender(PayRender::OUT_HTML, $html_text);
	}
	
	protected function _verify(){
		$alipay_config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_wap/lib/alipay_notify.class.php");
		return new \AlipayNotify($alipay_config);
	}
	
	
	public function pay_callback(){
		$alipayNotify=$this->_verify();
		if(!$alipayNotify->verifyReturn()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_GET);
		$out_trade_no=isset($_GET['out_trade_no'])?$_GET['out_trade_no']:null;
		$trade_no=isset($_GET['trade_no'])?$_GET['trade_no']:null;
		$result=isset($_GET['result'])?$_GET['result']:null;
		switch ($result){
			case 'success':
				$_result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_GET);
				break;
			default:
				$_result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$result);
				break;
		}
		return $_result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		$alipayNotify=$this->_verify();
		if(!$alipayNotify->verifyNotify()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$_POST);
		$doc = new \DOMDocument();
		
		$xml=$alipayNotify->decrypt($_POST['notify_data']);
		if(!isset($xml)){
			return PayResult::unkown($this->get_name(),"parse xml fail");
		}
		@$doc->loadXML($xml);
		if(@empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ){
			return PayResult::unkown($this->get_name(),"xml struct wrong");
		}
		//商户订单号
		$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
		//支付宝交易号
		$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
		$buyer_email = $doc->getElementsByTagName( "buyer_email" )->item(0)->nodeValue;
		$total_fee = $doc->getElementsByTagName( "total_fee" )->item(0)->nodeValue;
		//交易状态
		$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
		//商品购买
		if($trade_status == 'TRADE_FINISHED'||$trade_status == 'TRADE_SUCCESS') {
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$doc);
		}else $result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$trade_status);
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return  $result;
	}
}