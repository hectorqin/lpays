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
 * ����̻���returl_urlҳ��ʵ�ֵ�ģ��
 * ��ҳ���ҵ���߼��ǣ�
 * 1. ���̻��յ��ٶ�Ǯ���˿�ɹ���֪ͨ�󣬵���sdk��Ԥ�������ȷ���ö����˿�ɹ�
 * 2. ȷ���˿�ɹ����̻��Լ���ҵ���߼��������޸Ķ���״̬֮��ġ�
 * ע�⣬sdk�е�query_order_state()�����������̻��Լ�ʵ�֣�
 * ����������յ���ΰٶ�Ǯ�����˿���֪ͨ�������̻��Լ������ʽ�Ĳ�һ�¡�
 */
error_reporting(0);
require_once 'bdpay_sdk.php';

$bdpay_sdk = new bdpay_sdk();

$bdpay_sdk->log(sprintf('get the notify from baifubao, the request is [%s]', print_r($_GET, true)));

print_r('�ٶ�Ǯ���˿���֪ͨ���ز�����ӡ��');
print_r($_GET);

if (false === $bdpay_sdk->check_bfb_refund_result_notify()) {
	$bdpay_sdk->log('get the notify from baifubao, but the check work failed');
	return;
}
$bdpay_sdk->log('get the notify from baifubao and the check work success');


/*
 * �˴����̻��յ��ٶ�Ǯ���˿���֪ͨ����Ҫ�����Լ��ľ���ҵ���߼��������޸Ķ���״̬֮��ġ� ֻ�е��̻��յ��ٶ�Ǯ���˿� ���֪ͨ��
 * ���е�Ԥ�����������������󣬲�ִ�иò���
 */

// ��ٶ�Ǯ�������ִ
$bdpay_sdk->notify_bfb();


?>