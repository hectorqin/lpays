<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\JD;
class PayConfig extends Config{
	protected $_notify_url;
	protected $_return_url;
	public function set_notify_url($url){
		$this->_notify_url=$url;
		return $this;
	}
	public function set_return_url($url){
		$this->_return_url=$url;
		return $this;
	}
	public function get_notify_url(){
		return $this->_notify_url;
	}
	public function get_return_url(){
		return $this->_return_url;
	}
}