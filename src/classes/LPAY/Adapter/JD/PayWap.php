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
class PayWap extends JD implements PayAdapterCallback{
	const NAME="lpay_jdwap";
	/**
	 * @var PayConfig
	 */
	protected $_config;
	public function __construct(PayConfig $config){
		parent::__construct($config);
		$this->_pay_url='https://h5pay.jd.com/jdpay/saveOrder';
	}
	public function support_type(){
		return Pay::TYPE_WAP|Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return PayWap::NAME;
	}
	public function match($name){
		if ($name==PayWap::NAME) return true;
	}
}