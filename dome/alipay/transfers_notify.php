<?php
use LPAY\PayUtils\Refund;
use LPAY\PayUtils\Transfers;
include __DIR__."/../Bootstarp.php";
include_once 'alipay.config.php';
$refund=Transfers::alipay($alipay_config);
transfers_callback($refund->transfers_notify());