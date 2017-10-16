<?php
use LPAY\PayUtils\Pay;
include __DIR__."/../Bootstarp.php";
$_config=include_once './cfg.php';
$pay=Pay::palpay_direct($_config);
$pay->direct_pay_from_post();