<?php
use LPAY\Adapter\Palpay\IPN;


use LPAY\Adapter\Palpay\Config;
include __DIR__."/../Bootstarp.php";
$_config=include_once './cfg.php';
$ipn=new IPN(Config::arr($_config));
switch ($ipn->get_type()){
	case IPN::TYPE_PAYCALLBACK:
		pay_callback($ipn,$ipn->pay_notify(),TYPE_NOTIFY);
		$ipn->pay_notify_output(true);
	break;
	case IPN::TYPE_REFUND:
		refund_callback($ipn->refund_notify());
		$ipn->refund_notify_output(true);
	break;
	case IPN::TYPE_INVALID:
		$ipn->output(false);
	break;
	default:
		$ipn->output(true);
}