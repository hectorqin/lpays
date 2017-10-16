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
 * 这个商户的returl_url页面实现的模板
 * 该页面的业务逻辑是：
 * 1. 当商户收到百度钱包退款成功的通知后，调用sdk中预处理操作确定该订单退款成功
 * 2. 确认退款成功后，商户自己的业务逻辑，比如修改订单状态之类的。
 * 注意，sdk中的query_order_state()方法，必须商户自己实现，
 * 否则会由于收到多次百度钱包的退款结果通知，导致商户自己出现资金的不一致。
 */
error_reporting(0);
require_once 'bdpay_sdk.php';

$bdpay_sdk = new bdpay_sdk();

$bdpay_sdk->log(sprintf('get the notify from baifubao, the request is [%s]', print_r($_GET, true)));

print_r('百度钱包退款结果通知返回参数打印：');
print_r($_GET);

if (false === $bdpay_sdk->check_bfb_refund_result_notify()) {
	$bdpay_sdk->log('get the notify from baifubao, but the check work failed');
	return;
}
$bdpay_sdk->log('get the notify from baifubao and the check work success');


/*
 * 此处是商户收到百度钱包退款结果通知后需要做的自己的具体业务逻辑，比如修改订单状态之类的。 只有当商户收到百度钱包退款 结果通知后，
 * 所有的预处理工作都返回正常后，才执行该部分
 */

// 向百度钱包发起回执
$bdpay_sdk->notify_bfb();


?>