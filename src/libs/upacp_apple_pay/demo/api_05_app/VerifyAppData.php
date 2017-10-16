<?php
header ( 'Content-type:text/html;charset=utf-8' );
include_once $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/sdk/acp_service.php';

/**
 * 对控件给商户APP返回的应答信息验签，前段请直接把string型的json串post上来
 */
$data = file_get_contents('php://input', 'r');
echo com\unionpay\acp\sdk\AcpService::validateAppResponse($data) ? "true" : "false";