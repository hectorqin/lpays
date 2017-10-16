<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Transfers;
use LPAY\Adapter;

interface TransfersAdapter extends Adapter{
	/**
	 * @return string
	 */
	public function transfers_name();
	public function fee();
	public function min_fee();
	public function max_fee();
}