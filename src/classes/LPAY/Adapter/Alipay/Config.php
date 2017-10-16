<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Exception;

class Config{
	public static function arr(array $alipay_config){
		if(empty($alipay_config['private_key_path'])||empty($alipay_config['ali_public_key_path'])){
			$alipay_config['sign_type']='md5';
		}
		$self = new static(
			$alipay_config['partner'],
			$alipay_config['key'],
			$alipay_config['private_key_path'],
			$alipay_config['ali_public_key_path'],
			isset($alipay_config['transport'])?$alipay_config['transport']:'http'
		);
		switch (strtoupper($alipay_config['sign_type'])){
			case 'MD5':
				$self->set_md5();
			break;
			case 'RSA':
				$self->set_rsa();
			break;
		}
		if (isset($alipay_config['cacert'])) $self->_alipay_config['cacert']=$alipay_config['cacert'];
		
		return $self;
	}
	protected $_alipay_config=array();
	public function __construct($partner,$key,$private_path=null,$ali_public_path=null,$transport='http'){
		$alipay_config['partner']= $partner;
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    			= __DIR__."/../../../../libs/alipay_direct/cacert.pem";
		$alipay_config['key']					= $key;
		if (empty($private_path)||empty($ali_public_path)){
			$alipay_config['sign_type'] 			=strtoupper('MD5');
		}else{
			$alipay_config['sign_type'] 			=strtoupper('RSA');
			$alipay_config['private_key_path'] 		= $private_path;
			$alipay_config['ali_public_key_path']	= $ali_public_path;
		}
		$alipay_config['transport']= $transport;
		$this->_alipay_config=$alipay_config;
	}
	public function set_cacert($cacert){
		$this->_alipay_config['cacert']=$cacert;
		return $this;
	}
	public function set_md5(){
		$this->_alipay_config['sign_type']=strtoupper('MD5');
		return $this;
	}
	public function set_rsa(){
		if (empty($this->_alipay_config['private_key_path'])
			||empty($this->_alipay_config['ali_public_key_path'])) throw new Exception("key not set,not support rsa sign type");
		$this->_alipay_config['sign_type']=strtoupper('RSA');
		return $this;
	}
	public function set_0001(){
		$this->set_rsa();
		$this->_alipay_config['sign_type']='0001';
		return $this;
	}
	public function as_array(){
		return $this->_alipay_config;
	}
	
}