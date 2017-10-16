<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\PayUtils;
use LPAY\PayUtils;
class Transfers extends PayUtils{
	//alipay
	public static function alipay(array $_config){
		$config=\LPAY\Adapter\Alipay\TransfersConfig::arr($_config);
		isset($_config['transfers_notify_url'])&&$config->set_notify_url($_config['transfers_notify_url']);
		isset($_config['seller_email'])&&$config->set_seller_id($_config['seller_email']);
		isset($_config['seller_name'])&&$config->set_seller_name($_config['seller_name']);
		return new \LPAY\Adapter\Alipay\Transfers($config);
	}
	//wxpay
	public static function wechat(array $_config){
		$config=\LPAY\Adapter\Wechat\Config::arr($_config);
		return new \LPAY\Adapter\Wechat\Transfers($config);
	}
	public function set_alipay(array $_config){
		$this->pay->add_transfers(self::alipay($_config));
		return $this;
	}
	public function set_wechat(array $_config){
		$this->pay->add_transfers(self::wechat($_config));
		return $this;
	}
}