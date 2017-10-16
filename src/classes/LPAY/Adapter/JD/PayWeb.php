<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\JD;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay;
class PayWeb extends JD implements PayAdapterCallback{
	const NAME="lpay_jdweb";
	public function __construct(PayConfig $config){
		parent::__construct($config);
		$this->_pay_url='https://wepay.jd.com/jdpay/saveOrder';
	}
	public function support_type(){
		return Pay::TYPE_PC;
	}
	public function support_name(){
		return PayWeb::NAME;
	}
	public function match($name){
		if ($name==PayWeb::NAME) return true;
	}
}