<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;

class Config{
	// 	return array(
	// 		'username'=>'700000000000001',
	// 		'password'=>'8934e7d15453e97507ef794cf7b0519d',
	// 		'signature'=>'8934e7d15453e97507ef794cf7b0519d',
	// 		'payment_type'=>'sale',
	// 		'currency_code'=>'USD',
	// 		'mode'=>'sandbox',
	// 	);
	/**
	 * @param array $config
	 * @return static
	 */
	public static function arr(array $config){
		$self= new static(
			$config['username'],
			$config['password'],
			$config['signature'],
			isset($config['payment_type'])?$config['payment_type']:'Sale'
		);
		if (isset($config['currency_code'])&&isset($config['exchange_rate']))
			$self->set_currency($config['currency_code'], $config['exchange_rate']);
		if (isset($config['mode']))$self->set_mode($config['mode']);
		return $self;
	}
	
	public static $payment_type_types=array(
		'Sale',
		'Authorization',
		'Order',
	);
	
	protected $_config=array();
	public function __construct($username,$password,$signature,$payment_type='sale'){
		$this->_config=array(
			'mode'=>'live',//"sandbox",'live'
			'username'=>$username,
			'password'=>$password,
			'signature'=>$signature,
			'currency_code'=>'USD',
			'exchange_rate'=>1/6.8,
			'payment_type'=>$payment_type,
		);
	}
	/**
	 * @param string $currency_code
	 * @param float $exchange_rate
	 * @return string[]
	 */
	public function set_currency($currency_code,$exchange_rate){
		$this->_config['currency_code']=$currency_code;
		$this->_config['exchange_rate']=floatval($exchange_rate);
		return $this;
	}
	public function set_mode($mode){
		$this->_config['mode']=$mode;
		return $this;
	}
	public function get_mode(){
		return $this->_config['mode'];
	}
	public function get_username(){
		return $this->_config['username'];
	}
	public function get_password(){
		return $this->_config['password'];
	}
	public function get_signature(){
		return $this->_config['signature'];
	}
	public function get_currency_code(){
		return $this->_config['currency_code'];
	}
	public function get_payment_type(){
		if (in_array($this->_config['payment_type'],self::$payment_type_types)) return $this->_config['payment_type'];
		return self::$payment_type_types[0];
	}
	public function get_currency($currency_code=NULL){
		if ($currency_code==null)$currency_code=$this->_config['currency_code'];
		return $currency_code;
	}
}