<?php
use LPAY\PayUtils\Pay;

include __DIR__."/../Bootstarp.php";
$config=include_once 'cfg.php';
$pay=Pay::tenpay_wap($config);
pay_callback($pay,$pay->pay_notify(),TYPE_NOTIFY); 