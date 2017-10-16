<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
use LPAY\Utils;
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
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = intval($refund_param->get_refund_pay_money()*100);
		$total_money = intval($refund_param->get_total_pay_money()*100);
		$return_no = $refund_param->get_return_no();

		$transaction_id =$recharge_pay_no;
		$total_fee = $total_money;
		$refund_fee = $refund_money;
		$input = new \WxPayRefund();
		$input->SetTransaction_id($transaction_id);
		$input->SetTotal_fee($total_fee);
		$input->SetRefund_fee($refund_fee);
		$input->SetOut_refund_no($return_no);
		$input->SetOp_user_id($this->_config->get_WxPayConfigObj()->MCHID);
		try{
			$result=\WxPayApi::refund($input);
		}catch (\WxPayException $e){
			return RefundResult::fail($this->get_name(),$return_no, $e->getMessage());
		}
		if ($result["return_code"] != "SUCCESS"){
			return RefundResult::fail($this->get_name(),$return_no, $result['return_msg']);
		}
		if($result["result_code"]!='SUCCESS'){
			return RefundResult::fail($this->get_name(),$return_no, $result['err_code_des']);
		}
		return RefundResult::success($this->get_name(),$return_no,$result['refund_id'],$result);
	}
}