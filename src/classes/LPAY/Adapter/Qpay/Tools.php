<?php
namespace LPAY\Adapter\Qpay;
use LPAY\Exception;

class Tools{
	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return string
	 */
	private function _get_nonce_str($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
	/**
	 * 格式化参数格式化成url参数
	 */
	private function _arr_to_query($arr)
	{
		$buff = "";
		foreach ($arr as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		$buff = trim($buff, "&");
		return $buff;
	}
	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	private function _make_sign($arr,$key)
	{
		//签名步骤一：按字典序排序参数
		ksort($arr);
		$string = $this->_arr_to_query($arr);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".$key;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
		
	}
	public static function get_to_xml(array $arr,Config $config){
		$arr['appid']=$config->get("appid");
		$arr['mch_id']=$config->get("mchid");
		$arr['nonce_str']=$this->_get_nonce_str();
		$arr['sign']=$this->_make_sign($arr, $config->get("key"));
		return self::to_xml($arr);
	}
	public static function to_xml(array $arr){
		$xml = "<xml>";
		foreach ($arr as $key=>$val)
		{
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}
	
	

	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws Exception
	 */
	public static function post($url, $xml,Config $config,$useCert=false)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		//如果有配置代理这里就设置代理
		$pip=$config->get("proxy_ip");
		$ppp=$config->get("proxy_port");
		if( $pip!= "0.0.0.0" && $ppp != 0){
			curl_setopt($ch,CURLOPT_PROXY, $pip);
			curl_setopt($ch,CURLOPT_PROXYPORT, $ppp);
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if($useCert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $config->get("sslcert_path"));
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $config->get("sslkey_path"));
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			throw new Exception("qqpay error:{$error}");
		}
	}
	public static function parse($result,Config $config){
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$values = json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if (!isset($values['return_code'])||$values['return_code']!='SUCCESS'){
			throw new Exception("qpay request fail:{$values['retmsg']}");
		}
		if (!isset($values['sign'])||$values['sign']!=$this->_make_sign($values, $config->get("key"))){
			throw new Exception("qpay data parse fail:{$result}");
		}
		return $values;
	}
}