<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
class Refund extends RefundAdapter{
	/**
	 * @var RefundConfig
	 */
	protected $_config;
	public function __construct(RefundConfig $config){
		$this->_config=$config;
	}
	/**
	 * @return RefundConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	/**
	 * @return string
	 */
	public function support_name(){
		return array(PayWeb::NAME,PayWap::NAME);
	}
	public function enable(){
		$config=$this->_config;
		if(empty($config->get_partner())||empty($config->get_key())) return false;
		return true;
	}
	/**
	 * refund money
	 * @param RefundParam $refund_param
	 * @return RefundResult
	 */
	public function refund(RefundParam $refund_param){
		$msg=$refund_param->get_refund_msg();
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = intval($refund_param->get_refund_pay_money()*100);
		$total_money = intval($refund_param->get_total_pay_money()*100);
		$return_no = $refund_param->get_return_no();
		
		
		
	
		require_once Utils::lib_path("tenpay/lib/classes/RequestHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/client/ClientResponseHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/client/TenpayHttpClient.class.php");
		
		
		$partner=$this->_config->get_partner();
		$key=$this->_config->get_key();
		
		$op_user_id=$this->_config->get_op_user_id();
		$op_user_passwd=$this->_config->get_op_user_passwd();
		$get_ca_path=$this->_config->get_ca_path();
		$get_pem_path=$this->_config->get_pem_path();
		$get_pem_passwd=$this->_config->get_pem_passwd();
		
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
		
		$reqHandler->setGateUrl("https://mch.tenpay.com/refundapi/gateway/refund.xml");
		$reqHandler->setParameter("partner", $partner);
		
		//out_trade_no和transaction_id至少一个必填，同时存在时transaction_id优先
		//$reqHandler->setParameter("out_trade_no", "201309170922391065");
		$reqHandler->setParameter("transaction_id",$recharge_pay_no);
		//必须保证全局唯一，同个退款单号财付通认为是同笔请求
		$reqHandler->setParameter("out_refund_no",$return_no);
		$reqHandler->setParameter("total_fee", $total_money);
		$reqHandler->setParameter("refund_fee", $refund_money);
		$reqHandler->setParameter("op_user_id", $op_user_id);
		//操作员密码,MD5处理
		$reqHandler->setParameter("op_user_passwd", md5($op_user_passwd));
		//接口版本号,取值1.1
		$reqHandler->setParameter("service_version", "1.1");
		
		
		//-----------------------------
		//设置通信参数
		//-----------------------------
		//设置PEM证书，pfx证书转pem方法：openssl pkcs12 -in 2000000501.pfx  -out 2000000501.pem
		//证书必须放在用户下载不到的目录，避免证书被盗取
		$httpClient->setCertInfo($get_pem_path, $get_pem_passwd);
		//设置CA
		$httpClient->setCaInfo($get_ca_path);
		$httpClient->setTimeOut(5);
		$httpClient->setMethod("POST");
		//设置请求内容
		$httpClient->setReqContent($reqHandler->getRequestURL());
		
		//后台调用
		if(!$httpClient->call()){
			$msg=$httpClient->getErrInfo();
			return RefundResult::fail($this->get_name(),$return_no, $msg);
		}
		//设置结果参数
		$resHandler->setContent($httpClient->getResContent());
		$resHandler->setKey($key);
		//判断签名及结果
		//只有签名正确并且retcode为0才是请求成功
		if($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == 0 ) {
			//取结果参数做业务处理
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//商户退款单号
			$out_refund_no = $resHandler->getParameter("out_refund_no");
			//财付通退款单号
			$refund_id = $resHandler->getParameter("refund_id");
			//退款金额,以分为单位
			$refund_fee = $resHandler->getParameter("refund_fee");
			//退款状态
			$refund_status = $resHandler->getParameter("refund_status");
	
// 			echo "OK,refund_status=" . $refund_status . ",out_refund_no=" . $resHandler->getParameter("out_refund_no") . ",refund_fee=" . $resHandler->getParameter("refund_fee") . "<br>";
			return RefundResult::success($this->get_name(),$return_no,$refund_id,$resHandler);
		} else {
			$msg=$resHandler->getParameter("retmsg");
			return RefundResult::fail($this->get_name(),$return_no, $msg);
		}
	
	}
}