<?php
require_once  __DIR__."/Bootstarp.php";
//注册公共支付接口
$pay_utils=\LPAY\PayUtils\Transfers::instance();
//alipay
include_once 'alipay/alipay.config.php';
$pay_utils->set_alipay($alipay_config);

//wechat
include_once 'wechat/WxPay.Config.php';
$pay_utils->set_wechat(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());
