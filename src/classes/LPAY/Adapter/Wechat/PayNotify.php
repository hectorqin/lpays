<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;
use LPAY\Adapter\PayAdapter;
use LPAY\Loger;
use LPAY\Pay\PayResult;
use LPAY\Utils;
use LPAY\Pay\Query;
use LPAY\Pay\QueryParam;
use LPAY\Pay\PayAdapterNotify;
use LPAY\Pay\Reverse;
use LPAY\Pay\ReverseParam;
use LPAY\Pay\ReverseResult;

abstract class PayNotify extends PayAdapter implements Query,Reverse,PayAdapterNotify{
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
	protected $_name;
	public function set_name($name){
		$this->_name=$name;
		return $this;
	}
	public function get_name(){
		return $this->_name;
	}
	
	public function pay_notify(){
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$xml=file_get_contents("php://input");
		try {
			$result = \WxPayResults::Init($xml);
		} catch (\WxPayException $e){
			if ($e->getCode()===888) return PayResult::unkown($this->get_name(), PayResult::$sign_invalid);
			return  PayResult::unkown($this->get_name(),$e->getMessage());
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$xml);
		if (!isset($result["out_trade_no"])
			||!isset($result["transaction_id"])
			||!isset($result["total_fee"])
			||!isset($result["openid"])
		){
			return  PayResult::unkown($this->get_name(),$xml);
		}
		$out_trade_no=$result["out_trade_no"];
		$trade_no=$result["transaction_id"];
		$total_fee=round($result["total_fee"]/100,2);
		$openid=$result["openid"];
		if (isset($result['return_code'])&&$result['return_code']=='SUCCESS'
			&&isset($result['result_code'])&&$result['result_code']=='SUCCESS'){
			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$result);
			$result->set_money($total_fee)->set_pay_account($openid);
			return $result;
		}
		return  PayResult::ing($this->get_name(),$out_trade_no,$result,$openid);
// 		try{
// 			$input = new \WxPayOrderQuery();
// 			$input->SetTransaction_id($trade_no);
// 			$_result = \WxPayApi::orderQuery($input);
// 		}catch (\WxPayException $e){
// 			Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$e);
// 			return  PayResult::unkown($this->get_name(),$e->getMessage());
// 		}
// 		if(array_key_exists("return_code", $_result)
// 			&& array_key_exists("result_code", $_result)
// 			&& $_result["return_code"] == "SUCCESS"
// 			&& $_result["result_code"] == "SUCCESS")
// 		{
// 			$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$result);
// 			$result->set_money($total_fee)->set_pay_account($openid);
// 			return $result; 
// 		}
// 		return  PayResult::ing($this->get_name(),$out_trade_no,$result,$openid);
	}
	//out to wechat pay
	public function pay_notify_output($status=true,$msg='OK'){
		$return = new \WxPayNotifyReply();
		$return->SetReturn_code($status?'SUCCESS':"FAIL");
		$return->SetReturn_msg($msg);
		\WxpayApi::replyNotify($return->ToXml());
		exit;
	}
	public function query(QueryParam $param){
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$out_trade_no = $param->get_pay_sn();
		$input = new \WxPayOrderQuery();
		$input->SetOut_trade_no($out_trade_no);
		try{
			$_result = \WxPayApi::orderQuery($input);
		}catch (\WxPayException $e){
			return PayResult::unkown($this->get_name(),$e->getMessage());
		}
		if(array_key_exists("return_code", $_result)
				&& array_key_exists("result_code", $_result)
				&& $_result["return_code"] == "SUCCESS"
				&& $_result["result_code"] == "SUCCESS")
		{
			switch ($_result["trade_state"]){
				case 'SUCCESS':
					return PayResult::success($this->get_name(),$out_trade_no, $_result['transaction_id'],$_result);
				break;
				case 'USERPAYING':
					return PayResult::ing($this->get_name(),$out_trade_no,$_result,@$_result['transaction_id']);
				break;
				case 'NOTPAY':
				case 'REVOKED':
				case 'CLOSED':
				case 'PAYERROR':
					return PayResult::fail($this->get_name(),$out_trade_no, @$_result['transaction_id'],$_result["trade_state_desc"]);
				break;
			}
		}
		return PayResult::unkown($this->get_name(),$_result["return_msg"]);
	}
	public function reverse(ReverseParam $param){
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$input = new \WxPayReverse();
		$input->SetOut_trade_no($param->get_pay_sn());
		$input->SetTransaction_id($param->get_pay_no());
		try{
			$_result = \WxPayApi::reverse($input);
		}catch (\WxPayException $e){
			return ReverseResult::unkown($this->get_name(),$e->getMessage());
		}
		if(array_key_exists("return_code", $_result)
				&& array_key_exists("result_code", $_result)
				&& $_result["return_code"] == "SUCCESS"
				&& $_result["result_code"] == "SUCCESS")
		{
			if ($_result["recall"]=='N'){
				return ReverseResult::success($this->get_name(),$_result);
			}else{
				switch ($_result['err_code']){
					case 'SYSTEMERROR':
						return ReverseResult::ing($this->get_name(),$_result);
					break;
					case 'REVERSE_EXPIRE':
						return ReverseResult::success($this->get_name(),$_result);
					break;
					default:
					return ReverseResult::fail($this->get_name(), $_result['err_code_des']);
				}
			}
		}
		return ReverseResult::unkown($this->get_name(),$_result["return_msg"]);
	}
	
}