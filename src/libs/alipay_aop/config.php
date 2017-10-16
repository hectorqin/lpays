<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016080100142895",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIICXAIBAAKBgQCfOhDu9DsCqe9YVHmXpsg9zt0pA33Rhy0BK7WjObuJ8XP6U2XKWWzSQ7enwpu429KFtyCshblvuk4N68ujeqAqCM7VQmbghs4IWahiNabNTTXCMSe4OJmoSF0RS+o3WU6Gr24YzIyk2D8BnQpKP6R8XwKnGOFSZ6FkZp42ZmLawIDAQABAoGAH9KRE9DIAm5IIZUwf/ibSI8RcGL8QGYNvpAnyyl8q8MP9NW6IFzd+Ij/acrLYQIfeeU7yPsizGPtZIjsgBdo6GD8qzcDF8HOTJRY47GoHlYSz/OIdiZ1vNbJ9y6ZBttaiZmtIhY8EZAfRFKDnYQbQCn2zf9rt5+esCfxatmVcmECQQDMpvrTzkN+nqTVJPheulDNRD1pbSeOdc2O7dZNkU9iVZ7G6xEpSLEbzeweP0LLynheiZQ/UA2Y6nGLB/7hK/s5AkEAxy1dUgFRvd3I1qUu12ZdwHTDPxgbhVJN2g2BQ6T3ZYjEVB8KItiVcDWW445gSoDoBfhJmefSoFuVPqKu6u+nwwJAYdmI3lKl1Nm3iC3YDzrYPXzePBUzr5rFwQwYxhevNB4p/4QPPYUDIX8w4TlwD45sRQ9U8XyuM6oMxeP5yuHDiQJACnLYyGqFTT6LQKdds7MNDAGUFIVBPFc6+ktnEpNe3xazpe4S7A0MmdxV1A4uAvqMMXP6+HXu2La1N5n1LyHvXQJBAKSrKAYEymc58JbSHb/Z269dKhLCy3zcKrxaP7CL/N0UJUHfmt4s1Wpjlyco7NfMy/jBsJfTnIXQmtb3WFKgOLU=",
		
		//异步通知地址
		'notify_url' => "http://工程公网访问地址/alipay.trade.wap.pay-PHP-UTF-8/notify_url.php",
		
		//同步跳转
		'return_url' => "http://工程公网访问地址/alipay.trade.wap.pay-PHP-UTF-8/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥
		'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCsiiDGPsWHHncvtyoI4IPRKq1JSZLQmH1RLBw3 D8JLdjLpruw0T10VGYCPNgu1jMjruejEQKk/s2nZQuGeTZkljwdKWgewAhSFeHr5Padmng6W9Y81 sz4eBuHjzWl1m9MkbOQKWZh7RmAQl4Y6qePpj0dyR0N7r4nTwViBlsVWoQIDAQAB",
		
	
);