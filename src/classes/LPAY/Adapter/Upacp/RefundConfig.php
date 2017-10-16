<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Upacp;
class RefundConfig extends Config{
	protected $_notify_url;
	public function set_notify_url($url){
		$this->_notify_url=$url;
		return $this;
	}
	public function get_notify_url(){
		return $this->_notify_url;
	}
}