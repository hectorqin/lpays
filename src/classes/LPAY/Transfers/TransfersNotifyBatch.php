<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Transfers;
use LPAY\Transfers\TransfersResult;
interface TransfersNotifyBatch extends \Countable,\SeekableIterator{
	/**
	 * @return bool
	 */
	public function transfers_notify();
	/**
	 * @return TransfersResult
	 */
	public function current ();
	/**
	 * @param bool $status
	 * @param string $msg
	 */
	public function transfers_notify_output($status=true,$msg=null);
}