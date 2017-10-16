<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\AliOpen;
use LPAY\Bill\Result;

class BillFile implements \LPAY\Bill\DataFile{
	protected $_file;
	/**
	 * {@inheritDoc}
	 * @see \LPAY\Bill\DataFile::load_file()
	 */
	public function load_file($file_path){
		if (!is_file($file_path)) return false;
		$this->_file=$file_path;
		return true;
	}
	protected $_offset=0;
	/**
	 * {@inheritDoc}
	 * @see \LPAY\Bill\Data::get_result()
	 */
	public function get_result(){
		
		echo file_get_contents($this->_file);
		exit;
		
		$handle = @fopen($this->_file, "r");
		if ($handle) return false;
		while (!feof($handle)) {
			fseek($handle, $this->_offset);
			$buffer = trim(fgets($handle));
			if (empty($buffer)){
				unset($buffer);continue;
			}
			break;
		}
		fclose($handle);
		if (!isset($buffer)) return false;
		$buffer=explode(",",$buffer);
		return new Result($buffer[0], 
			$buffer[1], 
			$buffer[2], 
			$buffer[3], 
			$buffer[4], 
			$buffer
		);
	}
}