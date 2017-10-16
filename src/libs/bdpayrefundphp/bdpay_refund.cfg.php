<?php

final class sp_conf{
	
	
	public static $config=array(
			'sp_no'=>'9000100005',
			'key_file'=>'sp.key',
	);
	
	public static function SP_NO(){
		return self::$config['sp_no'];
	}
	public static function SP_KEY_FILE(){
		return self::$config['key_file'];
	}
	
	
	
	// 商户在百度钱包的商户ID
	const SP_NO = '9000100005';
	// 密钥文件路径，该文件中保存了商户的百度钱包合作密钥，该文件需要放在一个安全的地方，切勿让外人知晓或者外网访问
	const SP_KEY_FILE = 'sp.key';
	// 日志文件
	const LOG_FILE = 'sdk.log';
	

	// 百度钱包退款接口URL
	const BFB_REFUND_URL = "https://www.baifubao.com/api/0/refund";
	// 百度钱包退款查询接口URL
	const BFB_REFUND_QUERY_URL = "https://www.baifubao.com/api/0/refund/0/query";
	// 百度钱包查询重试次数
	const BFB_QUERY_RETRY_TIME = 3;
	// 百度钱包退款结果通知成功后的回执
	const BFB_NOTIFY_META = "<meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\">";
	
	// 签名校验算法
	const SIGN_METHOD_MD5 = 1;
	const SIGN_METHOD_SHA1 = 2;
	// 百度钱包退款接口服务ID
	const BFB_REFUND_INTERFACE_SERVICE_ID = 2;
	// 百度钱包退款查询接口服务ID
	const BFB_QUERY_INTERFACE_SERVICE_ID = 12;
	// 百度钱包接口版本
	const BFB_INTERFACE_VERSION = 2;
	// 百度钱包接口字符编码
	const BFB_INTERFACE_ENCODING = 1;
	// 百度钱包接口返回格式：XML
	const BFB_INTERFACE_OUTPUT_FORMAT = 1;
	// 百度钱包接口货币单位：人民币
	const BFB_INTERFACE_CURRENTCY = 1;
}

?>