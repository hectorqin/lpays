<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
use LPAY\Pay;
use LPAY\Pay\Adapter;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay\PayResult;
use LPAY\Pay\QueryParam;
use LPAY\Utils;
class PayWap extends PayNotify implements PayAdapterCallback{
	public static $save_key="__sn__";
	const NAME="lpay_qpay";
	public function __construct(PayWapConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	public function enable(){
		return true;
	}
	public function support_type(){
		return Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return PayWap::NAME;
	}
	public function match($name){
		if ($name==PayWap::NAME) return true;
	}
	/**
	 * next action : pay_js
	 * {@inheritDoc}
	 * @see \LPAY\Pay\PayAdapter::pay_render()
	 */
	public function pay_render(PayParam $pay_param){
		$body=$pay_param->get_title();
		$attach='';
		$sn=$pay_param->get_sn();
		$money=intval($pay_param->get_pay_money()*100);
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		$ctime=$pay_param->get_create_time();
		
		$param=array();
		$param['body']=$body;
		$param['attach']=$attach;
		$param['out_trade_no']=$sn;
		$param['fee_type']='CNY';
		$param['total_fee']=$money;
		$param['spbill_create_ip']=Utils::client_ip();
		$param['time_start']=date("YmdHis",$ctime);
		$param['time_expire']=date("YmdHis",$timeout);
		$param['trade_type']='JSAPI';
		$param['notify_url']=$this->_config->get_notify_url();
		//$param['limit_pay']='no_balance';
		//$param['contract_code']='';
		//$param['promotion_tag']='';
		//$param['device_info']='';
		
		$xml=Tools::get_to_xml($param, $this->_config);		
		$url="https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi";
		$result=Tools::post($url, $xml, $this->_config);
		$result=Tools::parse($result);
		//vars
		$html=$this->_render($sn,$result['prepay_id']);
		return new PayRender(PayRender::OUT_HTML, $html);
	}
	//render pay html
	protected function _render($sn,$tokenId){
		$return_url=$this->_config->get_return_url();
		
		$op=strpos($return_url, "?")!==false?"&":"?";
		$sn=Utils::encode_url($sn,$this->_config->get("key"));
		$return_url.=$op.self::$save_key."=".$sn;
		
		$cancel_url=$pay_param->get_cancel_url();
		$pubAcc=$this->_config->get("pubAcc");
		$pubAccHint=$this->_config->get("pubAccHint");
		ob_start();
		require_once Utils::lib_path("qpay/pay_page.php");
		$html=ob_get_contents();
		ob_end_clean();
		return $html;
	}
	public function pay_callback(){
		if (!isset($_GET[self::$save_key]))  return PayResult::unkown($this->get_name(),'pay sn not find');
		$sn=Utils::decode_url($_GET[self::$save_key],$this->_config->get("key"));
		if (empty($sn)) return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		$param=new QueryParam($sn, null, null);
		return $this->query($param);
	}
}