<?php

use LPAY\PayUtils\Pay;
include __DIR__."/../Bootstarp.php";
include_once './WxPay.Config.php';
$pay=Pay::wechat_pc(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());
pay_callback($pay,$pay->pay_notify(),TYPE_NOTIFY); 