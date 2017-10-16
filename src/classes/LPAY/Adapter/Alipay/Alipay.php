<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Adapter\PayAdapter;
use LPAY\Pay\Query;
use LPAY\Pay\QueryParam;
use LPAY\Pay\PayResult;
use LPAY\Pay\PayAdapterNotify;
use LPAY\Utils;
abstract class Alipay extends PayAdapter implements Query,PayAdapterNotify{
	/**
	 * @var PayConfig
	 */
	protected $_config;
	/**
	 * @return PayConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function query(QueryParam $param){
		$this->_config->set_md5();
		$alipay_config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_trade_query/lib/alipay_submit.class.php");
		/**************************请求参数**************************/
		//支付宝交易号
		$trade_no =$param->get_pay_no();
		//支付宝交易号与商户网站订单号不能同时为空
		//商户订单号
		$out_trade_no =$param->get_pay_sn();
		/************************************************************/
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "single_trade_query",
				"partner" => trim($alipay_config['partner']),
				"trade_no"	=> $trade_no,
				"out_trade_no"	=> $out_trade_no,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		//解析XML
		//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
		$xml = simplexml_load_string($html_text);
		$data = json_decode(json_encode($xml),TRUE);
		if (isset($data['is_success'])&&$data['is_success']=='T'){
			if (isset($data['response']['trade']['trade_status'])){
				if ($data['response']['trade']['trade_status']=='TRADE_SUCCESS'
					||$data['response']['trade']['trade_status']=='TRADE_FINISHED'){
					$out_trade_no=isset($data['response']['trade']['out_trade_no'])?$data['response']['trade']['out_trade_no']:$out_trade_no;
					$trade_no=isset($data['response']['trade']['trade_no'])?$data['response']['trade']['trade_no']:$trade_no;
					$succ=PayResult::success($this->get_name(),$out_trade_no, $trade_no,$data);
					isset($data['response']['trade']['total_fee'])&&$succ->set_money($data['response']['trade']['total_fee']);
					isset($data['response']['trade']['buyer_email'])&&$succ->set_pay_account($data['response']['trade']['buyer_email']);
					return $succ;
				}
			}
			return PayResult::ing($this->get_name(),$out_trade_no, $data,$trade_no);
		}else if (isset($data['error'])&&$data['error']=='ILLEGAL_PARTNER_EXTERFACE'){
			//未签约
			return PayResult::ing($this->get_name(),$out_trade_no, $data,$trade_no);
		}else{
			return PayResult::fail($this->get_name(),$out_trade_no,$trade_no,@$data['error']);
		}
	}
	public function pay_notify_output($status=true,$msg=null){
		if ($status){
			http_response_code(200);
			echo "success";
			die();
		}else{
			$msg&&$msg=":".$msg;
			echo "fail".$msg;
			die();
		}
	}
}