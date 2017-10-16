<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
class Config{
	protected $_partner;
	protected $_key;
	public function __construct($partner,$key){
		$this->_partner=$partner;
		$this->_key=$key;
	}
	public function get_partner(){
		return $this->_partner;
	}
	public function get_key(){
		return $this->_key;
	}
}