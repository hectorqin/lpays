<?php
use LPAY\PayUtils\Pay;

include __DIR__."/../Bootstarp.php";
$config=include_once 'cfg.php';
$pay=Pay::baidu_pc($config);
pay_callback($pay,$pay->pay_callback());