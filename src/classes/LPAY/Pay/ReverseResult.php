<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Result;
class ReverseResult extends Result{
	public static function success($name,&$param=null){
		$result= new static($name,ReverseResult::STATUS_SUCC);
		$result->_param=$param;
		return $result;
	}
	public static function fail($name,$msg){
		$result= new static($name,ReverseResult::STATUS_FAIL);
		$result->_msg=$msg;
		return $result;
	}
	public static function ing($name,&$param=null){
		$result= new static($name,ReverseResult::STATUS_ING);
		$result->_param=$param;
		return $result;
	}
}