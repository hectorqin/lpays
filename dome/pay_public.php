<?php
require_once  __DIR__."/Bootstarp.php";
//注册公共支付接口
$pay_utils=\LPAY\PayUtils\Pay::instance();
//alipay
include_once 'alipay/alipay.config.php';

$pay_utils->set_alipay($alipay_config);


include_once 'aliopen/alipay.config.php';
$config=\LPAY\Adapter\AliOpen\PayConfig::arr($alipay_config);
isset($alipay_config['pay_wap_notify_url'])&&$config->set_notify_url($alipay_config['pay_wap_notify_url']);
isset($alipay_config['pay_wap_return_url'])&&$config->set_return_url($alipay_config['pay_wap_return_url']);
$pay_utils->get_pay()->add_pay(new \LPAY\Adapter\AliOpen\Alipay($config));

//wechat
include_once 'wechat/WxPay.Config.php';
$pay_utils->set_wechat(\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr());

//upacp
$_config=include_once './upacp_apple/cfg.php';
$pay_utils->set_upacp_apple($_config);
$_config=include_once './upacp/cfg.php';
$pay_utils->set_upacp($_config);

// //tenpay
$_config=include_once './tenpay/cfg.php';
$pay_utils->set_tenpay($_config);

//jdpay
$_config=include_once './jdpay/cfg.php';
$pay_utils->set_jdpay($_config);


//baidu
$_config=include_once './baidu/cfg.php';
$pay_utils->set_baidu($_config);




// //palpay
$_config=include_once './palpay/cfg.php';
$pay_utils->set_palpay($_config);
