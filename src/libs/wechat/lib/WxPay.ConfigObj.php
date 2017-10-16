<?php
/**
* 	配置账号信息
*/

class WxPayConfigObj
{
	protected $_param;
	public function __construct($appid,$mchid,$key,$appsecret,$ssl_cert_path,$ssl_key_path,$curl_proxy_host,$curl_proxy_port,$report_level,$ca_path=null){
		$this->_param=array(
			'APPID'=>$appid,	
			'MCHID'=>$mchid,	
			'KEY'=>$key,	
			'APPSECRET'=>$appsecret,	
			'SSLCERT_PATH'=>$ssl_cert_path,	
			'SSLKEY_PATH'=>$ssl_key_path,	
			'CURL_PROXY_HOST'=>$curl_proxy_host,	
			'CURL_PROXY_PORT'=>$curl_proxy_port,	
			'REPORT_LEVENL'=>$report_level,	
			'SSLCERT_CA'=>$ca_path,	
		);
	}
	public function __get($key){
		if (isset($this->_param[$key])) return $this->_param[$key];
		else null;
	}
}
