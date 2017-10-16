<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Bill;
use LPAY\Bill;
use LPAY\Bill\Downloader\CURL;

abstract class Download{
	protected $_downloader;
	protected $_tag;
	/**
	 * @param Downloader $downloader
	 * @return bool
	 */
	public function set_downloader(Downloader $downloader){
		$this->_downloader=$downloader;
		return $this;
	}
	/**
	 * @return Downloader
	 */
	public function get_downloader(){
		if ($this->_downloader==null)$this->_downloader=new CURL();
		return $this->_downloader;
	}
	/**
	 * @return DataFile
	 */
	abstract public function get_data_file();
	/**
	 * @return string
	 */
	public function get_tag(){
		return $this->_tag;
	}
	/**
	 * set tag name
	 * @param string $tag
	 * @return \LPAY\Bill\Download
	 */
	public function set_tag($tag){
		$this->_tag=$tag;
		return $this;
	}
}