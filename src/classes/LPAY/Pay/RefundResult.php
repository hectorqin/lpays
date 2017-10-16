<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Result;
class RefundResult extends Result{
	public static function success($name,$refund_no,$refund_pay_no,&$param=null){
		$result= new static($name,RefundResult::STATUS_SUCC);
		$result->_refund_no=$refund_no;
		$result->_refund_pay_no=$refund_pay_no;
		$result->_param=$param;
		return $result;
	}
	public static function fail($name,$refund_no,$msg){
		$result= new static($name,RefundResult::STATUS_FAIL);
		$result->_refund_no=$refund_no;
		$result->_msg=$msg;
		return $result;
	}
	
	public static function ing($name,$refund_no,&$param=null,$refund_pay_no=null){
		$result= new static($name,RefundResult::STATUS_ING);
		$result->_refund_no=$refund_no;
		$result->_refund_pay_no=$refund_pay_no;
		$result->_param=$param;
		return $result;
	}
	protected $_refund_no;
	protected $_refund_pay_no;
	public function get_refund_no(){
		return $this->_refund_no;
	}
	public function get_refund_pay_no(){
		return $this->_refund_pay_no;
	}
}