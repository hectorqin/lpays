<?php
use LPAY\PayUtils\Refund;

include __DIR__."/../Bootstarp.php";
$config=include_once 'cfg.php';
$refund=Refund::upacp_apple($config);
refund_callback($refund->refund_notify());