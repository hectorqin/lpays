<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Pay;
use LPAY\Exception;
use LPAY\Utils;
use LPAY\Result;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayResult;
use LPAY\Loger;
class PayApp extends Alipay{
	const NAME="lpay_alipay_app";
	/**
	 * @var PayConfig
	 */
	protected $_config;
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
		$this->_config->set_rsa();
	}
	public function enable(){
		$alipay_config=$this->_config->as_array();
		$check=array('partner','private_key_path','ali_public_key_path');
		foreach ($check as $v){
			if(empty($alipay_config[$v])) return false;
		}
		return true;
	}
	public function support_type(){
		return Pay::TYPE_ANDROID|Pay::TYPE_IOS;
	}
	public function support_name(){
		return PayApp::NAME;
	}
	public function match($name){
		if ($name==PayApp::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		throw new Exception('not support the method');
	}
	public function pay_notify(){
		ignore_user_abort(true);
		$alipay_config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_app/lib/alipay_notify.class.php");
		$alipayNotify = new \AlipayNotify($alipay_config);
		if(!$alipayNotify->verifyNotify()){
			return  PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$_POST);
		$out_trade_no = $_POST['out_trade_no'];
		$trade_no = $_POST['trade_no'];
		$trade_status = $_POST['trade_status'];
		$total_fee=$_POST[ 'total_fee'];
		$buyer_email=$_POST[ 'buyer_email'];
		if($trade_status == 'TRADE_FINISHED') {
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		}else if ($trade_status == 'TRADE_SUCCESS') {
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_POST);
		}else $result=PayResult::fail($this->get_name(),$out_trade_no,$trade_no,$trade_status);;
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return   $result;
	}
}