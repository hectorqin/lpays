<?php
namespace com\jdjr\pay\demo\common;
class RSAUtils{
	public static function encryptByPrivateKey($data,$key='../config/seller_rsa_private_key.pem') {
		$pi_key =  openssl_pkey_get_private(file_get_contents($key));//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
		$encrypted="";
		openssl_private_encrypt($data,$encrypted,$pi_key,OPENSSL_PKCS1_PADDING);//私钥加密
		$encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
		return $encrypted;
	}
	
	public static function decryptByPublicKey($data,$key='../config/wy_rsa_public_key.pem') {
		$pu_key =  openssl_pkey_get_public(file_get_contents($key));//这个函数可用来判断公钥是否是可用的，可用返回资源id Resource id
		$decrypted = "";
		$data = base64_decode($data);
		openssl_public_decrypt($data,$decrypted,$pu_key);//公钥解密
		return $decrypted;
	}
}
?>