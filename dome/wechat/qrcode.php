<?php
use LPAY\Pay\PayRender;
include __DIR__."/../Bootstarp.php";
PayRender::qrcode_render('STSONG.TTF','扫描二维码进行付款','logo.png');