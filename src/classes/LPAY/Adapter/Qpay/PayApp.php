<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
use LPAY\Pay;
use LPAY\Pay\PayParam;
use LPAY\Exception;
class PayApp extends PayNotify{
	const NAME="lpay_qq_app";
	/**
	 * @var Config
	 */
	protected $_config;
	public function __construct(Config $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	public function support_name(){
		return PayApp::NAME;
	}
	public function match($name){
		if ($name==PayApp::NAME) return true;
	}
	public function enable(){
		return true;
	}
	public function support_type(){
		return Pay::TYPE_ANDROID|Pay::TYPE_IOS;
	}
	public function pay_render(PayParam $pay_param){
		throw new Exception('not support the method');
	}
}