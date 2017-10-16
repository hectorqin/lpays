<?php

namespace com\jdjr\pay\demo\common;

class ConfigUtil {
	protected static $_config=array(
		'serverPayUrl' => 'https://wepay.jd.com/jdpay/saveOrder',
		'serverQueryUrl' => 'http://paygate.jd.com/service/query',
		'refundUrl' => 'http://paygate.jd.com/service/refund',
	);
	public static $config=array(
		'merchantNum' => '22294531',
		'desKey' => 'ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t',
		'callbackUrl' => 'http://10.13.83.2/jdPay2Demo/com/jdjr/pay/demo/action/CallBack.php',
		'notifyUrl' => 'http://localhost/jdPay2Demo/com/jdjr/pay/demo/action/AsnyNotify.php',
	);
	public static function get_val_by_key($key) {
		$config=self::$config+self::$_config;
		return isset($config[$key])?$config[$key]:null;
	}
	public static function get_trade_num() {
		return ConfigUtil::get_val_by_key ( 'merchantNum' ) . ConfigUtil::getMillisecond ();
	}
	public static function getMillisecond() {
		list ( $s1, $s2 ) = explode ( ' ', microtime () );
		return ( float ) sprintf ( '%.0f', (floatval ( $s1 ) + floatval ( $s2 )) * 1000 );
	}
}