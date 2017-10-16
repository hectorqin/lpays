<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Upacp;
class Config{
	// 	return array(
	// 			'merid'=>'700000000000001',
	// 			'sign_cert'=>__DIR__.'/PM_700000000000001_acp.pfx',
	// 			'sign_pwd'=>'000000',
	// 			'encrypt_cert'=>__DIR__.'/RSA2048_PROD_index_22.cer',
	// 			'verify_cert_dir'=>__DIR__.'/',
	// 	);
	/**
	 * 
	 * @param array $config
	 * @return static
	 */
	public static function arr(array $config){
		$obj= new static(
				$config['merid'],
				$config['sign_cert'],
				$config['sign_pwd'],
				$config['verify_cert_dir'],
				isset($config['encrypt_cert'])?$config['encrypt_cert']:null
		);
		if (isset($config['mode'])) $obj->_mode=$config['mode'];
		return $obj;
	}
	protected $_merid;
	protected $_sign_cert_path;
	protected $_sign_cert_pwd;
	protected $_encrypt_cert_path;
	protected $_verify_cert_dir;
	protected $_mode='live';
	public function __construct($merid,$sign_cert_path,$sign_cert_pwd,$verify_cert_dir,$encrypt_cert_path=null){
		$this->_merid=$merid;
		$this->_sign_cert_path=$sign_cert_path;
		$this->_sign_cert_pwd=$sign_cert_pwd;
		$this->_verify_cert_dir=$verify_cert_dir;
		$this->_encrypt_cert_path=$encrypt_cert_path;
	}
	public function get_merid(){
		return $this->_merid;
	}
	public function get_sign_cert_path(){
		return $this->_sign_cert_path;
	}
	public function get_sign_cert_pwd(){
		return $this->_sign_cert_pwd;
	}
	public function get_encrypt_cert_path(){
		//暂时没用到....
		return $this->_encrypt_cert_path;
	}
	public function get_verify_cert_dir(){
		return $this->_verify_cert_dir;
	}
	public function get_mode(){
		return $this->_mode;
	}
}
