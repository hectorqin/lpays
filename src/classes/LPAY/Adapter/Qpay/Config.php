<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Qpay;
class Config {
	protected $appid;
	protected $mchid;
	protected $key;
	protected $pubAcc;
	protected $pubAccHint;
	protected $sslcert_path;
	protected $sslkey_path;
	protected $proxy_ip='0.0.0.0';
	protected $proxy_port='0';
	public function __construct($appid,$mchid,$key,$pubAcc='',$pubAccHint=''){
		$this->appid=$appid;
		$this->mchid=$mchid;
		$this->key=$key;
		$this->pubAcc=$pubAcc;
		$this->pubAccHint=$pubAccHint;
	}
	public function set_ssl($sert,$key){
		$this->sslcert_path=$sert;
		$this->sslkey_path=$key;
		return $this;
	}
	public function set_proxy($ip,$port){
		$this->proxy_ip=$ip;
		$this->proxy_port=$port;
		return $this;
	}
	public function get($key,$default=''){
		if (!isset($this->{$key}))return $default;
		return $this->{$key};
	}
}