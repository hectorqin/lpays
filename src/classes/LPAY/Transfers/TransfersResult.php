<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Transfers;
use LPAY\Result;
class TransfersResult extends Result{
	
	public static function success($name,$transfers_no,$pay_no,&$param=null){
		$result= new static($name,TransfersResult::STATUS_SUCC);
		$result->_transfers_no=$transfers_no;
		$result->_pay_no=$pay_no;
		$result->_param=$param;
		return $result;
	}
	public static function ing($name,$transfers_no,$pay_no,&$param=null){
		$result= new static($name,TransfersResult::STATUS_ING);
		$result->_transfers_no=$transfers_no;
		$result->_pay_sn=$pay_no;
		$result->_param=$param;
		return $result;
	}
	public static function fail($name,$transfers_no,$pay_no,$msg){
		$result= new static($name,TransfersResult::STATUS_FAIL);
		$result->_msg=$msg;
		$result->_transfers_no=$transfers_no;
		$result->_pay_no=$pay_no;
		return $result;
	}
	
	protected $_transfers_no;
	protected $_pay_no;
	
	public function get_transfers_no(){
		return $this->_transfers_no;
	}
	public function get_pay_no(){
		return $this->_pay_no;
	}
}