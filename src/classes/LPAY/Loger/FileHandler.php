<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Loger;
use LPAY\Loger;

class FileHandler implements Handler{
	protected $_dir;
	public function __construct($dir){
		$this->_dir=rtrim($dir.'/\\ ').'/';
		if(!is_writable($this->_dir))$this->_dir=null;
	}
	public function save($type,$name,$log){
		if ($this->_dir==null) return;
		$y=date("Y");
		$m=date("m");
		$d=date("d");
		$dir=$this->_dir;
		$filename=$dir.$y.$m.DIRECTORY_SEPARATOR."pay-".$d.".log";
		if(!is_dir($dir.$y.$m)) @mkdir($dir.$y.$m);
		switch ($type){
			case Loger::TYPE_PAY_CALLBACK:$type='callback'; break;
			case Loger::TYPE_PAY_NOTIFY:$type='notify'; break;
			case Loger::TYPE_REFUND:$type='refund'; break;
			case Loger::TYPE_TRANSFERS:$type='transfers'; break;
			default:$type='other';
		}
		$wlog="";
		$wlog.="Type:".$type."\n";
		$wlog.="Name:".$name."\n";
		$wlog.="Time:".date("Y-m-d H:i:s")."\n";
		$log=str_replace(array("<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */"), "[PHP]", $log);
		$wlog.="Log:\n".$log."\n\n";
		@file_put_contents($filename, $wlog,FILE_APPEND);
	}
}
