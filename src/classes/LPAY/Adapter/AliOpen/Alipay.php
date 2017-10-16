<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\AliOpen;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
use LPAY\Adapter\PayAdapter;
class Alipay extends PayAdapter  implements PayAdapterCallback{
	//支付宝2.0版支付接口,未完成..
	const NAME="lpay_aliopen";
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
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	public function enable(){
		$alipay_config=$this->_config->as_array();
		$check=array('app_id','private_key_path','ali_public_key_path');
		foreach ($check as $v){
			if(empty($alipay_config[$v])) return false;
		}
		return !Utils::user_agent(Utils::BROWSER_WECHAT);
	}
	public function support_type(){
		return Pay::TYPE_WAP|Pay::TYPE_ANDROID|Pay::TYPE_IOS;
	}
	public function support_name(){
		return self::NAME;
	}
	public function match($name){
		if ($name==self::NAME) return true;
	}
	/**
	 * {@inheritDoc}
	 */
	public function pay_render(PayParam $pay_param){
		$config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_aop/AopSdk.php");
		$aop = new \AopClient ();
		$aop->gatewayUrl = $this->_config->get_gateway();
		$aop->appId = $config['app_id'];
		$aop->rsaPrivateKeyFilePath = $config['private_key_path'];
		$aop->alipayPublicKey= $config['ali_public_key_path'];
		$aop->apiVersion = '1.0';
		$aop->postCharset='utf-8';
		$aop->format='json';
		// 开启页面信息输出
// 		$aop->debugInfo=true;
		
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$disable_pay_channels=$this->_config->get_disable_pay_channels();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=$pay_param->get_pay_money();
		$subject=$pay_param->get_title();
		$end_url=$pay_param->get_cancel_url();
		
		$request = new \AlipayTradeWapPayRequest();
		$timeout_express=($pay_param->get_timeout()/60)."m";
		$out_trade_no=$pay_param->get_sn();
		$subject=$pay_param->get_title();
		$body=$pay_param->get_body();
		$money=$pay_param->get_money();
		$request->setNotifyUrl($notify_url);
		$request->setReturnUrl($return_url);
		$p=array(
			"body"=>$body,
			"subject"=>$subject,
			"out_trade_no"=>$out_trade_no,
			"timeout_express"=>$timeout_express,
			"total_amount"=>$money,
			"product_code"=>'QUICK_WAP_PAY',
		);
		if (!empty($disable_pay_channels))$p['disable_pay_channels']=$disable_pay_channels;;
		$request->setBizContent(json_encode($p));
		$result = $aop->pageExecute($request,"post");
		return new PayRender(PayRender::OUT_HTML, $result);
	}
	public function pay_callback(){
		
	}
	public function pay_notify(){
		ignore_user_abort(true);
	}
}