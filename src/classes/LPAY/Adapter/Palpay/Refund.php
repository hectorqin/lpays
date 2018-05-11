<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;
use LPAY\Adapter\RefundAdapter;
use LPAY\Pay\RefundParam;
use LPAY\Pay\RefundResult;
use PayPal\PayPalAPI\RefundTransactionRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\PayPalAPI\RefundTransactionReq;
use LPAY\Exception;
use PayPal\CoreComponentTypes\BasicAmountType;
class Refund extends RefundAdapter{
	/**
	 * @var Config
	 */
	protected $_config;
	public function __construct(Config $config){
		$this->_config=$config;
	}
	/**
	 * @return Config
	 */
	public function get_config(){
		return $this->_config;
	}
	/**
	 * @return string
	 */
	public function support_name(){
		return array(Pay::NAME,DirectPay::NAME);
	}
	public function enable(){
		return true;
	}
	protected function _config(){
		$mode=$this->_config->get_mode();//"sandbox",'live'
		$username=$this->_config->get_username();//"362724880-facilitator_api1.qq.com"
		$password=$this->_config->get_password();//"RDYZD6FAEK28865K"
		$sign=$this->_config->get_signature();//"AFcWxV21C7fd0v3bYYYRCpSSRl31AFOI1naadHvh1c1vzVqRCY9c2mFZ"
		$config = array(
				"mode" => $mode,
				'log.LogEnabled' => false,
				'log.FileName' => sys_get_temp_dir().'/PayPal.log',
				'log.LogLevel' => 'FINE',
				"acct1.UserName" => $username,
				"acct1.Password" => $password,
				"acct1.Signature" => $sign,
		);
		return $config;
	}
	/**
	 * refund money
	 * @param RefundParam $refund_param
	 * @return RefundResult
	 */
	public function refund(RefundParam $refund_param){
		$msg=$refund_param->get_refund_msg();
		$recharge_pay_no= $refund_param->get_pay_no();
		$refund_money = $refund_param->get_refund_pay_money($this->_config->get_currency_code());
		$total_money = $refund_param->get_total_pay_money($this->_config->get_currency_code());
		$return_no = $refund_param->get_return_no();
		$config=$this->_config();
		/*
		 * The RefundTransaction API operation issues a refund to the PayPal account holder associated with a transaction.
		 This sample code uses Merchant PHP SDK to make API call
		 */
		$refundReqest = new RefundTransactionRequestType();
		if ($refund_money>=$total_money){
			$refundReqest->RefundType ='Full';
		}else{
			$refundReqest->RefundType ='Partial';
			$refundReqest->Amount = new BasicAmountType($currencyCode, $refund_money);
		}
		/*
		 *  Either the `transaction ID` or the `payer ID` must be specified.
		 PayerID is unique encrypted merchant identification number
		 For setting `payerId`,
		 `refundTransactionRequest.setPayerID("A9BVYX8XCR9ZQ");`
		
		 Unique identifier of the transaction to be refunded.
		 */
		$refundReqest->TransactionID = $recharge_pay_no;
		/*
		 *  (Optional)Type of PayPal funding source (balance or eCheck) that can be used for auto refund. It is one of the following values:
		
		 any � The merchant does not have a preference. Use any available funding source.
		
		 default � Use the merchant's preferred funding source, as configured in the merchant's profile.
		
		 instant � Use the merchant's balance as the funding source.
		
		 eCheck � The merchant prefers using the eCheck funding source. If the merchant's PayPal balance can cover the refund amount, use the PayPal balance.
		
		 */
		$refundReqest->RefundSource = 'any';
		$refundReqest->Memo = $msg;
		$refundReqest->InvoiceID=$return_no;
		/*
		 *
		 (Optional) Maximum time until you must retry the refund.
		 */
		//$refundReqest->RetryUntil = $_REQUEST['retryUntil'];
		
		$refundReq = new RefundTransactionReq();
		$refundReq->RefundTransactionRequest = $refundReqest;
		/*
		 * 	 ## Creating service wrapper object
		 Creating service wrapper object to make API call and loading
		 Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$paypalService = new PayPalAPIInterfaceServiceService(\Configuration::getAcctAndConfig());
		try {
			/* wrap API method calls on the service object with a try catch */
			$refundResponse = $paypalService->RefundTransaction($refundReq);
		} catch (\Exception $ex) {
			return RefundResult::unkown($this->get_name(),$ex->getMessage());
		}
		
		if ($refundResponse->Ack!='Success'){
			$err=array_shift($refundResponse->Errors);
			return RefundResult::fail($this->get_name(),$refund_no, $err->LongMessage);
		}
		return RefundResult::ing($this->get_name(),$return_no,$refundResponse,$refundResponse->CorrelationID);
	}
}