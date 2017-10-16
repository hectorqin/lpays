<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Bill;
interface Downloader{
	/**
	 * @param string $bill_type
	 * @param string $url
	 * @param string $tag
	 * @return bool
	 */
	public function download($bill_type,$url,&$tag=null);
	/**
	 * @return bool
	 */
	public function is_realtime();
	/**
	 * @param string $bill_type
	 * @param string $tag
	 * @return string
	 */
	public function file_path($bill_type,$tag);
	/**
	 * @param string $bill_type
	 * @param string $tag
	 * @return bool
	 */
	public function delete($bill_type,$tag);
}