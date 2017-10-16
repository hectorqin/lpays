<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
use LPAY\Loger\Handler;

class Loger{
	const TYPE_PAY_CALLBACK		=0;
	const TYPE_PAY_NOTIFY 		=1;
	const TYPE_REFUND			=3;
	const TYPE_TRANSFERS		=2;
	protected static $_instances=array();
	/**
	 * @param int $type
	 * @return Loger
	 */
	public static function instance($type){
		if (!isset(self::$_instances[$type])){
			self::$_instances[$type]=new self($type);
		}
		return self::$_instances[$type];
	}
	protected static $_handler=array();
	public static function reg_handler(Handler $handler){
		self::$_handler[]=$handler;
	}
	
	
	protected $_type;
	protected $_logs=array();
	public function __construct($type){
		$this->_type=$type;
	}
	
	public function add($name,$log){
		if ($log instanceof \Exception) $log=$log->getTraceAsString();
		if (is_array($log)) $log=json_encode($log);
		if (is_object($log)) $log=serialize($log);
		$log=strval($log);
		$this->_logs[]=array($name,$log);
	}
	public function __destruct(){
		foreach (self::$_handler as $v){
			foreach ($this->_logs  as $args){
				array_unshift($args, $this->_type);
				call_user_func_array(array($v,'save'),$args);
			}
		}
	}
}