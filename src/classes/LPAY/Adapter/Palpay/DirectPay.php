<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;
use LPAY\Pay\PayParam;
use LPAY\Result;
use LPAY\Pay\PayResult;
use LPAY\Pay\PayRender;
use LPAY\Exception;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\PayPalAPI\DoDirectPaymentRequestType;
use PayPal\PayPalAPI\DoDirectPaymentReq;
use PayPal\EBLBaseComponents\DoDirectPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\CreditCardDetailsType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\PersonNameType;
use PayPal\EBLBaseComponents\PayerInfoType;

class DirectPay extends Palpay{
	const NAME="lpay_palpay_direct";
	public function __construct(DirectPayConfig $config){
		$this->set_name(self::NAME);
		$this->_config=$config;
	}
	protected function _init_sess(){
		if(!session_id()) session_start();
	}
	
	/**
	 * @return DirectPayConfig
	 */
	public function get_config(){
		return $this->_config;
	}
	public function support_type(){
		return \LPAY\Pay::TYPE_WAP|\LPAY\Pay::TYPE_PC|\LPAY\Pay::TYPE_ANDROID|\LPAY\Pay::TYPE_IOS|\LPAY\Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return DirectPay::NAME;
	}
	public function match($name){
		if ($name==DirectPay::NAME) return true;
	}
	/**
	 * {@inheritDoc}

	 */
	public function pay_render(PayParam $pay_param){
		$config=$this->_config();
		$pay_url=$this->_config->get_pay_url();
		$return_url=$this->_config->get_return_url();
		$pay_param=serialize($pay_param);
		$key=md5($pay_param);
		$skey=md5($key);
		$this->_init_sess();
		$_SESSION['__LPAY_DIRECT_PAY__'][$skey]=$pay_param;
		if (count($_SESSION['__LPAY_DIRECT_PAY__'][$skey])>3)array_shift($_SESSION['__LPAY_DIRECT_PAY__'][$skey]);
		
		return new PayRender(PayRender::OUT_CREDITCARD, array('key'=>$key,'pay_param'=>$pay_param,'pay_url'=>$pay_url,'return_url'=>$return_url));
	}
	/**
	 * @param string $key
	 * @param CreditCardDetailsType $cardDetails
	 * @param PersonNameType $personName
	 * @param AddressType $address
	 * @return \LPAY\PayResult|\LPAY\Pay\PayResult
	 */
	public function direct_pay($key,CreditCardDetailsType $cardDetails,PersonNameType $personName,AddressType $address){
		
		$this->_init_sess();
		$session=isset($_SESSION['__LPAY_DIRECT_PAY__'])?$_SESSION['__LPAY_DIRECT_PAY__']:array();
		$skey=md5($key);
		if(!isset($session[$skey])||
			!($pay_param=@unserialize($session[$skey]))||
			!$pay_param instanceof PayParam){
			return PayResult::unkown($this->get_name(),'Timeout, please refresh the page.');
		}
		if ($init_session) session_write_close();
		$config=$this->_config();
		if (empty($address->Name)) $address->Name = "$personName->FirstName $personName->LastName";
		$paymentDetails = new PaymentDetailsType();
		$paymentDetails->ShipToAddress = $address;
		
		$total_fee=$pay_param->get_pay_money($this->_config->get_currency());
		$currencyCode=$this->_config->get_currency_code();//"USD"
		$notify_url=$this->_config->get_notify_url();
		$payment_type=$this->_config->get_payment_type();//sale
		/*
		 *  Total cost of the transaction to the buyer. If shipping cost and tax
		 charges are known, include them in this value. If not, this value
		 should be the current sub-total of the order.
		
		 If the transaction includes one or more one-time purchases, this field must be equal to
		 the sum of the purchases. Set this field to 0 if the transaction does
		 not include a one-time purchase such as when you set up a billing
		 agreement for a recurring payment that is not immediately charged.
		 When the field is set to 0, purchase-specific fields are ignored.
		
		 * `Currency Code` - You must set the currencyID attribute to one of the
		 3-character currency codes for any of the supported PayPal
		 currencies.
		 * `Amount`
		 */
		$paymentDetails->OrderTotal = new BasicAmountType($currencyCode, $total_fee);
		/*
		 * 		Your URL for receiving Instant Payment Notification (IPN) about this transaction. If you do not specify this value in the request, the notification URL from your Merchant Profile is used, if one exists.
		
		 */
		$paymentDetails->NotifyURL = $notify_url;
		
		
		//information about the payer
		$payer = new PayerInfoType();
		$payer->PayerName = $personName;
		$payer->Address = $address;
		if (empty($payer->PayerCountry)) $payer->PayerCountry = $address->Country;
			
		
		$cardDetails->CardOwner = $payer;
		
		
		$ddReqDetails = new DoDirectPaymentRequestDetailsType();
		$ddReqDetails->CreditCard = $cardDetails;
		$ddReqDetails->PaymentDetails = $paymentDetails;
		$ddReqDetails->PaymentAction = $payment_type;
		
		$doDirectPaymentReq = new DoDirectPaymentReq();
		$doDirectPaymentReq->DoDirectPaymentRequest = new DoDirectPaymentRequestType($ddReqDetails);
		/*
		 * 		 ## Creating service wrapper object
		 Creating service wrapper object to make API call and loading
		 Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$paypalService = new PayPalAPIInterfaceServiceService($config);
		try {
			/* wrap API method calls on the service object with a try catch */
			$doDirectPaymentResponse = $paypalService->DoDirectPayment($doDirectPaymentReq);
		} catch (\Exception $ex) {
			return PayResult::unkown($this->get_name(),$ex->getMessage());
		}
		if ($doDirectPaymentResponse->Ack!='Success'){
			$err=array_shift($doDirectPaymentResponse->Errors);
			return PayResult::fail($this->get_name(),$pay_param->get_sn(),  $doDirectPaymentResponse->TransactionID,  $err->LongMessage);
		}
		
		unset($_SESSION['__LPAY_DIRECT_PAY__'][$skey]);
		
		
		return PayResult::success($this->get_name(),$pay_param->get_sn(), $doDirectPaymentResponse->TransactionID,$doDirectPaymentResponse);
	}
	/**
	 * run pay...
	 */
	public function direct_pay_from_post(){
		$keys=array(
			'creditCardNumber',
			'creditCardType',
			'expDateMonth',
			'expDateYear',
			'cvv2Number',
			'firstName',
			'lastName',
			'firstName',
			'address1',
			'address2',
			'city',
			'state',
			'zip',
			'country',
			'phone',
			'key',
		);
		foreach ($keys as $v){
			if (!isset($_POST[$v]))PayRender::credit_card_output(false,'miss param');
		}
		$cardDetails = new CreditCardDetailsType();
		$cardDetails->CreditCardNumber =$_POST['creditCardNumber'];
		$cardDetails->CreditCardType =$_POST['creditCardType'];
		$cardDetails->ExpMonth =$_POST['expDateMonth'];
		$cardDetails->ExpYear = $_POST['expDateYear'];
		$cardDetails->CVV2 =  $_POST['cvv2Number'];
		$personName = new PersonNameType();
		$personName->FirstName =  $_POST['firstName'];
		$personName->LastName =$_POST['lastName'];
		$address = new AddressType();
		$address->Street1 =$_POST['address1'];
		$address->Street2 =$_POST['address2'];
		$address->CityName =$_POST['city'];
		$address->StateOrProvince = $_POST['state'];
		$address->PostalCode = $_POST['zip'];
		$address->Country = $_POST['country'];
		$address->Phone = $_POST['phone'];
		$key=$_POST['key'];
		$result=$this->direct_pay($key, $cardDetails, $personName, $address);
		if ($result->get_status()==PayResult::STATUS_SUCC){
			PayRender::credit_card_output(true);
		}else{
			PayRender::credit_card_output(false,$result->get_msg());
		}
	}
}
