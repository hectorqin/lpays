<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\AliOpen;
class Config{
	public static function arr(array $alipay_config){
		$self = new static(
			$alipay_config['app_id'],
			$alipay_config['private_key_path'],
			$alipay_config['ali_public_key_path']
		);
		return $self;
	}
	protected $_alipay_config=array();
	protected $_gateway="https://openapi.alipay.com/gateway.do";
	public function __construct($appid,$private_path,$ali_public_path){
		$alipay_config['app_id']= $appid;
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    			= __DIR__."/../../../../libs/alipay_direct/cacert.pem";
		$alipay_config['private_key_path'] 		= $private_path;
		$alipay_config['ali_public_key_path']	= $ali_public_path;
		$this->_alipay_config=$alipay_config;
	}
	public function set_cacert($cacert){
		$this->_alipay_config['cacert']=$cacert;
		return $this;
	}
	public function get_gateway(){
		return $this->_gateway;
	}
	public function as_array(){
		return $this->_alipay_config;
	}
}