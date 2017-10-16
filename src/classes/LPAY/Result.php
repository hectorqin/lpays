<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
abstract class Result{
	const STATUS_FAIL=0;
	const STATUS_SUCC=1;
	const STATUS_UKN =2;
	const STATUS_ING=3;
	public static $sign_invalid='sign invalid';
	public static function unkown($name,$msg){
		$result= new static($name,static::STATUS_UKN);
		$result->_msg=$msg;
		return $result;
	}
	protected $_msg;
	protected $_param;
	protected $_name;
	protected $_status;
	public function __construct($name,$status){
		$this->_name=$name;
		$this->_status=$status;
	}
	public function get_status(){
		return $this->_status;
	}
	public function get_name(){
		return $this->_name;
	}
	public function get_params(){
		return $this->_param;
	}
	public function get_msg(){
		return $this->_msg;
	}
}