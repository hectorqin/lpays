<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\AliOpen;
use LPAY\Bill\Download;
use LPAY\Exception;
use LPAY\Utils;
class Bill extends \LPAY\Bill\Download implements \LPAY\Bill{
	/**
	 * @var Config
	 */
	protected $_config;
	protected $_data_file;
	protected $_date;
	public function __construct(Config $config){
		$this->_config=$config;
	}
	
	public function set_date($date){
		$time=strtotime($date);
		if ($time<=0) return false;
		$this->_date=date("Y-m-d",strtotime($date));
		return $this;
	}
	public function get_data_file(){
		if ($this->_data_file==null) $this->_data_file= new BillFile();
		return $this->_data_file;
	}
	public function exec(){
		//call api get url
		$config=$this->_config->as_array();
		require_once Utils::lib_path("alipay_aop/AopSdk.php");
		$aop = new \AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = $config['app_id'];
		$aop->rsaPrivateKeyFilePath = $config['private_key_path'];
		$aop->alipayPublicKey= $config['ali_public_key_path'];
		$aop->apiVersion = '1.0';
		$aop->postCharset='utf-8';
		$aop->format='json';
		$request = new \AlipayDataDataserviceBillDownloadurlQueryRequest ();
		$request->setBizContent(json_encode(array(
				"bill_type"=>"trade",
				"bill_date"=>$this->_date
		)));
		$result = $aop->execute ($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(empty($resultCode)||$resultCode != 10000){
			throw new Exception($result->$responseNode->msg."[{$result->$responseNode->sub_code}]");
		}
		$url=$result->$responseNode->bill_download_url;
		$downloader=$this->get_downloader();
		$status=$downloader->download(get_called_class(), $url,$this->_tag);
		if ($downloader->is_realtime())
			$status=$status&&$this->get_data_file()->load_file($downloader->file_path(get_called_class(), $this->_tag));
		return $status;
	}
}