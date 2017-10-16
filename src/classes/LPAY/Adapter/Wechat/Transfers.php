<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;
use LPAY\Transfers\TransfersParam;
use LPAY\Utils;
use LPAY\Transfers\TransfersResult;
use LPAY\Transfers\TransfersAdapter\RealTime;
class Transfers implements RealTime{
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
	public function enable(){
		return true;
	}
	public function fee(){
		return 0;
	}
	public function min_fee(){
		return 0;
	}
	public function max_fee(){
		return 0;
	}
	/**
	 * @return string
	 */
	public function transfers_name(){
		return Transfers::NAME;
	}
	public function real_transfers(TransfersParam $param){
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		
		$openid=$param->get_pay_account();
		$no=$param->get_transfers_no();
		$name=$param->get_pay_name();
		$money=round($param->get_pay_money()*100);
		$desc=strip_tags($param->get_pay_msg());
		
		$input=new \WxPayTransfers();
		$input->SetPartner_trade_no($no);
		$input->SetOpenid($openid);
		$input->SetRe_user_name($name);
		$input->SetAmount($money);
		$input->SetDesc($desc);
		$input->SetSpbill_create_ip(Utils::client_ip());
		$input->SetMch_appid(\WxPayApi::$config->APPID);
		
		
		try{
			$result=\WxPayApi::transfers($input);
		}catch (\WxPayException $e){
			return TransfersResult::fail($this->transfers_name(),$no, null,$e->getMessage());
		}
		if ($result["return_code"] != "SUCCESS"){
			return TransfersResult::fail($this->transfers_name(),$no, @$result['payment_no'], $result['return_msg']);
		}
		if($result["result_code"]!='SUCCESS'){
			return TransfersResult::fail($this->transfers_name(),$no, @$result['payment_no'], $result['err_code_des']);
		}
		return TransfersResult::success($this->transfers_name(),$no, @$result['payment_no'],$result);
	}
}