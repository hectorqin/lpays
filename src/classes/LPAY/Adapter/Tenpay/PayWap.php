<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
use LPAY\Pay\PayAdapterCallback;

use LPAY\Pay\PayResult;
use LPAY\Result;
use LPAY\Loger;
use LPAY\Exception;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
class PayWap extends TenPay implements PayAdapterCallback{
	const NAME="lpay_tenpay_wap";
	public function support_type(){
		return Pay::TYPE_WAP|Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return PayWap::NAME;
	}
	public function match($name){
		if ($name==PayWap::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		
		$partner=$this->_config->get_partner();
		$key=$this->_config->get_key();
		
		
		$show_url=$pay_param->get_show_url();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=intval($pay_param->get_pay_money()*100);
		
		$subject=$pay_param->get_title();
		
		$attach='';
		
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		
		
	
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		
		require_once Utils::lib_path("tenpay_wap/lib/classes/RequestHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/client/ClientResponseHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/client/TenpayHttpClient.class.php");
		/* 商户号 */
		/* 创建支付请求对象 */
		$reqHandler = new \RequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($key);
		//$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
		//设置初始化请求接口，以获得token_id
		$reqHandler->setGateUrl("http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");
		
		$httpClient = new \TenpayHttpClient();
		//应答对象
		$resHandler = new \ClientResponseHandler();
		//----------------------------------------
		//设置支付参数
		//----------------------------------------
		
		
		$reqHandler->setParameter("total_fee", $total_fee);  //总金额
		//用户ip
		$reqHandler->setParameter("spbill_create_ip", Utils::client_ip());//客户端IP
		$reqHandler->setParameter("ver", "2.0");//版本类型
		$reqHandler->setParameter("bank_type", "0"); //银行类型，财付通填写0
		$reqHandler->setParameter("callback_url", $return_url);//交易完成后跳转的URL
		$reqHandler->setParameter("bargainor_id", $partner); //商户号
		$reqHandler->setParameter("sp_billno", $out_trade_no); //商户订单号
		$reqHandler->setParameter("notify_url",$notify_url);//接收财付通通知的URL，需绝对路径
		$reqHandler->setParameter("desc", $subject);
		$reqHandler->setParameter("time_expire", date("YmdHis",$timeout));
		$reqHandler->setParameter("attach", $attach);
		
		$httpClient->setReqContent($reqHandler->getRequestURL());
		
		//后台调用
		if(!$httpClient->call()) throw new Exception($httpClient->getErrInfo());
		
		$resHandler->setContent($httpClient->getResContent());
		//获得的token_id，用于支付请求
		$token_id = $resHandler->getParameter('token_id');
		//$reqHandler->setParameter("token_id", $token_id);
		//$reqHandler->setGateUrl("https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi");
		//此次请求只需带上参数token_id就可以了，$reqUrl和$reqUrl2效果是一样的
		//$reqUrl = $reqHandler->getRequestURL();
		$reqUrl = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=".$token_id;
		
		
		return new PayRender(PayRender::OUT_URL, $reqUrl);
		
	}

	public function pay_callback(){
	
		require_once Utils::lib_path("tenpay_wap/lib/classes/ResponseHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/WapResponseHandler.class.php");
		$key=$this->_config->get_key();
		/* 创建支付应答对象 */
		$resHandler = new \WapResponseHandler();
		$resHandler->setKey($key);
		$t=$resHandler->getAllParameters();
		//判断签名
		if(!isset($t['sign'])||!$resHandler->isTenpaySign()) return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		
		//商户号
		$bargainor_id = $resHandler->getParameter("bargainor_id");
		//财付通交易单号
		$trade_no = $resHandler->getParameter("transaction_id");
		//金额,以分为单位
		$total_fee = $resHandler->getParameter("total_fee")/100;
		//支付结果
		$pay_result = $resHandler->getParameter("pay_result");
	
		if( "0" != $pay_result  ){
			return PayResult::fail($this->get_name(),$out_trade_no, $trade_no, $pay_result);
		}
		$buyer_email = $resHandler->getParameter("purchaser_id");
		$out_trade_no = $resHandler->getParameter("sp_billno");
		
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_GET);
		$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$_GET);
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return $result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		
		require_once Utils::lib_path("tenpay_wap/lib/classes/ResponseHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/WapNotifyResponseHandler.class.php");
		$partner=$this->_config->get_partner();
		$key=$this->_config->get_key();
		
		
		/* 创建支付应答对象 */
		$resHandler = new \WapNotifyResponseHandler();
		$resHandler->setKey($key);
		$t=$resHandler->getAllParameters();
		//判断签名
		if(!isset($t['sign'])||!$resHandler->isTenpaySign()){
			return  PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
			die('fail');
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$_POST);
		
		//商户号
		$bargainor_id = $resHandler->getParameter("bargainor_id");
	
		//财付通交易单号
		$trade_no = $resHandler->getParameter("transaction_id");
		//金额,以分为单位
		$total_fee = $resHandler->getParameter("total_fee")/100;
	
		//支付结果
		$pay_result = $resHandler->getParameter("pay_result");
	
		if( "0" != $pay_result  ) {
			return  PayResult::fail($this->get_name(),$out_trade_no, $trade_no, $pay_result);
		}
		
		$buyer_email = $resHandler->getParameter("purchaser_id");
		$out_trade_no = $resHandler->getParameter("sp_billno");
		
		$result= PayResult::success($this->get_name(),$out_trade_no,$trade_no,$resHandler);
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		
		return   $result;
	}
}