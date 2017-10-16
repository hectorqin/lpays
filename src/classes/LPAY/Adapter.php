<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
interface Adapter{
	/**
	 * return bool
	 */
	public function enable();
	/**
	 * return adapter config
	 * @return mixed
	 */
	public function get_config();
}