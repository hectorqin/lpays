<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\PayParam;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Loger;
use LPAY\Pay\PayRender;

class PayWeb extends Alipay implements PayAdapterCallback{
	const NAME="lpay_alipay_web";
	/**
	 * @var PayConfig
	 */
	protected $_config;
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
		$this->_config->set_md5();
	}
	public function enable(){
		$alipay_config=$this->_config->as_array();
		$check=array('partner','key');
		foreach ($check as $v){
			if(empty($alipay_config[$v])) return false;
		}
		return !Utils::user_agent(Utils::BROWSER_WECHAT);
	}
	public function support_type(){
		return Pay::TYPE_PC;
	}
	public function support_name(){
		return PayWeb::NAME;
	}
	public function match($name){
		if ($name==PayWeb::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		$alipay_config=$this->_config->as_array();
		$seller_email =$this->_config->get_seller_id();
		
		$payment_type = "1";
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$show_url=$pay_param->get_show_url();
		
		$out_trade_no=$pay_param->get_sn();
		$total_fee=$pay_param->get_pay_money();
		$subject=$pay_param->get_title();
		$body=$pay_param->get_body();
		
		
		$anti_phishing_key = "";
		$exter_invoke_ip = Utils::client_ip();
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		require_once Utils::lib_path("alipay_direct/lib/alipay_submit.class.php");
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text=$alipaySubmit->buildRequestForm($parameter,"get", "");
		return new PayRender(PayRender::OUT_HTML, $html_text);
	}
	protected function _verify(){
		$alipay_config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_direct/lib/alipay_notify.class.php");
		return new \AlipayNotify($alipay_config);
	}
	public function pay_callback(){
		$alipayNotify=$this->_verify();
		if(!$alipayNotify->verifyReturn()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_GET);
		$trade_status=$_GET['trade_status'];
		$out_trade_no=@$_GET['out_trade_no'];
		$trade_no=@$_GET['trade_no'];
		if($trade_status== 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' ){
			$result= PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_GET);
		}else $result= PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$trade_status);
		$total_fee=$_GET['total_fee'];
		$buyer_email=$_GET['buyer_email'];
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return $result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		$alipayNotify=$this->_verify();
		if(!$alipayNotify->verifyNotify()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_POST);
		$out_trade_no=$_POST['out_trade_no'];
		$trade_no=$_POST['trade_no'];
		$buyer_email=$_POST['buyer_email'];
		$total_fee=$_POST['total_fee'];
		//交易状态
		$trade_status = $_POST['trade_status'];
		if($_POST['trade_status'] == 'TRADE_FINISHED') {
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		}else{
			$result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$_POST['trade_status']);
		}
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return  $result;
	}
}