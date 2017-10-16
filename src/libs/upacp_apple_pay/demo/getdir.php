<?php
/**
 * 用于获取服务器绝对路径。
 * 将此文件放在服务器上，然后访问一下。
 * 比如获取到：
 * /Users/xxxx/Sites/upacp_demo_app/demo/getdir.php
 * 比如证书放在工程upacp_demo_app的certs文件夹下，
 * 则配置文件的绝对路径需要配置为/Users/xxxx/Sites/upacp_demo_app/cert/acp_test_sign.pfx
 * 其他路径配置也类似。
 */
function getDir()
 {
 	$a = debug_backtrace();
 	echo $a[0]["file"];
 }

getDir();

