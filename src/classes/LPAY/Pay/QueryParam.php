<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Param;
class QueryParam extends Param{
	protected $_param=array();
	public function __construct($pay_sn,$pay_no,$create_time){
		$this->_param['pay_sn']=$pay_sn;
		$this->_param['pay_no']=$pay_no;
		$this->_param['ctime']=$create_time;
	}
	public function get_create_time(){
		return $this->_param['ctime'];
	}
	public function get_pay_no(){
		return $this->_param['pay_no'];
	}
	public function get_pay_sn(){
		return $this->_param['pay_sn'];
	}
	public function as_array(){
		return $this->_param;
	}
}