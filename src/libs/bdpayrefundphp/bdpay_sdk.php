<?php

/***************************************************************************
 * 
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/

/**
 * @file sdk.php
 *
 * @author wuxiaofang(com@baidu.com)
 *         @date 2014/08/14 16:39:58
 *         @brief
 *        
 */
if (!defined("bdpay_sdk_ROOT"))
{
	define("bdpay_sdk_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
 
require_once(bdpay_sdk_ROOT . 'bdpay_refund.cfg.php');

if (!function_exists('curl_init')) {
	exit('您的PHP没有安装 配置cURL扩展，请先安装配置cURL，具体方法可以上网查。');
}

if (!function_exists('json_decode')) {
	exit('您的PHP不支持JSON，请升级您的PHP版本。');
}


class bdpay_sdk{
	public $err_msg;
	public $order_no;

	function __construct() {
	}

	/**
	 * 生成百度钱包退款接口对应的URL
	 *
	 * @param array $params	生成退款请求的参数数组，具体参数的取值参见接口文档
	 * @param string $url   百度钱包退款接口URL
	 * @return string 返回生成的百度钱包退款接口URL
	 */
	function create_baifubao_Refund_url($params, $url) {
		if (empty($params ['service_code']) || empty($params ['input_charset']) ||
				 empty($params ['sign_method']) ||
				 empty($params ['output_type']) ||
				 empty($params ['output_charset']) ||
				 empty($params ['return_url']) ||
				 empty($params ['return_method']) ||
				 empty($params ['version']) ||
				 empty($params ['sp_no']) ||
			     empty($params ['order_no']) ||
				 empty($params ['cashback_amount']) ||
				 empty($params ['cashback_time']) ||
				 empty($params ['currency'])||
                   empty($params ['sp_refund_no'])
			 ) {
			$this->log(sprintf('invalid params, params:[%s]', print_r($params, true)));
			print_r("结束2yu2create_baifubao_Refund_url");
			return false;
		}
		//print_r("结束3"+$url);
		if (!in_array($url, 
				array (
					sp_conf::BFB_REFUND_URL,
					sp_conf::BFB_REFUND_QUERY_URL,
				))) {
			$this->log(
					sprintf('invalid url[%s], bfb just provide three kind of pay url', 
					$url));
			return false;
		}
		
		$refund_url = $url;
		
		if (false === ($sign = $this->make_sign($params))) {
			return false;
		}
		$this->order_no = $params ['order_no'];
		$params ['sign'] = $sign;
		$params_str = http_build_query($params);
		$this->log(
				sprintf('the params that create baifubao pay order is [%s]', 
						$params_str));
		
		return $refund_url . '?' . $params_str;
	}

	/**
	 * 当收到百度钱包的退款结果通知后，return_url页面需要做的预处理工作
	 * 该方法放在商户配置的return_url的页面的处理逻辑里，当收到该页面的get请求时，
	 * 预先进行参数验证，签名校验，订单查询，然后才是商户对该订单的处理流程。
	 *
	 * @return boolean 预处理成功返回true，否则返回false
	 */
	function check_bfb_refund_result_notify() {
		// 检查请求的必选参数，具体的参数参见接口文档
		if (empty($_GET) || !isset($_GET ['bfb_order_no']) || !isset(
				$_GET ['cashback_amount']) || !isset($_GET ['order_no']) ||
				 !isset($_GET ['ret_code']) ||
				 !isset($_GET ['sign_method']) || !isset($_GET ['sp_no']) ||
				 !isset($_GET ['sp_refund_no']) || !isset($_GET ['sign'])) {
			$this->err_msg = 'return_url页面的请求的必选参数不足';
			$this->log(
					sprintf('missing the params of return_url page, params[%s]', 
							print_r($_GET,true)));
			return false;
		}
		$arr_params = $_GET;
		$this->order_no = $arr_params ['order_no'];
		// 检查商户ID是否是自己，如果传过来的sp_no不是商户自己的，那么说明这个百度钱包的支付结果通知无效
		if (sp_conf::SP_NO() != $arr_params ['sp_no']) {
			$this->err_msg = '百度钱包退款通知中商户ID无效，该通知无效';
			$this->log(
					'the id in baifubao notify is wrong, this notify is invaild');
			return false;
		}
		// 签名校验
		if (false === $this->check_sign($arr_params)) {
			print_r('签名失败');
			$this->err_msg = '百度钱包后台通知签名校验失败';
			$this->log('baifubao notify sign failed');
			return false;
		}
		$this->log('baifubao notify sign success');
		
		// 通过百度钱包退款查询接口再次查询订单状态，二次校验
		// 该查询接口存在一定的延迟，商户也可以不用二次校验，信任后台的支付结果通知便行
		
		// 调用query_order_state($order_no)方法查询订单在商户自己系统的状态
		//如果返回的ret_code状态是1、2和3（1退款成功,2退款失败，3退款到余额，订单状态已经修改），则代表是重复通知
	    //query_order_state($order_no);

		return true;
	}
	
	/**
	 * 退款通知结果的回执
	 * 作用：	收到通知，并验证通过，向百度钱包发起回执。百度钱包GET请求商户的return_url页面，商户这边的响应
	 * 		中必须包含以下部分，百度钱包只有接收到特定的响应信息后，才能确认商户已经收到通知，并验证通过。这样
	 * 		百度钱包才不会再向商户发送支付结果通知
	 */
	function notify_bfb() {
		$rep_str = "<html><head>" . sp_conf::BFB_NOTIFY_META .
				 "</head><body><h1>这是一个return_url页面</h1></body></html>";
		echo "$rep_str";
	}

	/**
	 * 通过百度钱包按订单号查询退款信息，返回该订单是否已经退款成功
	 *
	 * @param string $order_no        	
	 * @return string | boolean 请求成功返回订单查询结果，其它情况
	 （包括查询失败以及签名错误等情况）返回false
	 */
	function query_baifubao_refund_result_by_order_no($order_no) {
		$params = array (
			'service_code' => sp_conf::BFB_QUERY_INTERFACE_SERVICE_ID, // 查询接口的服务ID号
			'sp_no' => sp_conf::SP_NO(),
			'order_no' => $order_no,
			'output_type' => sp_conf::BFB_INTERFACE_OUTPUT_FORMAT, // 百度钱包返回XML格式的结果
			'output_charset' => sp_conf::BFB_INTERFACE_ENCODING, // 百度钱包返回GBK编码的结果
			'version' => sp_conf::BFB_INTERFACE_VERSION,
			'sign_method' => sp_conf::SIGN_METHOD_MD5
		);
	
		// 百度钱包订单号退款查询接口参数，具体的参数取值参见接口文档
		
		if (false === ($sign = $this->make_sign($params))) {
			$this->log(
					'make sign for query baifubao refund result interface failed');
			return false;
		}
		$params ['sign'] = $sign;
		$params_str = http_build_query($params);
		
		$query_url = sp_conf::BFB_REFUND_QUERY_URL . '?' . $params_str;
		$this->log(
				sprintf('the url of query baifubao refund result is [%s]', 
						$query_url));
		$content = $this->request($query_url);
		$retry = 0;
		while (empty($content) && $retry < sp_conf::BFB_QUERY_RETRY_TIME) {
			$content = $this->request($query_url);
			$retry++;
		}
		if (empty($content)) {
			$this->err_msg = '调用百度钱包退款查询接口失败';
			return false;
		}
		$this->log(
				sprintf('the result from baifubao query pay result is [%s]', 
						$content));
		$response_arr = json_decode(
				json_encode(simplexml_load_string($content)), true);
		// 上句解析xml文件时，如果某字段没有取值时，会被解析成一个空的数组，对于没有取值的情况，都默认设为空字符串
		foreach ($response_arr as &$value) {
			if (empty($value) && is_array($value)) {
				$value = '';
			}
		}
		unset($value);
		
		return print_r($response_arr, true);
	}
	/**
	 * 通过百度钱包按退款流水号号查询退款信息，返回该订单是否已经退款成功
	 *
	 * @param string $order_no        	
	 * @return string | boolean 请求成功返回订单查询结果，其它情况
	 （包括查询失败以及签名错误等情况）返回false
	 */
	function query_baifubao_refund_result_by_sprefund_no($order_no,$sp_refund_no) {
		$params = array (
			'service_code' => sp_conf::BFB_QUERY_INTERFACE_SERVICE_ID, // 查询接口的服务ID号
			'sp_no' => sp_conf::SP_NO(),
			'order_no' => $order_no,
			'sp_refund_no' => $sp_refund_no,
			'output_type' => sp_conf::BFB_INTERFACE_OUTPUT_FORMAT, // 百度钱包返回XML格式的结果
			'output_charset' => sp_conf::BFB_INTERFACE_ENCODING, // 百度钱包返回GBK编码的结果
			'version' => sp_conf::BFB_INTERFACE_VERSION,
			'sign_method' => sp_conf::SIGN_METHOD_MD5
		);
	
		// 百度钱包订单号退款查询接口参数，具体的参数取值参见接口文档
		
		if (false === ($sign = $this->make_sign($params))) {
			$this->log(
					'make sign for query baifubao refund result interface failed');
			return false;
		}
		$params ['sign'] = $sign;
		$params_str = http_build_query($params);
		
		$query_url = sp_conf::BFB_REFUND_QUERY_URL . '?' . $params_str;
		$this->log(
				sprintf('the url of query baifubao refund result is [%s]', 
						$query_url));
		$content = $this->request($query_url);
		$retry = 0;
		while (empty($content) && $retry < sp_conf::BFB_QUERY_RETRY_TIME) {
			$content = $this->request($query_url);
			$retry++;
		}
		if (empty($content)) {
			$this->err_msg = '调用百度钱包退款查询接口失败';
			return false;
		}
		$this->log(
				sprintf('the result from baifubao query pay result is [%s]', 
						$content));
		$response_arr = json_decode(
				json_encode(simplexml_load_string($content)), true);
		// 上句解析xml文件时，如果某字段没有取值时，会被解析成一个空的数组，对于没有取值的情况，都默认设为空字符串
		foreach ($response_arr as &$value) {
			if (empty($value) && is_array($value)) {
				$value = '';
			}
		}
		unset($value);
		
        // 将可能出现中文的字段按照查询接口中定义的编码方式进行转码，此处测试是用的GBK编码
		if (isset($response_arr ['ret_details'])) {
			$response_arr ['ret_details'] = iconv("UTF-8", "GBK", 
					$response_arr ['ret_details']);
		}

		return print_r($response_arr, true);
	}

	/**
	 * 计算数组的签名，传入参数为数组，算法如下：
	 * 1.
	 * 对数组按KEY进行升序排序
	 * 2. 在排序后的数组中添加商户密钥，键名为key，键值为商户密钥
	 * 3. 将数组拼接成字符串，以key=value&key=value的形式进行拼接，注意这里不能直接调用
	 * http_build_query方法，因为该方法会对参数进行URL编码
	 * 4. 要所传入数组中的$params ['sign_method']定义的加密算法，对拼接好的字符串进行加密，生成的便是签名。
	 * $params ['sign_method']等于1使用md5加密，等于2使用sha-1加密
	 *
	 * @param array $params 生成签名的数组
	 * @return string | boolean 成功返回生成签名，失败返回false
	 */
	private function make_sign($params) {
		if (is_array($params)) {
			// 对参数数组进行按key升序排列
			if (ksort($params)) {
				if(false === ($params ['key'] = $this->get_sp_key())){
					return false;
				}
				$arr_temp = array ();
				foreach ($params as $key => $val) {
					$arr_temp [] = $key . '=' . $val;
				}
				$sign_str = implode('&', $arr_temp);
				// 选择相应的加密算法
				if ($params ['sign_method'] == sp_conf::SIGN_METHOD_MD5) {
					return md5($sign_str);
				} else if ($params ['sign_method'] == sp_conf::SIGN_METHOD_SHA1) {
					return sha1($sign_str);
				} else{
					$this->log('unsupported sign method');
					$this->err_msg = '签名方法不支持';
					return false;
				}
			} else {
				$this->log('ksort failed');
				$this->err_msg = '表单参数数组排序失败';
				return false;
			}
		} else {
			$this->log('the params of making sign should be a array');
			$this->err_msg = '生成签名的参数必须是一个数组';
			return false;
		}
	}

	/**
	 * 校验签名，传入的参数必须是一个数组，算法如下：
	 * 1. 删除数组中的签名sign元素
	 * 2. 对数组中的所有键值进行url反编码，避免传入的参数是经过url编码的
	 * 3. 利用商户密钥对新数组进行加密，生成签名
	 * 4. 比对生成签名和数组中原有的签名
	 *
	 * @param array $params	生成签名的参数数组
	 * @return boolean	生成签名成功返回true, 失败返回false
	 */
	private function check_sign($params) {
		$sign = $params ['sign'];
		unset($params ['sign']);
		foreach ($params as &$value) {
			$value = urldecode($value); // URL编码的解码
		}
		unset($value);
		if (false !== ($my_sign = $this->make_sign($params))) {
			print_r('商户自己拼接的签名：'.$my_sign);
			print_r('百度钱包返回的签名：'.$sign);
			if (0 !== strcmp($my_sign, $sign)) {
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 读取密钥文件，返回商户的百度钱包密钥
	 * 考虑到安全性，密钥需要放在外网访问不到的目录里。
	 *
	 * @return string	返回商户的百度钱包密钥
	 */
	private function get_sp_key() {
		$file = sp_conf::SP_KEY_FILE();
		if (!file_exists($file)) {
			$this->log(sprintf('can not find the sp key file, file [%s]', $file));
			return false;
		}
		$fh = fopen($file, 'rb');
		$key = trim(fread($fh, filesize($file)));
		fclose($fh);
		return $key;
	}

	/**
	 * 执行一个 HTTP GET请求
	 *
	 * @param string $url 执行请求的url
	 * @return array 返回网页内容
	 */
	function request($url) {
		$curl = curl_init(); // 初始化curl
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false); // 设置header
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 要求结果为字符串且输出到屏幕上
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); // 设置等待时间
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		
		$res = curl_exec($curl); // 运行curl
		$err = curl_error($curl);
		
		if (false === $res || !empty($err)) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			
			$this->log(
					sprintf(
							'curl the baifubao pay result interface failed, err_msg [%s]', 
							$info));
			$this->err_msg = $info;
			return false;
		}
		curl_close($curl); // 关闭curl
		return $res;
	}

	/**
	 * 日志打印函数
	 * 如果在bdpay_refund.cfg.php配置文件中定义了日志输出文件，那么日志信息就打到到该文件；
	 * 如果没有定义，那日志信息输出到PHP自带的日志文件
	 * 
	 * @param string $msg	日志信息    	
	 */
	function log($msg) {
		return ;
		if(defined(sp_conf::LOG_FILE)) {
			error_log(
					sprintf("[%s] [order_no: %s] : %s\n", date("Y-m-d H:i:s"), 
							$this->order_no, $msg));
		}
		else {
			error_log(
					sprintf("[%s] [order_no: %s] : %s\n", date("Y-m-d H:i:s"), 
							$this->order_no, $msg), 3, sp_conf::LOG_FILE);
		}
	}
}

?>