<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;
class DirectPayConfig extends PayConfig{
	protected $_pay_url;
	public function set_pay_url($url){
		$this->_pay_url=$url;
		return $this;
	}
	public function get_pay_url(){
		return $this->_pay_url;
	}
}