<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
use LPAY\Exception;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\PayResult;
use LPAY\Pay\QueryParam;
use LPAY\Pay\PayAdapterCallback;
class PayCode extends PayNotify implements PayAdapterCallback{
	const NAME="lpay_qq_code";
	public static $save_key='__sn__';
	/**
	 * @var PayCodeConfig
	 */
	protected $_config;
	public function __construct(PayCodeConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	/**
	 * @return PayCodeConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function support_name(){
		return PayCode::NAME;
	}
	public function match($name){
		if ($name==PayCode::NAME) return true;
	}
	public function enable(){
		return true;
	}
	public function support_type(){
		return Pay::TYPE_PC;
	}
	public function pay_render(PayParam $pay_param){
		
		$notify_url= $this->_config->get_notify_url();
		$qrcode_url= $this->_config->get_qrcode_url();
		$return_url= $this->_config->get_return_url();
		$check_url= $this->_config->get_check_url();
		
		
		$op=strpos($return_url, "?")!==false?"&":"?";
		$sn=Utils::encode_url($pay_param->get_sn(),$this->_config->get("key"));
		$return_url.=$op.self::$save_key."=".$sn;
		
		$body=$pay_param->get_body();
		$attach='';
		$sn=$pay_param->get_sn();
		$money=intval($pay_param->get_pay_money()*100);
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		$pids=implode(",",$pay_param->get_goods());
		if(empty($pids))$pids='0';
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
		$param['trade_type']='NATIVE';
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
		if (!isset($result["code_url"])){
			throw new Exception('wrong wechat return:'.$result['return_msg']);
		}
		$url = $result["code_url"];
		return new PayRender(PayRender::OUT_QRCODE, array(
			'code_url'=>$url,
			'sn'=>$sn,
			'qrcode_url'=>$qrcode_url,
			'return_url'=>$return_url,
			'check_url'=>$check_url,
		));
	}
	public function pay_callback(){
		if (!isset($_GET[self::$save_key]))  return PayResult::unkown($this->get_name(),'pay sn not find');
		$sn=Utils::decode_url($_GET[self::$save_key],$this->_config->get("key"));
		if (empty($sn)) return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		$param=new QueryParam($sn, null, null);
		return $this->query($param);
	}
}