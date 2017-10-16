<?php
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/pay_public.php";

/**
 * 支付页面
 */
//设备类型
$type=isset($_GET['type'])?intval($_GET['type']):LPAY\Pay::TYPE_WAP;
$pay_utils=\LPAY\PayUtils\Pay::instance();
$pays=$pay_utils
->get_pay()
//->set_type($type)
->get_pays();


function to_pay($name){
	echo "<a href='./pay.php?name={$name}&amount=0.01'>支付1元</a>";
}




foreach ($pays as $v){
	echo "支付类型:";
	if (is_array($v->support_name())){
		foreach ($v->support_name() as $vv){
			if (get_class($v)=='LPAY\Adapter\Baidu\PayBank'){
				echo "网银:".LPAY\Adapter\Baidu\PayBank::$banks[$vv];
			}else echo $vv;
			to_pay($vv);
		}
	}else{
		echo $v->support_name();
		to_pay($v->support_name());
	}
	echo "<br>";
}

