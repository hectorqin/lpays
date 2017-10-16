<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
class TransfersConfig extends Config{
	protected $_notify_url;
	protected $_seller_id;
	protected $_seller_name;
	public function set_notify_url($url){
		$this->_notify_url=$url;
		return $this;
	}
	public function get_notify_url(){
		return $this->_notify_url;
	}
	public function set_seller_id($email){
		$this->_seller_id=$email;
		return $this;
	}
	public function get_seller_id(){
		return $this->_seller_id;
	}
	public function set_seller_name($name){
		$this->_seller_name=$name;
		return $this;
	}
	public function get_seller_name(){
		return $this->_seller_name;
	}
}