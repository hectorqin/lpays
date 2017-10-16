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
/**
 * ����ǵ���bdpay_sdk�����ɰٶ�Ǯ���˿�ӿ�URL��DEMO
 *
 */

if (!defined("bdpay_sdk_ROOT"))
{
	define("bdpay_sdk_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

require_once(bdpay_sdk_ROOT . 'bdpay_sdk.php');
require_once(bdpay_sdk_ROOT . 'bdpay_refund.cfg.php');
error_reporting(0);
$bdpay_sdk = new bdpay_sdk();
/*
*refund.htmlҳ���ȡ�Ĳ���
*/

$output_type = $_POST['output_type'];
$output_charset = $_POST['output_charset_f'];
$return_url = $_POST['return_url_f']; 
$sp_refund_no = date("YmdHis"). sprintf ( '%06d', rand(0, 999999));
$order_no = $_POST['order_no_f'];
$return_method= $_POST['return_method_f'];
$cashback_amount = $_POST['cashback_amount_f']; 
$cashback_time= date("YmdHis");
/*
 * �ַ�����ת�����ٶ�Ǯ��Ĭ�ϵı�����GBK���̻���ҳ�ı���������ǣ���ת�롣�漰�����ĵ��ֶ���μ��ӿ��ĵ�
 * ���裺
 * 1. URLת��
 * 2. �ַ�����ת�룬ת��GBK
 * 
 * $good_name = iconv("UTF-8", "GBK", urldecode($good_name));
 * $good_desc = iconv("UTF-8", "GBK", urldecode($good_desc));
 * 
 */

// ���ڲ��Ե��̻������˿�ӿڵı�����������ı���������Ķ����ȡֵ�μ��ӿ��ĵ�
$params = array (
		'service_code' => sp_conf::BFB_REFUND_INTERFACE_SERVICE_ID,	
		'input_charset' => sp_conf::BFB_INTERFACE_ENCODING,
		'sign_method' => sp_conf::SIGN_METHOD_MD5,
		'output_type' => $output_type,
		'output_charset' => $output_charset,
		'return_url' => $return_url,
		'return_method' => $return_method,
		'version' =>  sp_conf::BFB_INTERFACE_VERSION,
		'sp_no' => sp_conf::SP_NO(),
		'order_no'=>$order_no,
		'cashback_amount' => $cashback_amount,
		'cashback_time' => $cashback_time,
		'currency' => sp_conf::BFB_INTERFACE_CURRENTCY,
		'sp_refund_no' => $sp_refund_no
);

$refund_url = $bdpay_sdk->create_baifubao_Refund_url($params, sp_conf::BFB_REFUND_URL);

$bdpay_sdk->request($refund_url);
if(false === $refund_url){
	$bdpay_sdk->log('create the url for baifubao pay interface failed');
}
else {
	$bdpay_sdk->log(sprintf('create the url for baifubao pay interface success, [URL: %s]', $refund_url));
	//echo "<script>window.location=\"" . $refund_url . "\";</script>";
}

?>