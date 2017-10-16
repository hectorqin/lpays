<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;

use LPAY\Utils;

class Config {
	/**
	 * @return static
	 */
	public static function WxPayConfig_to_arr(){
		if (!class_exists('\WxPayConfig')) require_once Utils::lib_path("wechat/lib/WxPay.Config.php");
		$r = new \ReflectionClass('\WxPayConfig');
		return $r->getConstants();
	}
	protected static $cls;
	/**
	 * @param array $config
	 * @return static
	 */
	public static function arr(array $config){
		$cls=get_called_class();
		if (!isset(self::$cls[$cls])){
			$clsobj= new static($config['APPID'], $config['MCHID'], $config['KEY'], $config['APPSECRET']);
			isset($config['REPORT_LEVENL'])&&$clsobj->set_reprot($config['REPORT_LEVENL']);
			isset($config['CURL_PROXY_HOST'])&&isset($config['CURL_PROXY_PORT'])&&$clsobj->set_proxy($config['CURL_PROXY_HOST'],$config['CURL_PROXY_PORT']);
			if (!isset($config['SSL_DIR']))$config['SSL_DIR']='';
			else $config['SSL_DIR']=rtrim($config['SSL_DIR'],'\\/').'/';
			$clsobj->set_ssl($config['SSL_DIR'].$config['SSLCERT_PATH'],$config['SSL_DIR'].$config['SSLKEY_PATH']);
			$clsobj->set_ca($config['SSL_DIR'].$config['SSLCERT_CA']);
			self::$cls[$cls]=$clsobj;
		}
		return self::$cls[$cls];
	}
	protected $_appid;
	protected $_mchid;
	protected $_key;
	protected $_appsecret;
	protected $_sslcert_path;
	protected $_sslkey_path;
	protected $_ca_path;
	protected $_proxy_ip='0.0.0.0';
	protected $_proxy_port='0';
	protected $_report_level=1;
	protected $_wxpayconfig;
	public function __construct($appid,$mchid,$key,$appsecret){
		$this->_appid=$appid;
		$this->_mchid=$mchid;
		$this->_key=$key;
		$this->_appsecret=$appsecret;
	}
	public function set_ssl($sert,$key){
		$this->_sslcert_path=$sert;
		$this->_sslkey_path=$key;
		return $this;
	}
	public function set_ca($ca){
		$this->_ca_path=$ca;
		return $this;
	}
	public function set_proxy($ip,$port){
		$this->_proxy_ip=$ip;
		$this->_proxy_port=$port;
		return $this;
	}
	public function set_reprot($level=1){
		$this->_report_level=$level;
		return $this;
	}
	/**
	 * @return \WxPayConfigObj
	 */
	public function get_WxPayConfigObj(){
		if ($this->_wxpayconfig==null){
			if (!class_exists('WxPayConfigObj')) require_once Utils::lib_path("wechat/lib/WxPay.ConfigObj.php");
			$this->_wxpayconfig=new \WxPayConfigObj(
				$this->_appid, 
				$this->_mchid, 
				$this->_key, 
				$this->_appsecret, 
				$this->_sslcert_path, 
				$this->_sslkey_path,
				$this->_proxy_ip, 
				$this->_proxy_port, 
				$this->_report_level,
				$this->_ca_path
			);
		}
		return $this->_wxpayconfig;
	}
	
}