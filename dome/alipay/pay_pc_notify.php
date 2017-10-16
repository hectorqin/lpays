<?php
use LPAY\PayUtils\Pay;
include __DIR__."/../Bootstarp.php";
include_once 'alipay.config.php';
$pay=Pay::alipay_pc($alipay_config);
pay_callback($pay,$pay->pay_notify(),TYPE_NOTIFY);
$pay->pay_notify_output(true);