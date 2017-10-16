<?php
namespace com\unionpay\acp\sdk;

include_once dirname(__FILE__).'/acp_service.php';

class Cert
{
    public $cert;
    public $certId;
    public $key;
}


// 内存泄漏问题说明：
//     openssl_x509_parse疑似有内存泄漏，暂不清楚原因，可能和php、openssl版本有关，估计有bug。
//     windows下试过php5.4+openssl0.9.8，php7.0+openssl1.0.2都有这问题。mac下试过也有问题。
//     不过至今没人来反馈过这个问题，所以不一定真有泄漏？或者因为增长量不大所以一般都不会遇到问题？
//     也有别人汇报过bug：https://bugs.php.net/bug.php?id=71519
//
// 替代解决方案：
//     方案1. 所有调用openssl_x509_parse的地方都是为了获取证书序列号，可以尝试把证书序列号+证书/key以别的方式保存，
//            从其他地方（比如数据库）读序列号，而不直接从证书文件里读序列号。
//     方案2. 代码改成执行脚本的方式执行，这样执行完一次保证能释放掉所有内存。
//     方案3. 改用下面的CertSerialUtil取序列号，
//            此方法仅用了几个测试和生产的证书做过测试，不保证没bug，所以默认注释掉了。如发现有bug或者可优化的地方可自行修改代码。
//            注意用了bcmath的方法，*nix下编译时需要 --enable-bcmath。http://php.net/manual/zh/bc.installation.php


class CertUtil{

    private static $signCerts = array();
    private static $encryptCerts = array();
    private static $verifyCerts = array();

    private static function initSignCert($certPath, $certPwd){
        $logger = LogUtil::getLogger();
        $logger->LogInfo("读取签名证书……");
        $cert = new Cert();

        $pkcs12certdata = file_get_contents ( $certPath );
        if($pkcs12certdata === false ){
        	$logger->LogInfo($certPath . "读取失败。");
        }
        
        openssl_pkcs12_read ( $pkcs12certdata, $certs, $certPwd );
        $x509data = $certs ['cert'];

        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert->certId = $certdata ['serialNumber'];

// 		$certId = CertSerialUtil::getSerial($x509data, $errMsg);
// 		if($certId === false){
//         	$logger->LogInfo("签名证书读取序列号失败：" . $errMsg);
//         	return;
// 		}
//         $cert->certId = $certId;
        
        $cert->key = $certs ['pkey'];
        $cert->cert = $x509data;

        $logger->LogInfo("签名证书读取成功，序列号：" . $cert->certId);
        CertUtil::$signCerts[$certPath] = $cert;
    }

    public static function getSignKeyFromPfx($certPath=SDK_SIGN_CERT_PATH, $certPwd=SDK_SIGN_CERT_PWD)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) {
            self::initSignCert($certPath, $certPwd);
        }
        return CertUtil::$signCerts[$certPath] -> key;
    }

    public static function getSignCertIdFromPfx($certPath=SDK_SIGN_CERT_PATH, $certPwd=SDK_SIGN_CERT_PWD)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) {
            self::initSignCert($certPath, $certPwd);
        }
        return CertUtil::$signCerts[$certPath] -> certId;
    }

    private static function initEncryptCert($cert_path)
    {
        $logger = LogUtil::getLogger();
        $logger->LogInfo("读取加密证书……");
        $cert = new Cert();
	    $x509data = file_get_contents ( $cert_path );
        if($x509data === false ){
        	$logger->LogInfo($cert_path . "读取失败。");
        }
	    
	    openssl_x509_read ( $x509data );
	    $certdata = openssl_x509_parse ( $x509data );
	    $cert->certId = $certdata ['serialNumber'];

// 	    $certId = CertSerialUtil::getSerial($x509data, $errMsg);
// 	    if($certId === false){
// 	    	$logger->LogInfo("签名证书读取序列号失败：" . $errMsg);
// 	    	return;
// 	    }
// 	    $cert->certId = $certId;
	    
        $cert->key = $x509data;
        CertUtil::$encryptCerts[$cert_path] = $cert;
        $logger->LogInfo("加密证书读取成功，序列号：" . $cert->certId);
    }

    public static function getEncryptCertId($cert_path=SDK_ENCRYPT_CERT_PATH){
        if(!array_key_exists($cert_path, CertUtil::$encryptCerts)){
            self::initEncryptCert($cert_path);
        }
        return CertUtil::$encryptCerts[$cert_path] -> certId;
    }

    public static function getEncryptKey($cert_path=SDK_ENCRYPT_CERT_PATH){
        if(!array_key_exists($cert_path, CertUtil::$encryptCerts)){
            self::initEncryptCert($cert_path);
        }
        return CertUtil::$encryptCerts[$cert_path] -> key;
    }

    private static function initVerifyCerts($cert_dir=SDK_VERIFY_CERT_DIR) {
        $logger = LogUtil::getLogger();
        $logger->LogInfo ( '验证签名证书目录 :>' . $cert_dir );
        $handle = opendir ( $cert_dir );
        if (!$handle) {
            $logger->LogInfo ( '证书目录 ' . $cert_dir . '不正确' );
            return;
        }
        while ($file = readdir($handle)) {
            clearstatcache();
            $filePath = $cert_dir . '/' . $file;
            if (is_file($filePath)) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'cer') {
                    $x509data = file_get_contents($filePath);
			        if($x509data === false ){
			        	$logger->LogInfo($filePath . "读取失败。");
			        }
                    $cert = new Cert();
                    openssl_x509_read($x509data);
                    $certdata = openssl_x509_parse($x509data);
                    $cert->certId = $certdata ['serialNumber'];

//                     $certId = CertSerialUtil::getSerial($x509data, $errMsg);
//                     if($certId === false){
//                     	$logger->LogInfo("签名证书读取序列号失败：" . $errMsg);
//                     	return;
//                     }
//                     $cert->certId = $certId;

                    $cert->key = $x509data;
                    CertUtil::$verifyCerts[$cert->certId] = $cert;
                    $logger->LogInfo($filePath . "读取成功，序列号：" . $cert->certId);
                }
            }
        }
        closedir ( $handle );
    }

    public static function getVerifyCertByCertId($certId,$cert_dir=SDK_VERIFY_CERT_DIR){
        $logger = LogUtil::getLogger();
        if(count(CertUtil::$verifyCerts) == 0){
            self::initVerifyCerts($cert_dir);
        }
        if(count(CertUtil::$verifyCerts) == 0){
            $logger.LogInfo("未读取到任何证书……");
            return null;
        }
        if(array_key_exists($certId, CertUtil::$verifyCerts)){
            return CertUtil::$verifyCerts[$certId]->key;
        } else {
            $logger.LogInfo("未匹配到序列号为[" . certId . "]的证书");
            return null;
        }
    }
    
	public static function test() {
		
		$x509data = file_get_contents ( "d:/certs/acp_test_enc.cer" );
// 		$resource = openssl_x509_read ( $x509data );
		// $certdata = openssl_x509_parse ( $resource ); //<=这句尼玛内存泄漏啊根本释放不掉啊啊啊啊啊啊啊
		// echo $certdata ['serialNumber']; //<=就是需要这个数据啦
		// echo $x509data;
		// unset($certdata); //<=没有什么用
		// openssl_x509_free($resource); //<=没有什么用x2
		echo CertSerialUtil::getSerial ( $x509data, $errMsg ) . "\n";
	}
}

class CertSerialUtil {
	 
	private static function bytesToInteger($bytes) {
		$val = 0;
		for($i = 0; $i < count ( $bytes ); $i ++) {
// 			$val += (($bytes [$i] & 0xff) << (8 * (count ( $bytes ) - 1 - $i)));
			$val += $bytes [$i] * pow(256, count ( $bytes ) - 1 - $i);
// 			echo $val . "<br>\n";
		}
		return $val;
	}
	
	private static function bytesToBigInteger($bytes) {
		$val = 0;
		for($i = 0; $i < count ( $bytes ); $i ++) {
			$val = bcadd($val, bcmul($bytes [$i], bcpow(256, count ( $bytes ) - 1 - $i)));
// 			echo $val . "<br>\n";
		}
		return $val;
	}
	
	private static function toStr($bytes) {
		$str = '';
		foreach($bytes as $ch) {
			$str .= chr($ch);
		}
		return $str;
	}
	
	public static function getSerial($fileData, &$errMsg) {
		
// 		$fileData = str_replace('\n','',$fileData);
// 		$fileData = str_replace('\r','',$fileData);
		
		$start = "-----BEGIN CERTIFICATE-----";
		$end = "-----END CERTIFICATE-----";
		$data = trim ( $fileData );
		if (substr ( $data, 0, strlen ( $start ) ) != $start || 
		substr ( $data, strlen ( $data ) - strlen ( $end ) ) != $end) {
			// echo $fileData;
			$errMsg = "error pem data";
			return false;
		}
		
		$data = substr ( $data, strlen ( $start ), strlen ( $data ) - strlen ( $end ) - strlen ( $start ) );
		$bindata = base64_decode ( $data );
		$bindata = unpack ( 'C*', $bindata );
		
		$byte = array_shift ( $bindata );
		if ($byte != 0x30) {
			$errMsg = "1st tag " . $byte . " is not 30"; 
			return false;
		}
		
		$length = CertSerialUtil::readLength ( $bindata ); 
		$byte = array_shift ( $bindata );
		if ($byte != 0x30) {
			$errMsg = "2nd tag " . $byte . " is not 30";
			return false;
		}
		
		$length = CertSerialUtil::readLength ( $bindata );
		$byte = array_shift ( $bindata );
// 		echo $byte . "<br>\n";
		if ($byte == 0xa0) { //version tag.
			$length = CertSerialUtil::readLength ( $bindata );
			CertSerialUtil::readData ( $bindata, $length );
			$byte = array_shift ( $bindata );
		}

// 		echo $byte . "<br>\n";
		if ($byte != 0x02) { //x509v1 has no version tag, x509v3 has.
			$errMsg = "4th/3rd tag " . $byte . " is not 02";
			return false;
		}
		$length = CertSerialUtil::readLength ( $bindata );
		$serial = CertSerialUtil::readData ( $bindata, $length );
// 		echo bin2hex(CertSerialUtil::toStr( $serial ));
		return CertSerialUtil::bytesToBigInteger($serial);
	}
	
	private static function readLength(&$bindata) {
		$byte = array_shift ( $bindata );
		if ($byte < 0x80) {
			$length = $byte;
		} else {
			$lenOfLength = $byte - 0x80;
			for($i = 0; $i < $lenOfLength; $i ++) {
				$lenBytes [] = array_shift ( $bindata );
			}
			$length = CertSerialUtil::bytesToInteger ( $lenBytes );
		}
		return $length;
	}
	
	private static function readData(&$bindata, $length) {
		$data = array ();
		for($i = 0; $i < $length; $i ++) {
			$data [] = array_shift ( $bindata );
		}
		return $data;
	}
}







    