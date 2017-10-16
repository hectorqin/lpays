<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Transfers\TransfersAdapter;
use LPAY\Transfers\TransfersParam;
use LPAY\Transfers\TransfersAdapter;
interface RealTime extends TransfersAdapter{
	/**
	 * @param Param $param
	 * @return TransfersResult
	 */
	public function real_transfers(TransfersParam $param);
}