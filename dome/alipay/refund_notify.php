<?php
use LPAY\PayUtils\Refund;
include __DIR__."/../Bootstarp.php";
include_once 'alipay.config.php';
$refund=Refund::alipay($alipay_config);
refund_callback($refund->refund_notify());
