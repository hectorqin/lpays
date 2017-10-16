<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter;
abstract class Adapter implements \LPAY\Pay\Adapter{
	protected $_name;
	public function set_name($name){
		$this->_name=$name;
		return $this;
	}
	public function get_name(){
		return $this->_name;
	}
	public function match($name){
		$names=$this->support_name();
		if (is_string($names))$names=array($names);
		if (in_array($name,$names)) return true;
		return false;
	}
}