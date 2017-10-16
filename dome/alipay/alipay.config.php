<?php
/* *
 * 配置文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
	
 * 提示：如何获取安全校验码和合作身份者id
 * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
 * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
 * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”
	
 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
 * 解决方法：
 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
 * 2、更换浏览器或电脑，重新登录查询。
 */

$alipay_config=array();
$alipay_config['seller_email'] ='test@qq.com';

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
$alipay_config['partner']		= '0000';

//安全检验码，以数字和字母组成的32位字符
$alipay_config['key']			= '0000';

//商户的私钥（后缀是.pen）文件相对路径
//如果签名方式设置为“0001”时，请设置该参数
$alipay_config['private_key_path']	= __DIR__.'/rsa_private_key.pem';

//支付宝公钥（后缀是.pen）文件相对路径
//如果签名方式设置为“0001”时，请设置该参数
$alipay_config['ali_public_key_path']= __DIR__.'/alipay_public_key.pem';


//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


//签名方式 不需修改
$alipay_config['sign_type']    = 'RSA';

//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= 'utf-8';

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = __DIR__.'/cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';

//-------------------------------------





//支付用到
$alipay_config['pay_pc_notify_url']    = CALLBACK_PATH.'alipay/pay_pc_notify.php';
$alipay_config['pay_pc_return_url']    = CALLBACK_PATH.'alipay/pay_pc_return.php';
$alipay_config['pay_wap_notify_url']    = 'http://www.test.tk/Cache/Temp/ipn.php';//CALLBACK_PATH.'alipay/pay_wap_notify.php';
$alipay_config['pay_wap_return_url']    = CALLBACK_PATH.'alipay/pay_wap_return.php';
$alipay_config['pay_app_notify_url']    = CALLBACK_PATH.'alipay/pay_app_notify.php';

//退款
$alipay_config['refund_notify_url']    = 'http://www.test.tk/Cache/Temp/ipn.php';//CALLBACK_PATH.'alipay/refund_notify.php';

//提款用到
$alipay_config['seller_name'] ='';
$alipay_config['transfers_notify_url']    = CALLBACK_PATH.'alipay/transfers_notify.php';


//额外配置
