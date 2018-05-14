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
class PayParam extends Param implements \Serializable{
	public static $sn_prefix="LO";
	/**
	 * @param float $money
	 * @return PayParam
	 */
	public static function factory($money,$sn=null){
		return new PayParam($money,$sn);
	}
	protected $_param=array();
	public function __construct($money,$sn=null){
		$this->_param['money']=Money::factroy($money);
		$this->_param['sn']=$sn;
		$this->_param['timeout']=0;
		$this->_param['ctimeout']=time();
		$this->_param['good_ids']=array();
	}
	public function get_sn(){
		if (empty($this->_param['sn'])) $this->_param['sn']=Utils::snno_create(self::$sn_prefix);
		return $this->_param['sn'];
	}
	/**
	 * @return Money
	 */
	public function get_money(){
		return $this->_param['money'];
	}
	public function get_pay_money($currency=Money::CNY){
		$money=$this->_param['money']->to($currency);
		return $money<0.01?0.01:$money;
	}
	public function get_title(){
		return empty($this->_param['title'])?("pay {$this->_param['money']}"):$this->_param['title'];
	}
	public function get_body(){
		return empty($this->_param['body'])?$this->get_title():$this->_param['body'];
	}
	public function get_show_url(){
		return empty($this->_param['show_url'])?$this->_def_url():$this->_param['show_url'];
	}
	public function get_cancel_url(){
		return empty($this->_param['cancel_url'])?$this->_def_url():$this->_param['cancel_url'];
	}
	public function get_goods(){
		return $this->_param['good_ids'];
	}
	public function get_timeout(){
		return $this->_param['timeout']<=0?0:$this->_param['timeout'];
	}
	protected function _def_url(){
		if (isset($_SERVER['HTTP_HOST'])){
			if (isset($_SERVER['HTTPS'])&&strtoupper($_SERVER['HTTPS']) == 'ON'){
				$p='https://';
			}else $p='http://';
			if(isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']!='80')$pr=':'.$_SERVER['SERVER_PORT'];
			else $pr='';
			return $p.$_SERVER['HTTP_HOST'].$pr;
		}else return '/';
	}
	public function set_create_time($timeout){
		$this->_param['ctimeout']=$timeout;
		return $this;
	}
	public function get_create_time(){
		return $this->_param['ctimeout'];
	}
	public function set_timeout($timeout){
		$this->_param['timeout']=$timeout;
		return $this;
	}
	public function set_title($title){
		$this->_param['title']=$title;
		return $this;
	}
	public function set_body($body){
		$this->_param['body']=$body;
		return $this;
	}
	public function set_show_url($show_url){
		$this->_param['show_url']=$show_url;
		return $this;
	}
	public function set_cancel_url($cancel_url){
		$this->_param['cancel_url']=$cancel_url;
		return $this;
	}
	public function set_goods(array $ids){
		$this->_param['good_ids']=$ids;
		return $this;
	}
	public function as_array(){
		$param=$this->_param;
		$param['money']=strval($param['money']);
		$param['currency']=$param['money']->get_currency();
		return $param;
	}
	public function serialize () {
		$money=$this->_param['money'];
		unset($this->_param['money']);
		$data=$this->_param;
		$data['money']=$money->get_money();
		$data['currency']=$money->get_currency();
		return json_encode($data);
	}
	public function unserialize ($serialized) {
		$data=json_decode($serialized,true);
		if (isset($data['currency']))$data['currency']=Money::CNY;
		$data['money']=Money::factroy($data['money'],$data['currency']);
		unset($data['currency']);
		$this->_param=$data;
	}
}