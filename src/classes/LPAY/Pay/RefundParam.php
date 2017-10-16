<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Param;
use LPAY\Utils;

class RefundParam extends Param{
	public static $sn_prefix="LR";
	/**
	 * @param float $money
	 * @return \LPAY\Pay\PayParam
	 */
	public static function factory($pay_sn,$pay_no,$total_money,$refund_money){
		return new PayParam($pay_sn,$pay_no,$total_money,$refund_money);
	}
	protected $_param=array();
	public function __construct($pay_sn,$pay_no,$total_money,$refund_money){
		$this->_param['pay_no']=$pay_no;
		$this->_param['pay_sn']=$pay_sn;
		$this->_param['total_money']=Money::factroy($total_money);
		$this->_param['money']=Money::factroy($refund_money);
	}
	public function set_return_no($return_no){
		$this->_param['return_no']=$return_no;
		return $this;
	}
	public function set_refund_msg($msg){
		$this->_param['msg']=$msg;
		return $this;
	}
	public function get_return_no(){
		if (empty($this->_param['return_no'])){
			$this->_param['return_no']=Utils::snno_create(self::$sn_prefix);
		}
		return $this->_param['return_no'];
	}
	public function get_refund_msg(){
		if(empty($this->_param['msg'])) return '';
		return $this->_param['msg'];
	}
	public function get_pay_sn(){
		return $this->_param['pay_sn'];
	}
	public function get_pay_no(){
		return $this->_param['pay_no'];
	}
	/**
	 * @return Money
	 */
	public function get_refund_money(){
		return $this->_param['money'];
	}
	/**
	 * @return Money
	 */
	public function get_total_money(){
		return $this->_param['total_money'];
	}
	public function get_refund_pay_money($currency=Money::CNY){
		$money=$this->_param['money']->to($currency);
		if ($money<=0) return 0;
		$total=$this->get_total_pay_money($currency);
		$money=$money<=$total?$money:$total;
		return Utils::money_format($money);
	}
	public function get_total_pay_money($currency=Money::CNY){
		$money=$this->_param['total_money']->to($currency);
		if ($money<=0) return 0;
		return $money;
	}
	public function as_array(){
		return $this->_param;
	}
}