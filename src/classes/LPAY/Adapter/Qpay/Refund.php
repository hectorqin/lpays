<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
class Refund extends RefundAdapter{
	const NAME="wechat";
	/**
	 * @var Config
	 */
	protected $_config;
	public function __construct(Config $config){
		$this->_config=$config;
	}
	/**
	 * @return Config
	 */
	public function get_config(){
		return $this->_config;
	}
	/**
	 * @return string
	 */
	public function support_name(){
		return array(PayCode::NAME,PayWap::NAME,PayApp::NAME);
	}
	public function enable(){
		return true;
	}
	public function refund(RefundParam $refund_param){
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = intval($refund_param->get_refund_pay_money()*100);
		$total_money = intval($refund_param->get_total_pay_money()*100);
		$return_no = $refund_param->get_return_no();
		$param=array();
		$param['transaction_id']=$recharge_pay_no;
		$param['total_fee']=$total_money;
		$param['refund_fee']=$refund_money;
		$param['out_refund_no']=$return_no;
		$xml=Tools::get_to_xml($param, $this->_config);
		$url="https://api.qpay.qq.com/cgi-bin/pay/qpay_refund.cgi";
		try{
			$result=Tools::post($url, $xml, $this->_config);
			$result=Tools::parse($result);
		}catch (\Exception $e){
			return RefundResult::fail($this->get_name(),$return_no, $e->getMessage());
		}
		if($result["result_code"]!='SUCCESS'){
			return RefundResult::fail($this->get_name(),$return_no, $result['err_code_des']);
		}
		return RefundResult::success($this->get_name(),$return_no,$result['refund_id'],$result);
	}
}