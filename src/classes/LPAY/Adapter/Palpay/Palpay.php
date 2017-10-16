<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Palpay;
use LPAY\Adapter\PayAdapter;
use LPAY\Pay\PayResult;
use PayPal\PayPalAPI\GetTransactionDetailsRequestType;
use LPAY\Pay\QueryParam;
use PayPal\PayPalAPI\GetTransactionDetailsReq;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use LPAY\Pay\Query;
abstract class Palpay extends PayAdapter implements Query{
	protected $_config;
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
	public function query(QueryParam $param){
		$config=$this->_config();
		$tid=$param->get_pay_no();
		$sn=$param->get_pay_sn();
		if (empty($tid)) return PayResult::unkown($this->get_name(),'this order unkown pay status');
		/*
		 * The GetTransactionDetails API operation obtains information about a specific transaction.
		 */
		
		$transactionDetails = new GetTransactionDetailsRequestType();
		/*
		 * Unique identifier of a transaction.
		 */
		$transactionDetails->TransactionID = $tid;
		$request = new GetTransactionDetailsReq();
		$request->GetTransactionDetailsRequest = $transactionDetails;
		/*
		 * 	 ## Creating service wrapper object
		 Creating service wrapper object to make API call and loading
		 Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$paypalService = new PayPalAPIInterfaceServiceService($config);
		try {
			/* wrap API method calls on the service object with a try catch */
			$transDetailsResponse = $paypalService->GetTransactionDetails($request);
		} catch (\Exception $ex) {
			return PayResult::unkown($this->get_name(),$ex->getMessage());
		}
		if ($transDetailsResponse->Ack!='Success'){
			return PayResult::unkown($this->get_name(),$refund_no, $transDetailsResponse->Errors->LongMessage);
		}
		return PayResult::success($this->get_name(),$sn, $tid,$transDetailsResponse);
	}
}
