<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Pay;
use LPAY\Pay\PayParam;

interface PayAdapter extends Adapter{
	/**
	 * @return int
	 */
	public function support_type();
	/**
	 * render to pay
	 * @param PayParam $pay_param
	 * @return PayRender
	 */
	public function pay_render(PayParam $pay_param);
	
}