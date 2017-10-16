<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
class PayCodeConfig extends Config{
	protected $_notify_url;
	protected $_qrcode_url;
	protected $_return_url;
	protected $_check_url;
	
	public function get_appid(){
		return $this->_appid;
	}
	
	public function set_notify_url($url){
		$this->_notify_url=$url;
		return $this;
	}
	public function set_qrcode_url($url){
		$this->_qrcode_url=$url;
		return $this;
	}
	public function set_return_url($url){
		$this->_return_url=$url;
		return $this;
	}
	public function set_check_url($url){
		$this->_check_url=$url;
		return $this;
	}
	public function get_notify_url(){
		return $this->_notify_url;
	}
	public function get_qrcode_url(){
		return $this->_qrcode_url;
	}
	public function get_return_url(){
		return $this->_return_url;
	}
	public function get_check_url(){
		return $this->_check_url;
	}
}
