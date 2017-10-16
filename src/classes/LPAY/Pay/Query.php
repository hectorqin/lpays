<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
interface Query{
	/**
	 * @param string $sn
	 * @return PayResult
	 */
	public function query(QueryParam $param);
}