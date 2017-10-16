<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\PayUtils;
use LPAY\PayUtils;
use LPAY\Pay\PayResult;
use LPAY\Pay\Query;
use LPAY\Pay\QueryParam;

class Pay extends PayUtils{
	//alipay
	public static function alipay_wap(array $_config){
		$config=\LPAY\Adapter\Alipay\PayConfig::arr($_config);
		isset($_config['seller_email'])&&$config->set_seller_id($_config['seller_email']);
		isset($_config['pay_wap_notify_url'])&&$config->set_notify_url($_config['pay_wap_notify_url']);
		isset($_config['pay_wap_return_url'])&&$config->set_return_url($_config['pay_wap_return_url']);
		return new \LPAY\Adapter\Alipay\PayWap($config);
	}
	public static function alipay_pc(array $_config){
		$config=\LPAY\Adapter\Alipay\PayConfig::arr($_config);
		isset($_config['seller_email'])&&$config->set_seller_id($_config['seller_email']);
		isset($_config['pay_pc_notify_url'])&&$config->set_notify_url($_config['pay_pc_notify_url']);
		isset($_config['pay_pc_return_url'])&&$config->set_return_url($_config['pay_pc_return_url']);
		return new \LPAY\Adapter\Alipay\PayWeb($config);
	}
	public static function alipay_app(array $_config){
		$config=\LPAY\Adapter\Alipay\PayConfig::arr($_config);
		isset($_config['pay_app_notify_url'])&&$config->set_notify_url($_config['pay_app_notify_url']);
		return  new \LPAY\Adapter\Alipay\PayApp($config);
	}
	
	//wxpay
	public static function wechat_wap(array $_config){
		$config=\LPAY\Adapter\Wechat\PayWapConfig::arr($_config);
		isset($_config['pay_wap_notify_url'])&&$config->set_notify_url($_config['pay_wap_notify_url']);
		isset($_config['pay_wap_return_url'])&&$config->set_return_url($_config['pay_wap_return_url']);
		isset($_config['pay_oauth_return_url'])&&$config->set_oauth_return_url($_config['pay_oauth_return_url']);
		return new \LPAY\Adapter\Wechat\PayWap($config);
	}
	public static function wechat_pc(array $_config){
		$config=\LPAY\Adapter\Wechat\PayCodeConfig::arr($_config);
		isset($_config['pay_code_notify_url'])&&$config->set_notify_url($_config['pay_code_notify_url']);
		isset($_config['pay_code_qrcode_url'])&&$config->set_qrcode_url($_config['pay_code_qrcode_url']);
		isset($_config['pay_code_check_url'])&&$config->set_check_url($_config['pay_code_check_url']);
		isset($_config['pay_code_return_url'])&&$config->set_return_url($_config['pay_code_return_url']);
		return new \LPAY\Adapter\Wechat\PayCode($config);
	}
	public static function wechat_app(array $_config){
		$config=\LPAY\Adapter\Wechat\PayCodeConfig::arr($_config);
		return new \LPAY\Adapter\Wechat\PayApp($config);
	}
	//upacp apple
	public static function upacp_apple(array $_config){
		$config=\LPAY\Adapter\Upacp\PayConfig::arr($_config);
		isset($_config['pay_apple_notify_url'])&&$config->set_notify_url($_config['pay_apple_notify_url']);
		return new \LPAY\Adapter\Upacp\ApplePay($config);
	}
	public static function upacp(array $_config){
		$config=\LPAY\Adapter\Upacp\PayConfig::arr($_config);
		isset($_config['pay_notify_url'])&&$config->set_notify_url($_config['pay_notify_url']);
		isset($_config['pay_return_url'])&&$config->set_return_url($_config['pay_return_url']);
		return new \LPAY\Adapter\Upacp\Pay($config);
	}
	//tenpay
	public static function tenpay_wap(array $_config){
		$config=\LPAY\Adapter\Tenpay\PayConfig::arr($_config);
		isset($_config['pay_pc_notify_url'])&&$config->set_notify_url($_config['pay_pc_notify_url']);
		isset($_config['pay_pc_return_url'])&&$config->set_return_url($_config['pay_pc_return_url']);
		return new \LPAY\Adapter\Tenpay\PayWap($config);
	}
	public static function tenpay_pc(array $_config){
		$config=\LPAY\Adapter\Tenpay\PayConfig::arr($_config);
		isset($_config['pay_wap_notify_url'])&&$config->set_notify_url($_config['pay_wap_notify_url']);
		isset($_config['pay_wap_return_url'])&&$config->set_return_url($_config['pay_wap_return_url']);
		return new \LPAY\Adapter\Tenpay\PayWeb($config);
	}
	//palpay
	public static function palpay(array $_config){
		$config=\LPAY\Adapter\Palpay\PayConfig::arr($_config);
		isset($_config['pay_ipn_url'])&&$config->set_notify_url($_config['pay_ipn_url']);
		isset($_config['pay_return_url'])&&$config->set_return_url($_config['pay_return_url']);
		return new \LPAY\Adapter\Palpay\Pay($config);
	}
	public static function palpay_direct(array $_config){
		$config=\LPAY\Adapter\Palpay\DirectPayConfig::arr($_config);
		isset($_config['pay_ipn_url'])&&$config->set_notify_url($_config['pay_ipn_url']);
		isset($_config['pay_do_url'])&&$config->set_pay_url($_config['pay_do_url']);
		return new \LPAY\Adapter\Palpay\DirectPay($config);
	}
	//jdpay
	public static function jd_wap(array $_config){
		$config=\LPAY\Adapter\JD\PayConfig::arr($_config);
		isset($_config['pay_wap_notify_url'])&&$config->set_notify_url($_config['pay_wap_notify_url']);
		isset($_config['pay_wap_return_url'])&&$config->set_return_url($_config['pay_wap_return_url']);
		return new \LPAY\Adapter\JD\PayWap($config);
	}
	public static function jd_pc(array $_config){
		$config=\LPAY\Adapter\JD\PayConfig::arr($_config);
		isset($_config['pay_pc_notify_url'])&&$config->set_notify_url($_config['pay_pc_notify_url']);
		isset($_config['pay_pc_return_url'])&&$config->set_return_url($_config['pay_pc_return_url']);
		return new \LPAY\Adapter\JD\PayWeb($config);
	}
	//baidu
	public static function baidu_wap(array $_config){
		$config=\LPAY\Adapter\Baidu\PayConfig::arr($_config);
		isset($_config['pay_wap_notify_url'])&&$config->set_notify_url($_config['pay_wap_notify_url']);
		isset($_config['pay_wap_return_url'])&&$config->set_return_url($_config['pay_wap_return_url']);
		return new \LPAY\Adapter\Baidu\PayWap($config);
	}
	public static function baidu_pc(array $_config){
		$config=\LPAY\Adapter\Baidu\PayConfig::arr($_config);
		isset($_config['pay_pc_notify_url'])&&$config->set_notify_url($_config['pay_pc_notify_url']);
		isset($_config['pay_pc_return_url'])&&$config->set_return_url($_config['pay_pc_return_url']);
		return new \LPAY\Adapter\Baidu\PayWeb($config);
	}
	public static function baidu_bank(array $_config){
		$config=\LPAY\Adapter\Baidu\PayConfig::arr($_config);
		isset($_config['pay_pc_notify_url'])&&$config->set_notify_url($_config['pay_pc_notify_url']);
		isset($_config['pay_pc_return_url'])&&$config->set_return_url($_config['pay_pc_return_url']);
		return new \LPAY\Adapter\Baidu\PayBank($config);
	}
	//method.....
	protected function _def_type(&$type){
		if ($type==null)$type=\LPAY\Pay::TYPE_ANDROID|\LPAY\Pay::TYPE_IOS|\LPAY\Pay::TYPE_PC|\LPAY\Pay::TYPE_WAP;
	}
	public function set_alipay(array $_config,$type=null){
		$this->_def_type($type);
		if ($type&\LPAY\Pay::TYPE_WAP)$this->pay->add_pay(self::alipay_wap($_config));
		if ($type&\LPAY\Pay::TYPE_PC) $this->pay->add_pay(self::alipay_pc($_config));
		if ($type&\LPAY\Pay::TYPE_IOS||$type&\LPAY\Pay::TYPE_ANDROID) $this->pay->add_pay(self::alipay_app($_config));
		return $this;
	}
	public function set_wechat(array $_config,$type=null){
		$this->_def_type($type);
		if ($type&\LPAY\Pay::TYPE_WAP){
			$this->pay->add_pay(self::wechat_wap($_config));
		}
		if ($type&\LPAY\Pay::TYPE_PC){
			$this->pay->add_pay(self::wechat_pc($_config));
		}
		if ($type&\LPAY\Pay::TYPE_IOS||$type&\LPAY\Pay::TYPE_ANDROID) $this->pay->add_pay(self::wechat_app($_config));
		return $this;
	}
	public function set_upacp_apple(array $_config){
		$this->_def_type($type);
		$this->pay->add_pay(self::upacp_apple($_config));
		return $this;
	}
	public function set_upacp(array $_config){
		$this->_def_type($type);
		$this->pay->add_pay(self::upacp($_config));
		return $this;
	}
	public function set_tenpay(array $_config,$type=null){
		$this->_def_type($type);
		if ($type&\LPAY\Pay::TYPE_WAP){
			$this->pay->add_pay(self::tenpay_wap($_config));
		}
		if ($type&\LPAY\Pay::TYPE_PC){
			$this->pay->add_pay(self::tenpay_pc($_config));
		}
		return $this;
	}
	public function set_palpay(array $_config,$direct=true){
		$this->_def_type($type);
		$this->pay->add_pay(self::palpay($_config));
		if($direct)$this->pay->add_pay(self::palpay_direct($_config));
		return $this;
	}
	public function set_jdpay(array $_config,$type=null){
		$this->_def_type($type);
	
		if ($type&\LPAY\Pay::TYPE_WAP){
			$this->pay->add_pay(self::jd_wap($_config));
		}
		if ($type&\LPAY\Pay::TYPE_PC){
			$this->pay->add_pay(self::jd_pc($_config));
		}
		return $this;
	}
	public function set_baidu(array $_config,$type=null){
		$this->_def_type($type);
		if ($type&\LPAY\Pay::TYPE_WAP){
			$this->pay->add_pay(self::baidu_wap($_config));
		}
		if ($type&\LPAY\Pay::TYPE_PC){
			$this->pay->add_pay(self::baidu_pc($_config));
			$this->pay->add_pay(self::baidu_bank($_config));
		}
		return $this;
	}
	
	//query wrap...
	public function query($name,$pay_sn,$pay_no,$create_time){
		$pay=$this->pay->find_pay($name);
		if(!$pay) return PayResult::unkown($this->get_name(),'not find this pay:'.$name);
		if(!($pay instanceof Query))  return PayResult::unkown($this->get_name(),'not query form the pay:'.$name);
		$param= new QueryParam($pay_sn, $pay_no, $create_time);
		return $pay->query($param);
	}
}