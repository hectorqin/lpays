<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Result;
class PayResult extends Result{
	public static function success($name,$pay_sn,$pay_no,&$param=null){
		$result= new static($name,PayResult::STATUS_SUCC);
		$result->_pay_no=$pay_no;
		$result->_pay_sn=$pay_sn;
		$result->_param=$param;
		return $result;
	}
	public static function ing($name,$pay_sn,&$param=null,$pay_no=null){
		$result= new static($name,PayResult::STATUS_ING);
		$result->_pay_no=$pay_no;
		$result->_pay_sn=$pay_sn;
		$result->_param=$param;
		return $result;
	}
	public static function fail($name,$pay_sn,$pay_no,$msg){
		$result= new static($name,PayResult::STATUS_FAIL);
		$result->_msg=$msg;
		$result->_pay_no=$pay_no;
		$result->_pay_sn=$pay_sn;
		return $result;
	}
	protected $_pay_no;
	protected $_pay_sn;
	protected $_pay_account;
	protected $_money;
	protected $_refunded=null;
	public function set_money($money){
		$this->_money= Money::factroy($money);
		return $this;
	}
	public function set_refundd($is_refund){
		$this->_refunded=boolval($is_refund);
		return $this;
	}
	public function set_pay_account($pay_account){
		$this->_pay_account=$pay_account;
		return $this;
	}
	
	/**
	 * 外部支付系统订单号
	 * @return string
	 */
	public function get_pay_no(){
		return $this->_pay_no;
	}
	/**
	 * 本地站点订单号
	 * @return string
	 */
	public function get_pay_sn(){
		return $this->_pay_sn;
	}
	/**
	 * @return Money
	 */
	public function get_money(){
		return $this->_money;
	}
	public function get_pay_account(){
		return $this->_pay_account;
	}
	public function get_refundd(){
		return $this->_refunded;
	}
}