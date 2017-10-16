<?php
// cvn2加密 1：加密 0:不加密
const SDK_CVN2_ENC = 0;
// 有效期加密 1:加密 0:不加密
const SDK_DATE_ENC = 0;
// 卡号加密 1：加密 0:不加密
const SDK_PAN_ENC = 0;
 
$DIR=dirname(__FILE__)."/../../";

// 签名证书路径 (从商户后台获得)
define('SDK_SIGN_CERT_PATH', $DIR."PM_700000000000001_acp.pfx");
//const SDK_SIGN_CERT_PATH = 'D:\xampp\htdocs\route_os\upacp_sdk_php\PM_700000000000001_acp.pfx';

// 签名证书密码 (从商户后台获得)
 const SDK_SIGN_CERT_PWD = '000000';


// 验签证书（这条用不到的请随便配）
 define('SDK_VERIFY_CERT_PATH', $DIR."UPOP_VERIFY.cer");
//const SDK_VERIFY_CERT_PATH = 'D:/certs/UPOP_VERIFY.cer';

// 密码加密证书
 define('SDK_ENCRYPT_CERT_PATH', $DIR."RSA2048_PROD_index_22.cer");
//const SDK_ENCRYPT_CERT_PATH = 'D:\xampp\htdocs\route_os\upacp_sdk_php\RSA2048_PROD_index_22.cer';

// 验签证书路径（请配到文件夹，不要配到具体文件）
 define('SDK_VERIFY_CERT_DIR', $DIR);
//const SDK_VERIFY_CERT_DIR = 'D:\xampp\htdocs\route_os\upacp_sdk_php/';

 // 前台请求地址
 const SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/gateway/api/frontTransReq.do';
 
 // 后台请求地址
 const SDK_BACK_TRANS_URL = 'https://gateway.95516.com/gateway/api/backTransReq.do';
 
 // 批量交易
 const SDK_BATCH_TRANS_URL = 'https://gateway.95516.com/gateway/api/batchTrans.do';
 
 //单笔查询请求地址
 const SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/gateway/api/queryTrans.do';
 
 //文件传输请求地址
 const SDK_FILE_QUERY_URL = 'https://filedownload.95516.com/';
 
 //有卡交易地址
 const SDK_Card_Request_Url = 'https://gateway.95516.com/gateway/api/cardTransReq.do';
 
 //App交易地址
 const SDK_App_Request_Url = 'https://gateway.95516.com/gateway/api/appTransReq.do';
 

// 前台通知地址 (商户自行配置通知地址)
const SDK_FRONT_NOTIFY_URL = 'http://localhost:8085/upacp_demo_app/demo/api_05_app/FrontReceive.php';

// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
const SDK_BACK_NOTIFY_URL = 'http://222.222.222.222/upacp_demo_app/demo/api_05_app/BackReceive.php';



//文件下载目录
const SDK_FILE_DOWN_PATH = './';

//日志 目录
// const SDK_LOG_FILE_PATH = $DIR.'./';
define('SDK_LOG_FILE_PATH',$DIR.'logs/');

//日志级别
define('SDK_LOG_LEVEL',6);//OFF

