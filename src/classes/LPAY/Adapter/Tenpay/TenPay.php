<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Adapter\PayAdapter;
use LPAY\Pay\PayResult;
use LPAY\Pay\QueryParam;
use LPAY\Pay\Query;
use LPAY\Pay\PayAdapterNotify;
abstract class TenPay extends PayAdapter implements PayAdapterCallback,PayAdapterNotify,Query{
	/**
	 * @var PayConfig
	 */
	protected $_config;
	public function __construct(PayConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	/**
	 * @return PayConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function enable(){
		$config=$this->_config;
		if(empty($config->get_partner())||empty($config->get_key())) return false;
		return true;
	}
	public function query(QueryParam $param){
		$partner=$this->_config->get_partner();
		$key=$this->_config->get_key();
		$out_trade_no=$param->get_pay_sn();
		
		require_once Utils::lib_path("tenpay_wap/lib/classes/RequestHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/client/ClientResponseHandler.class.php");
		require_once Utils::lib_path("tenpay_wap/lib/classes/client/TenpayHttpClient.class.php");
		
		
		/* 创建支付请求对象 */
		$reqHandler = new \RequestHandler();
		
		//通信对象
		$httpClient = new \TenpayHttpClient();
		
		//应答对象
		$resHandler = new \ClientResponseHandler();
		
		//-----------------------------
		//设置请求参数
		//-----------------------------
		$reqHandler->init();
		$reqHandler->setKey($key);
		
		$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/normalorderquery.xml");
		$reqHandler->setParameter("partner", $partner);
		//out_trade_no和transaction_id至少一个必填，同时存在时transaction_id优先
		$reqHandler->setParameter("out_trade_no", $out_trade_no);
		//$reqHandler->setParameter("transaction_id", "2000000501201004300000000442");
		//-----------------------------
		//设置通信参数
		//-----------------------------
		$httpClient->setTimeOut(5);
		//设置请求内容
		$httpClient->setReqContent($reqHandler->getRequestURL());
		
		//后台调用
		if($httpClient->call()) {
			//设置结果参数
			$resHandler->setContent($httpClient->getResContent());
			$resHandler->setKey($key);
		
			//判断签名及结果
			//只有签名正确并且retcode为0才是请求成功
			if($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == "0" ) {
				//取结果参数做业务处理
				//商户订单号
				$out_trade_no = $resHandler->getParameter("out_trade_no");
		
				//财付通订单号
				$transaction_id = $resHandler->getParameter("transaction_id");
		
				//金额,以分为单位
				$total_fee = $resHandler->getParameter("total_fee");
		
				//支付结果
				$trade_state = $resHandler->getParameter("trade_state");
		
				//支付成功
				if($trade_state == "0") {
					return PayResult::success($this->get_name(),$out_trade_no, $transaction_id,$resHandler);
				}else{
					return PayResult::ing($this->get_name(),$out_trade_no,$resHandler, $transaction_id);
				}
// 				echo "OK,trade_state=" . $trade_state . ",is_split=" . $resHandler->getParameter("is_split") . ",is_refund=" . $resHandler->getParameter("is_refund") . "<br>";
			} else {
				return PayResult::unkown($this->get_name(),$resHandler->getParameter("retmsg"));
				//错误时，返回结果可能没有签名，记录retcode、retmsg看失败详情。
// 				echo "验证签名失败 或 业务错误信息:retcode=" . $resHandler->getParameter("retcode"). ",retmsg=" . $resHandler->getParameter("retmsg") . "<br>";
			}
		
		} else {
			return PayResult::unkown($this->get_name(),$httpClient->getErrInfo());
			//后台调用通信失败
// 			echo "call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
			//有可能因为网络原因，请求已经处理，但未收到应答。
		}
		//调试信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
		/*
		 echo "<br>------------------------------------------------------<br>";
		 echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
		 echo "req:" . htmlentities($reqHandler->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		 echo "res:" . htmlentities($resHandler->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		 echo "reqdebug:" . $reqHandler->getDebugInfo() . "<br><br>" ;
		 echo "resdebug:" . $resHandler->getDebugInfo() . "<br><br>";
		 */
	}
	public function pay_notify_output($status=true,$msg=null){
		if ($status){
			http_response_code(200);
			die("success");		//请不要修改或删除
		}else{
			echo "fail";
			die();
		}
	}
	
}