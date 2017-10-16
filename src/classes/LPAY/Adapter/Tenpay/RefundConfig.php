<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
use LPAY\Utils;

class RefundConfig extends Config{
	
	// 	return array(
	// 		'partner'=>'700000000000001',
	// 		'key'=>'8934e7d15453e97507ef794cf7b0519d',
	// 	);
	/**
	 * @param array $config
	 * @return static
	 */
	public static function arr(array $config){
		$OBJ=new static(
				$config['partner'],
				$config['key'],
				$config['op_user_id'],
				$config['op_user_passwd'],
				$config['pem_path'],
				$config['pem_passwd']
			);
		if ($config['ca_path']) $OBJ->ca_path = $config['ca_path'];
		RETURN $OBJ;
	}
	protected $ca_path;
	protected $op_user_id;
	protected $op_user_passwd;
	protected $pem_path;
	protected $pem_passwd;
	public function __construct($partner,$key,$op_user_id,$op_user_passwd,$pem_path,$pem_passwd){
		parent::__construct($partner,$key);
		$this->op_user_id=$op_user_id;
		$this->op_user_passwd=$op_user_passwd;
		$this->pem_path=$pem_path;
		$this->pem_passwd=$pem_passwd;
		$this->ca_path=Utils::lib_path("tenpay_wap/cert/rootca.pem");
	}
	public function set_ca_path($ca_path){
		$this->ca_path=$ca_path;
		return $this;
	}
	public function get_ca_path(){
		return $this->ca_path;
	}
	public function get_op_user_id(){
		return $this->op_user_id;
	}
	public function get_op_user_passwd(){
		return $this->op_user_passwd;
	}
	public function get_pem_path(){
		return $this->pem_path;
	}
	public function get_pem_passwd(){
		return $this->pem_passwd;
	}
}