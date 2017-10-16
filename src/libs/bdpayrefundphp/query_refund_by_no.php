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
 * ����ǵ���bdpay_sdk��ͨ���ٶ�Ǯ�������Ų�ѯ�˿���Ϣ��DEMO
 *
 */
if (!defined("bdpay_sdk_ROOT"))
{
	define("bdpay_sdk_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
error_reporting(0);
require_once(bdpay_sdk_ROOT . 'bdpay_sdk.php');
require_once(bdpay_sdk_ROOT . 'bdpay_refund.cfg.php');

$bdpay_sdk = new bdpay_sdk();

$order_no = $_POST['order_no_f'];


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

// ���ڲ��Ե��̻�����֧���ӿڵı�����������ı���������Ķ����ȡֵ�μ��ӿ��ĵ�
		
$content = $bdpay_sdk->query_baifubao_refund_result_by_order_no($order_no);

if(false === $content){
	$bdpay_sdk->log('create the url for baifubao query interface failed');
}
else {
	$bdpay_sdk->log('create the url for baifubao query interface success');
	echo "��ѯ�ɹ�\n";
	echo $content;
}

?>