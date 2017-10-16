<?php
use LPAY\PayUtils\Refund;

include __DIR__."/../Bootstarp.php";
$config=include_once 'cfg.php';
$refund=Refund::jd($config);
refund_callback($refund->refund_notify());