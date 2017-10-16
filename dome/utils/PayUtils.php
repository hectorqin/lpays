<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
abstract class PayUtils{
	protected static $_instance=array();
	/**
	 * @return static
	 */
	public static function instance(){
		$cls=get_called_class();
		if (!isset(self::$_instance[$cls])){
			self::$_instance[$cls]= new static;
		}
		return self::$_instance[$cls];
	}
	/**
	 * @var Pay
	 */
	protected $pay;
	public function __construct(){
		$this->pay = new Pay();
	}
	/**
	 * @return \LPAY\Pay
	 */
	public function get_pay(){
		return $this->pay;
	}
}