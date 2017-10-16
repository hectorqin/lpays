<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Tenpay;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Loger;

use LPAY\Exception;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
class PayWeb extends TenPay implements PayAdapterCallback{
	const NAME="lpay_tenpay_web";
	public function support_type(){
		return Pay::TYPE_PC;
	}
	public function support_name(){
		return PayWeb::NAME;
	}
	public function match($name){
		if ($name==PayWeb::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		$partner=$this->_config->get_partner();
		$key=$this->_config->get_key();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		
		
		$show_url=$pay_param->get_show_url();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=intval($pay_param->get_pay_money()*100);
		$subject=$pay_param->get_title();
		
		/* 获取提交的商品名称 */
		$product_name=$pay_param->get_title();
		
		/* 获取提交的备注信息 */
		$remarkexplain=$pay_param->get_body();
		
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		
		/* 支付方式 */
		$trade_mode=1;
		/* 商品名称 */
		$desc = $subject;
		
		require_once Utils::lib_path("tenpay/lib/classes/RequestHandler.class.php");
		
		$ctime=$pay_param->get_create_time();
		/* 创建支付请求对象 */
		$reqHandler = new \RequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($key);
		$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
		
		//----------------------------------------
		//设置支付参数
		//----------------------------------------
		$reqHandler->setParameter("partner", $partner);
		$reqHandler->setParameter("out_trade_no", $out_trade_no);
		$reqHandler->setParameter("total_fee", $total_fee);  //总金额
		$reqHandler->setParameter("return_url", $return_url);
		$reqHandler->setParameter("notify_url", $notify_url);
		$reqHandler->setParameter("body", $remarkexplain);
		$reqHandler->setParameter("bank_type", "DEFAULT");  	  //银行类型，默认为财付通
		//用户ip
		$reqHandler->setParameter("spbill_create_ip", Utils::client_ip());//客户端IP
		$reqHandler->setParameter("fee_type", "1");               //币种
		$reqHandler->setParameter("subject",$desc);          //商品名称，（中介交易时必填）
		
		//系统可选参数
		$reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
		$reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
		$reqHandler->setParameter("input_charset", "utf-8");   	  //字符集
		$reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号
		
		//业务可选参数
		$reqHandler->setParameter("attach", "");             	  //附件数据，原样返回就可以了
		$reqHandler->setParameter("product_fee", "");        	  //商品费用
		$reqHandler->setParameter("transport_fee", "0");      	  //物流费用
		$reqHandler->setParameter("time_start", date("YmdHis",$ctime));  //订单生成时间
		$reqHandler->setParameter("time_expire", date("YmdHis",$timeout));             //订单失效时间
		$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
		$reqHandler->setParameter("goods_tag", "");               //商品标记
		$reqHandler->setParameter("trade_mode",$trade_mode);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
		$reqHandler->setParameter("transport_desc","");              //物流说明
		$reqHandler->setParameter("trans_type","1");              //交易类型
		$reqHandler->setParameter("agentid","");                  //平台ID
		$reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
		$reqHandler->setParameter("seller_id","");                //卖家的商户号
		
		$reqHandler->getRequestURL();
		//请求的URL
		//获取debug信息,建议把请求和debug信息写入日志，方便定位问题
		/**/
		$form='<form id="tenpay" action="'.$reqHandler->getGateUrl() .'" method="post">';
		$params = $reqHandler->getAllParameters();
		
		foreach($params as $k => $v) {
			$form.="<input type='hidden' name='{$k}' value='{$v}' />\n";
		}
		$form.='<input type="submit" style="display:none;">';
		$form.="<script>document.forms['tenpay'].submit();</script>";
		$form.="</form>";
		
		return new PayRender(PayRender::OUT_HTML, $form);
	}
	protected function _verify(){
		$key=$this->_config->get_key();
		require_once Utils::lib_path("tenpay/lib/classes/ResponseHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/function.php");
		$resHandler = new \ResponseHandler();
		$resHandler->setKey($key);
		$t=$resHandler->getAllParameters();
		if (!isset($t['sign'])) return false;
		return $resHandler;
	}
	public function pay_callback(){
		$resHandler=$this->_verify();
		if(!$resHandler||!$resHandler->isTenpaySign()){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_GET);
		//通知id
		$notify_id = $resHandler->getParameter("notify_id");
		//商户订单号
		$out_trade_no = $resHandler->getParameter("out_trade_no");
		//财付通订单号
		//$transaction_id = $resHandler->getParameter("transaction_id");
		//金额,以分为单位
		//$total_fee = $resHandler->getParameter("total_fee");
		//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
		$discount = $resHandler->getParameter("discount");
		//支付结果
		$trade_state = $resHandler->getParameter("trade_state");
		//交易模式,1即时到账
		$trade_mode = $resHandler->getParameter("trade_mode");
	
	
		if("1" != $trade_mode ) throw new Exception('not support trade_mode.');
		
		
		
		$trade_no=$resHandler->getParameter("transaction_id");;
		$total_fee = $resHandler->getParameter("total_fee")/100;
		$buyer_email=$resHandler->getParameter('buyer_alias');
		
		$result= PayResult::success($this->get_name(),$out_trade_no,$trade_no,$resHandler);
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return $result;
	}
	public function pay_notify(){
		ignore_user_abort(true);
		require_once Utils::lib_path("tenpay/lib/classes/ResponseHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/RequestHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/client/ClientResponseHandler.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/client/TenpayHttpClient.class.php");
		require_once Utils::lib_path("tenpay/lib/classes/function.php");
		$resHandler=$this->_verify();
		if(!$resHandler||!$resHandler->isTenpaySign()){
			return  PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		Loger::instance(Loger::TYPE_PAY_NOTIFY)->add($this->support_name(),$_POST);
		
		$key=$this->_config->get_key();
		$partner=$this->_config->get_partner();
		//通知id
		$notify_id = $resHandler->getParameter("notify_id");
		//通过通知ID查询，确保通知来至财付通
		//创建查询请求
		$queryReq = new \RequestHandler();
		$queryReq->init();
		$queryReq->setKey($key);
		$queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
		$queryReq->setParameter("partner", $partner);
		$queryReq->setParameter("notify_id", $notify_id);
	
		//通信对象
		$httpClient = new \TenpayHttpClient();
		$httpClient->setTimeOut(5);
		//设置请求内容
		$httpClient->setReqContent($queryReq->getRequestURL());
		//后台调用
		if(!$httpClient->call()){
			return  PayResult::unkown($this->get_name(),$httpClient->getErrInfo());
		}
		//设置结果参数
		$queryRes = new \ClientResponseHandler();
		$queryRes->setContent($httpClient->getResContent());
		$queryRes->setKey($key);
	
		$log=$queryRes->getContent();
		$trade_mode=$resHandler->getParameter("trade_mode");
		if($trade_mode != "1"){
			return  PayResult::unkown($this->get_name(),'wrong trade mode :'.$trade_mode);
		}
		//判断签名及结果（即时到帐）
		//只有签名正确,retcode为0，trade_state为0才是支付成功
		if(!($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $resHandler->getParameter("trade_state") == "0")){
			return  PayResult::unkown($this->get_name(),'query res wrong');
		}
		//取结果参数做业务处理
		$out_trade_no = $resHandler->getParameter("out_trade_no");
		$discount = $resHandler->getParameter("discount");
		$trade_no=$resHandler->getParameter("transaction_id");;
		$total_fee = $resHandler->getParameter("total_fee")/100;
		$buyer_email=$resHandler->getParameter('buyer_alias');
		
		$result= PayResult::success($this->get_name(),$out_trade_no,$trade_no,$resHandler);
		$result->set_money($total_fee)->set_pay_account($buyer_email);
		return  $result;
	}
}