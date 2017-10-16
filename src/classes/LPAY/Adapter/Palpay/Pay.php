<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay\PayParam;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Loger;
use LPAY\Pay\PayRender;
use LPAY\Exception;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;

use LPAY\Pay\Money;
class Pay extends Palpay implements PayAdapterCallback{
	const NAME="lpay_palpay";
	public function __construct(PayConfig $config){
		$this->set_name(self::NAME);
		$this->_config=$config;
	}
	/**
	 * @return PayConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function support_type(){
		return \LPAY\Pay::TYPE_WAP|\LPAY\Pay::TYPE_PC|\LPAY\Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return Pay::NAME;
	}
	public function match($name){
		if ($name==Pay::NAME) return true;
	}
	
	public function pay_render(PayParam $pay_param){
		$config=$this->_config();
		$notify_url=$this->_config->get_notify_url();
		$return_url=$this->_config->get_return_url();
		$mode=$this->_config->get_mode();//"sandbox",'live'
		$currencyCode=$this->_config->get_currency_code();//"USD"
		$payment_type=$this->_config->get_payment_type();//Sale
		
		$show_url=$pay_param->get_show_url();
		$cancel_url=$pay_param->get_cancel_url();
		$out_trade_no=$pay_param->get_sn();
		$total_fee=$pay_param->get_pay_money($this->_config->get_currency());
		$subject=$pay_param->get_title();
		$body=$pay_param->get_body();

		// details about payment
		$paymentDetails = new PaymentDetailsType();
		
		$itemDetails = new PaymentDetailsItemType();
		$itemDetails->Name = $subject;
		$itemDetails->Description =$body;
		$itemDetails->ItemURL=$show_url;
		$itemDetails->Amount = $total_fee;
		$itemDetails->Quantity = 1;
		$itemDetails->ItemCategory = 'Physical';
		
		
		
		$paymentDetails->PaymentDetailsItem[0] = $itemDetails;
		$paymentDetails->TaxTotal = new BasicAmountType($currencyCode, 0);
		$paymentDetails->OrderTotal = new BasicAmountType($currencyCode, $total_fee);
		$paymentDetails->PaymentAction =$payment_type;
		$paymentDetails->InvoiceID=$out_trade_no;
		
		$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
		$setECReqDetails->PaymentDetails[0] = $paymentDetails;
		$setECReqDetails->CancelURL = $cancel_url;
		$setECReqDetails->ReturnURL = $return_url;
		$setECReqDetails->NoShipping =1;
		$setECReqDetails->ReqConfirmShipping = 0;
		
		
		$setECReqType = new SetExpressCheckoutRequestType();
		$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
		$setECReq = new SetExpressCheckoutReq();
		$setECReq->SetExpressCheckoutRequest = $setECReqType;
		
		
		$paypalService = new PayPalAPIInterfaceServiceService($config);
		try {
			$setECResponse = $paypalService->SetExpressCheckout($setECReq);
		} catch (\Exception $ex) {
			throw new Exception($ex->getMessage(),$ex->getCode(),$ex);
		}
		if($setECResponse->Ack =='Success') {
			$token = $setECResponse->Token;
			if ($mode=='sandbox') $payPalURL = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
			else $payPalURL = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
			return new PayRender(PayRender::OUT_URL, $payPalURL);
		}else{
			$err=array_shift($setECResponse->Errors);
			throw new Exception($err->LongMessage,$err->ErrorCode);
		}
	}
	public function pay_callback(){
		$config=$this->_config();
		if (!isset($_REQUEST['token'])||!isset($_REQUEST['PayerID'])){
			return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		}
		$token = $_REQUEST['token'];
		
		//查询订单数据
		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;
		$paypalService = new PayPalAPIInterfaceServiceService($config);
		try {
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		} catch (\Exception $ex) {
			return PayResult::unkown($this->get_name(),$ex->getmessage());
		}
		if ($getECResponse->Ack!='Success'){
			$err=array_shift($getECResponse->Errors);
			return PayResult::unkown($this->get_name(),$err->LongMessage);
		}
		
		$out_trade_no=$getECResponse->GetExpressCheckoutDetailsResponseDetails->InvoiceID;
		
		
		
		//从用户palpay账户进行扣款
		$currencyCode=$this->_config->get_currency_code();//"USD"
		
		$orderTotal = new BasicAmountType();
		$orderTotal->currencyID = $currencyCode;
		$orderTotal->value = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value;;
		
		//扣款详细
		$paymentDetails= new PaymentDetailsType();
		$paymentDetails->OrderTotal = $orderTotal;
		$notify_url=$this->_config->get_notify_url();
		$paymentDetails->NotifyURL = $notify_url;
		
		
		$payerId=$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;
		$payment_type=$this->_config->get_payment_type();//sale
		
		$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
		$DoECRequestDetails->PayerID = $payerId;
		$DoECRequestDetails->Token = $token;
		$DoECRequestDetails->PaymentAction = $payment_type;
		$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;
		
		$DoECRequest = new DoExpressCheckoutPaymentRequestType();
		$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;
		
		
		$DoECReq = new DoExpressCheckoutPaymentReq();
		$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;
		
		try {
			/* wrap API method calls on the service object with a try catch */
			$DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
		} catch (\Exception $ex) {
			return PayResult::unkown($this->get_name(),$ex->getMessage());
		}
		
		if ($DoECResponse->Ack!='Success'){
			$err=array_shift($DoECResponse->Errors);
			return PayResult::unkown($this->get_name(),$err->LongMessage);
		}
		
		Loger::instance(Loger::TYPE_PAY_CALLBACK)->add($this->support_name(),$_GET);
		$out_trade_no=$getECResponse->GetExpressCheckoutDetailsResponseDetails->InvoiceID;
		$trade_no=$DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;
		$buyer_email=$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Payer;
		$total_fee=$getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value;
		$currency=$getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->currencyID;
		$result=PayResult::success($this->get_name(),$out_trade_no,$trade_no,$DoECResponse);
		$result->set_money(Money::factroy($total_fee,$this->_config->get_currency($currency)))
			->set_pay_account($buyer_email);
		return $result;
	}
}