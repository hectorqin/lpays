<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
interface PayAdapterNotify{
	/**
	 * pay notify
	 * @return PayResult
	 */
	public function pay_notify();
	/**
	 * pay notify
	 */
	public function pay_notify_output($status=true,$msg=null);
}