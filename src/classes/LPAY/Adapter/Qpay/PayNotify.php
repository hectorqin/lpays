<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
use LPAY\Adapter\PayAdapter;
use LPAY\Loger;

use LPAY\Pay\PayResult;
use LPAY\Pay\Query;
use LPAY\Pay\QueryParam;
use LPAY\Pay\PayAdapterNotify;

abstract class PayNotify extends PayAdapter implements Query,PayAdapterNotify{
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
		//@todo...
		$xml=file_get_contents("php://input");
		try {
			$result=Tools::parse($result);
		} catch (\Exception $e){
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
	
	}
	//out to wechat pay
	public function pay_notify_output($status=true,$msg='OK'){
		die(Tools::to_xml(array(
			'return_code'=>$status?'SUCCESS':"FAIL",
			'return_msg'=>strip_tags($msg)
		)));
	}
	public function query(QueryParam $param){
		$out_trade_no = $param->get_pay_sn();
		$tid=$param->get_pay_no();
		$param=array();
		$tid&&$param['transaction_id']= $tid;
		$param['out_trade_no']=$out_trade_no;
		$xml=Tools::get_to_xml($param, $this->_config);
		$url="https://qpay.qq.com/cgi-bin/pay/qpay_order_query.cgi";
		$result=Tools::post($url, $xml, $this->_config);
		$_result=Tools::parse($result);
		//vars
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
	
}