<?php

/**
 * ����̻���returl_urlҳ��ʵ�ֵ�ģ��
 * ��ҳ���ҵ���߼��ǣ�
 * 1. ���̻��յ��ٶ�Ǯ��֧���ɹ���֪ͨ�󣬵���sdk��Ԥ�������ȷ���ö���֧���ɹ�
 * 2. ȷ��֧���ɹ����̻��Լ���ҵ���߼����������֮��ġ�
 * ע�⣬sdk�е�query_order_state()�����������̻��Լ�ʵ�֣�
 * ����������յ���ΰٶ�Ǯ����֧�����֪ͨ�������̻��Լ������ʽ�Ĳ�һ�¡�
 */
require_once 'bdpay_sdk.php';

$bdpay_sdk = new bdpay_sdk();

$bdpay_sdk->log(sprintf('get the notify from baifubao, the request is [%s]', print_r($_GET, true)));

if (false === $bdpay_sdk->check_bfb_pay_result_notify()) {
	$bdpay_sdk->log('get the notify from baifubao, but the check work failed');
	return;
}
$bdpay_sdk->log('get the notify from baifubao and the check work success');


/*
 * �˴����̻��յ��ٶ�Ǯ��֧�����֪ͨ����Ҫ�����Լ��ľ���ҵ���߼����������֮��ġ� ֻ�е��̻��յ��ٶ�Ǯ��֧�� ���֪ͨ��
 * ���е�Ԥ�����������������󣬲�ִ�иò���
 */

// ��ٶ�Ǯ�������ִ
$bdpay_sdk->notify_bfb();


?>