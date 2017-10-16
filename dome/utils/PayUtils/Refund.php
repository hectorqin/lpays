<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\PayUtils;
use LPAY\PayUtils;

class Refund extends PayUtils{
	//alipay
	public static function alipay(array $_config){
		$config=\LPAY\Adapter\Alipay\RefundConfig::arr($_config);
		isset($_config['refund_notify_url'])&&$config->set_notify_url($_config['refund_notify_url']);
		return new \LPAY\Adapter\Alipay\Refund($config);
	}
	//wxpay
	public static function wechat(array $_config){
		$config=\LPAY\Adapter\Wechat\Config::arr($_config);
		return new \LPAY\Adapter\Wechat\Refund($config);
	}
	//upacp apple
	public static function upacp_apple(array $_config){
		$config=\LPAY\Adapter\Upacp\RefundConfig::arr($_config);
		isset($_config['refund_apple_notify_url'])&&$config->set_notify_url($_config['refund_apple_notify_url']);
		return new \LPAY\Adapter\Upacp\AppleRefund($config);
	}
	public static function upacp(array $_config){
		$config=\LPAY\Adapter\Upacp\RefundConfig::arr($_config);
		isset($_config['refund_notify_url'])&&$config->set_notify_url($_config['refund_notify_url']);
		return new \LPAY\Adapter\Upacp\Refund($config);
	}
	//tenpay
	public static function tenpay(array $_config){
		$config=\LPAY\Adapter\Tenpay\RefundConfig::arr($_config);
		return new \LPAY\Adapter\Tenpay\Refund($config);
	}
	//palpay
	public static function palpay(array $_config){
		$config=\LPAY\Adapter\Palpay\Config::arr($_config);
		return new \LPAY\Adapter\Palpay\Refund($config);
	}
	//jdpay
	public static function jd(array $_config){
		$config=\LPAY\Adapter\JD\RefundConfig::arr($_config);
		isset($_config['refund_notify_url'])&&$config->set_notify_url($_config['refund_notify_url']);
		return new \LPAY\Adapter\JD\Refund($config);
	}
	//baidu
	public static function baidu(array $_config){
		$config=\LPAY\Adapter\Baidu\RefundConfig::arr($_config);
		isset($_config['refund_notify_url'])&&$config->set_notify_url($_config['refund_notify_url']);
		return new \LPAY\Adapter\Baidu\Refund($config);
	}
	//pingxx
	public function set_alipay(array $_config){
		$this->pay->add_refund(self::alipay($_config));
		return $this;
	}
	public function set_wechat(array $_config){
		$this->pay->add_refund(self::wechat($_config));
		return $this;
	}
	public function set_upacp_apple(array $_config){
		$this->pay->add_refund(self::upacp_apple($_config));
		return $this;
	}
	public function set_upacp(array $_config){
		$this->pay->add_refund(self::upacp($_config));
		return $this;
	}
	public function set_tenpay(array $_config){
		$this->pay->add_refund(self::tenpay($_config));
		return $this;
	}
	public function set_palpay(array $_config){
		$this->pay->add_refund(self::palpay($_config));
		return $this;
	}
	public function set_jdpay(array $_config,$type=null){
		$this->pay->add_refund(self::jd($_config));
		return $this;
	}
	public function set_baidu(array $_config,$type=null){
		$this->pay->add_refund(self::baidu($_config));
		return $this;
	}
}