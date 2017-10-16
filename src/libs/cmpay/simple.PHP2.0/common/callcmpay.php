<?php
/****************************************************
callcmpay.php

This file uses the constants.php to get parameters needed 
to make an API call and calls the server.if you want use your
own credentials, you have to change the constants.php

Called by ..

****************************************************/
require("log.php"); 

/*
* 功能：把GB2312编码转换成UTF-8的编码
* 程序员：wlxz
*　日期：2002-00-00
*/

function gb2utf8($gb){
  if(!trim($gb))
    return $gb;

  $filename="gb2312.txt";
  $tmp=file($filename);
  $codetable=array();
  
  while(list($key,$value)=each($tmp))
    $codetable[hexdec(substr($value,0,6))]=substr($value,7,6);
  
  $ret="";
  $utf8="";
  
  while($gb){
    if (ord(substr($gb,0,1))>127)
        {
        $the=substr($gb,0,2);
        $gb=substr($gb,2,strlen($gb));
        $utf8=u2utf8(hexdec($codetable[hexdec(bin2hex($the))-0x8080]));

        for($i=0;$i<strlen($utf8);$i+=3)
          $ret.=chr(substr($utf8,$i,3));
        }
        else{
          $ret.=substr($gb,0,1);
          $gb=substr($gb,1,strlen($gb));
          }
  }
  
  return $ret;
}


function u2utf8($c){
  for($i=0;$i<count($c);$i++)
    $str="";

  if ($c < 0x80){
    $str.=$c;
  }
  else if ($c < 0x800){
    $str.=(0xC0 | $c>>6);
    $str.=(0x80 | $c & 0x3F);
  }
  else if ($c < 0x10000){
    $str.=(0xE0 | $c>>12);
    $str.=(0x80 | $c>>6 & 0x3F);
    $str.=(0x80 | $c & 0x3F);
  }
  else if ($c < 0x200000){
    $str.=(0xF0 | $c>>18);
    $str.=(0x80 | $c>>12 & 0x3F);
    $str.=(0x80 | $c>>6 & 0x3F);
    $str.=(0x80 | $c & 0x3F);
  }
  
  return $str;
}


function gb2unicode($gb){
  if(!trim($gb))
    return $gb;
  
  $filename="gb2312.txt";
  $tmp=file($filename);
  $codetable=array();

  while(list($key,$value)=each($tmp))
    $codetable[hexdec(substr($value,0,6))]=substr($value,9,4);
  $utf="";
  while($gb){
    if (ord(substr($gb,0,1))>127){
        $the=substr($gb,0,2);
        $gb=substr($gb,2,strlen($gb));
        $utf.="&#x".$codetable[hexdec(bin2hex($the))-0x8080].";";
        }
        else{
          $gb=substr($gb,1,strlen($gb));
          $utf.=substr($gb,0,1);
          }
  }
  return $utf;
}

/*
 功能 发送HTTP请求
  URL  请求地址
  data 请求数据数组
*/
function POSTDATA($url, $data)
{
	$url = parse_url($url);
	if (!$url)
	{
		RecordLog("couldn't parse url");
		return "couldn't parse url";
	}
	if (!isset($url['port'])) { $url['port'] = ""; }

	if (!isset($url['query'])) { $url['query'] = ""; }


	$encoded = "";

	while (list($k,$v) = each($data))
	{
		$encoded .= ($encoded ? "&" : "");
		$encoded .= rawurlencode($k)."=".rawurlencode($v);
	}
	$urlHead = null;
	$urlPort = $url['port'];
	if($url['scheme'] == "https")
	{
		$urlHead = "ssl://".$url['host'];
		if($url['port'] == null || $url['port'] == 0)
		{
			$urlPort = 443;
		}
	}
	else
	{
		$urlHead = $url['host'];
		if($url['port'] == null || $url['port'] == 0)
		{
			$urlPort = 80;
		}
	}
	RecordLog("YGM",$urlHead);
	$fp = fsockopen($urlHead, $urlPort);

	if (!$fp) return "Failed to open socket to $url[host]";

	$tmp="";
	$tmp.=sprintf("POST %s%s%s HTTP/1.0\r\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
	$tmp.="Host: $url[host]\r\n";
	$tmp.="Content-type: application/x-www-form-urlencoded\r\n";
	$tmp.="Content-Length: " . strlen($encoded) . "\r\n";
	$tmp.="Connection: close\r\n\r\n";
	$tmp.="$encoded\r\n";
	fputs($fp,$tmp);

	$line = fgets($fp,1024);

	if (!preg_match("#^HTTP/1\.. 200#i", $line))
	{
		$logstr = "MSG".$line;
		RecordLog("YGM",$logstr);
		return array("FLAG"=>0,"MSG"=>$line);
	}

	$results = ""; $inheader = 1;
	while(!feof($fp))
	{
		$line = fgets($fp,1024);
		if ($inheader && ($line == "\n" || $line == "\r\n"))
		{
			$inheader = 0;
		}
		elseif (!$inheader)
		{
			$results .= $line;
		}
	}
	fclose($fp);
	return array("FLAG"=>1,"MSG"=>$results);
} 

//MD5方式签名
function MD5sign($okey,$odata)
{
	 $signdata=hmac("",$odata);			     
	 return hmac($okey,$signdata);
}
function hmac ($key, $data)
{
  $key = iconv('gb2312', 'utf-8', $key);
  $data = iconv('gb2312', 'utf-8', $data);
  $b = 64;
  if (strlen($key) > $b) {
  $key = pack("H*",md5($key));
  }
  $key = str_pad($key, $b, chr(0x00));
  $ipad = str_pad('', $b, chr(0x36));
  $opad = str_pad('', $b, chr(0x5c));
  $k_ipad = $key ^ $ipad ;
  $k_opad = $key ^ $opad;
 return md5($k_opad . pack("H*",md5($k_ipad . $data)));
} 

/*
 功能 把http请求返回数组 格式化成数组
*/
function parseRecv($source)
{
	$ret = array();
	$temp = explode("&",$source);
	
	foreach ($temp as $value)
	{
		$index=strpos($value,"=");
		$_key=substr($value,0,$index);
		$_value=substr($value,$index+1);
		$ret[$_key] = $_value;
	}

	return $ret;
}
/*
	功能：把UTF-8 编号数据转换成 GB2312 忽略转换错误
*/
function decodeUtf8($source)
{
	$temp = urldecode($source);
	$ret = iconv("UTF-8","GB2312//IGNORE",$temp);
	return $ret;
}
/*获取用户IP地址*/
function getClientIP()  
{  
	if(!empty($_SERVER["HTTP_CLIENT_IP"]))
	{
		$cip = $_SERVER["HTTP_CLIENT_IP"];  
	}
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if(!empty($_SERVER["REMOTE_ADDR"]))
	{
		$cip = $_SERVER["REMOTE_ADDR"];  
	}
	else
	{
		$cip = "unknown";  
	}
	return $cip;
	  
} 
//返回URL处理
function parseUrl($payUrl)
{
	$temp =explode("<hi:$$>",$payUrl);			
	$url_lst=explode("<hi:=>",$temp[0]);
	$url=$url_lst[1];
	$method_lst=explode("<hi:=>",$temp[1]);
	$method=$method_lst[1];
	$sessionid_lst=explode("<hi:=>",$temp[2]);
	$sessionid=$sessionid_lst[1];
	$url=$url."?SESSIONID=".$sessionid;
	$rpayUrl = array();		
	$rpayUrl["url"]=$url;
	$rpayUrl["method"]=$method;
	return $rpayUrl;
}
?>
