<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Bill\Downloader;
use LPAY\Bill\Downloader;
use LPAY\Exception;
class CURL implements Downloader{
	protected $_dir;
	public function __construct($dir=null){
		if ($dir===null)$dir=sys_get_temp_dir();
		$this->_dir=rtrim($dir,"/\\");
		if (!is_dir($this->_dir)||!is_writable($this->_dir)) throw new Exception("can't write :dir",array("dir"=>$dir));
	}
	public function download($bill_type,$url,&$tag=null){
		if ($tag==null)$tag=md5($url).".bill";
		$ch = curl_init($url);
		$file=$this->file_path($bill_type, $tag);
		$fp = fopen($file, "w");
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$status=curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		if (!$status)unlink($file);
		return $status;
	}
	public function is_realtime(){
		return true;
	}
	public function file_path($bill_type,$tag){
		$bill_type=str_ireplace(array("\\","/"),"_", $bill_type);
		if (@mkdir($this->_dir.DIRECTORY_SEPARATOR.$bill_type));
		return $this->_dir.DIRECTORY_SEPARATOR.$bill_type.DIRECTORY_SEPARATOR.$tag;
	}
	public function delete($bill_type,$tag){
		unlink($this->file_path($bill_type, $tag));
	}
}