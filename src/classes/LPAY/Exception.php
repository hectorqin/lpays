<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
class Exception extends \Exception {
	public function __construct($message = "", $code = 0, \Exception $previous = NULL)
	{
		parent::__construct($message, (int) $code, $previous);
		$this->code = $code;
	}
}