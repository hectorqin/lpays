<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Transfers;
use LPAY\Param;
use LPAY\Pay\Money;
use LPAY\Utils;

class TransfersParam extends Param{
	public static $sn_prefix="LT";
	protected $_param=array();
	public function __construct($pay_account,$pay_name,$money,$transfers_no=null){
		if ($transfers_no==null)$transfers_no=Utils::snno_create(self::$sn_prefix);
		$this->_param=array(
			'pay_account'=>$pay_account,
			'pay_name'=>$pay_name,
			'pay_money'=>Money::factroy($money),
			'transfers_no'=>$transfers_no,
			'msg'=>'transfers',
			'extra'=>array(),
		);
	}
	public function set_extra($param){
		$this->_param['extra']=$param;
		return $this;
	}
	public function set_pay_msg($msg){
		$this->_param['msg']=$msg;
		return $this;
	}
	public function get_pay_account(){
		return $this->_param['pay_account'];
	}
	public function get_pay_name(){
		return $this->_param['pay_name'];
	}
	public function get_money(){
		return $this->_param['pay_money'];
	}
	public function get_pay_money($currency=Money::CNY){
		$money=$this->_param['pay_money']->to($currency);
		if ($money<=0) return 0;
		return $money;
	}
	public function get_transfers_no(){
		return $this->_param['transfers_no'];
	}
	public function get_pay_msg(){
		return $this->_param['msg'];
	}
	public function get_extra(){
		return $this->_param['extra'];
	}
	public function as_array(){
		return $this->_param;
	}
	
}