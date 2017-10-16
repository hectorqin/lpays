<?php

require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/transfers_public.php";

$pays = LPAY\PayUtils\Transfers::instance()->get_pay()->get_transfers();



function to_pay($name){
	echo "<a href='./transfers.php?name={$name}&amount=1'>提现1元</a>";
}

foreach ($pays as $v){
	echo "支付类型:";
	echo $v->transfers_name();
	to_pay($v->transfers_name());
	echo "<br>";
}


