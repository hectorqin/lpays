<?php

use LPAY\PayUtils\Pay;
include __DIR__."/../Bootstarp.php";
$_config=include_once './cfg.php';
$pay=Pay::upacp($_config);
pay_callback($pay,$pay->pay_callback());