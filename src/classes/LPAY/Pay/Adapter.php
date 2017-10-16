<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
interface Adapter extends \LPAY\Adapter{
	/**
	 * set now adapter name
	 * @param string $name
	 */
	public function set_name($name);
	/**
	 * @return string
	 */
	public function get_name();
	/**
	 * @return array|string
	 */
	public function support_name();
	/**
	 * match pay name
	 * @return string
	 */
	public function match($name);
}