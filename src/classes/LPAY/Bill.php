<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
interface Bill{
	/**
	 * setting get bill date 
	 * @param string $date
	 * @return $this
	 */
	public function set_date($date);
	/**
	 * run get bill
	 */
	public function exec();
}