<?php
use LPAY\Pay\PayResult;

use LPAY\Pay\Money;
use LPAY\Loger;
use LPAY\Loger\FileHandler;
use LPAY\Pay\RefundResult;
use LPAY\Transfers\TransfersResult;
use LPAY\Pay;
use LPAY\Pay\PayAdapterNotify;
use LPAY\Bill\Result;
include_once __DIR__."/../vendor/autoload.php";
include_once __DIR__."/utils/PayUtils.php";
include_once __DIR__."/utils/PayUtils/Pay.php";
include_once __DIR__."/utils/PayUtils/Refund.php";
include_once __DIR__."/utils/PayUtils/Transfers.php";



//注册日志目录
Loger::reg_handler(new FileHandler(__DIR__."/logs"));

//回调基本路径
define("CALLBACK_PATH", "http://testpay.com/dome/");
define("TYPE_CALLBACK", 0);
define("TYPE_NOTIFY", 1);

//支付统一处理.....
function pay_callback($pay,PayResult $pay_result,$type=TYPE_CALLBACK){
	//支付完成回调
	if ($type==TYPE_CALLBACK){
		if ($pay_result->get_status()==PayResult::STATUS_SUCC){
			//支付完成回调
			echo "<pre>支付成功\n";
			echo "支付方式:".$pay_result->get_name()."\n";
			echo "订单号:".$pay_result->get_pay_sn()."\n";
			echo "支付号:".$pay_result->get_pay_no()."\n";
			if ($pay_result->get_money()){
				echo "支付金额:".$pay_result->get_money()->to(Money::CNY)."\n";
			}
			if ($pay_result->get_seller()){
				echo "支付用户:".$pay_result->get_pay_account()."\n";
			}
			echo "其他参数:\n";
			print_r($pay_result->get_params());
			echo "\n</pre>";
		}else if ($pay_result->get_status()==PayResult::STATUS_FAIL){
			echo "<pre>支付失败"."\n";
			echo $pay_result->get_msg()."\n</pre>";
		}else{
			echo "<pre>"."\n";
			echo $pay_result->get_msg()."\n</pre>";
		}
	}else{
		if (!$pay instanceof PayAdapterNotify) die('fail');
		//后台回调,不能处理抛异常.
		if ($pay_result->get_status()==PayResult::STATUS_SUCC){
			$pay_result->get_pay_no();
			$pay_result->get_pay_sn();
			//error... set false
			$status=true;
			$msg='';
			$pay->pay_notify_output($status,$msg);
			//
		}else{
			if($pay_result->get_msg()==PayResult::$sign_invalid){
				$pay->pay_notify_output(false,PayResult::$sign_invalid);
			}else{
				$pay->pay_notify_output(true);
			}
		}
	}
}

function result_callback(PayResult $pay_result){
	//支付完成回调
	if ($pay_result->get_status()==PayResult::STATUS_SUCC){
		//支付完成回调
		echo "<pre>支付成功\n";
		echo "支付方式:".$pay_result->get_name()."\n";
		echo "订单号:".$pay_result->get_pay_sn()."\n";
		echo "支付号:".$pay_result->get_pay_no()."\n";
		if ($pay_result->get_money()){
			echo "支付金额:".$pay_result->get_money()->to(Money::CNY)."\n";
		}
		if ($pay_result->get_seller()){
			echo "支付用户:".$pay_result->get_pay_account()."\n";
		}
		echo "其他参数:\n";
		print_r($pay_result->get_params());
		echo "\n</pre>";
	}else if ($pay_result->get_status()==PayResult::STATUS_FAIL){
		echo "<pre>支付失败"."\n";
		echo $pay_result->get_msg()."\n</pre>";
	}else{
		echo "<pre>"."\n";
		echo $pay_result->get_msg()."\n</pre>";
	}
}

//统一退款处理
function refund_callback(RefundResult $refund_result){
	//支付完成回调
	if ($refund_result->get_status()==RefundResult::STATUS_SUCC){
		//支付完成回调
		echo "退款完成成功\n";
		echo "退款号:".$refund_result->get_refund_no()."\n";
		echo "支付方退款号:".$refund_result->get_refund_pay_no()."\n";
		print_r($refund_result);
	}else if ($refund_result->get_status()==PayResult::STATUS_FAIL){
		echo "退款失败:";
		echo $refund_result->get_msg();//save to db...
	}else{
		echo "success";//其他
	}
}


//统一转账处理
function transfers_callback(TransfersResult $transfers_result){
	//支付完成回调
	if ($transfers_result->get_status()==TransfersResult::STATUS_SUCC){
		//支付完成回调
		echo "转账完成\n";
		echo "转账号:".$transfers_result->get_transfers_no()."\n";
	}else if ($transfers_result->get_status()==TransfersResult::STATUS_FAIL){
		 //"退款失败"."\n";
		echo $transfers_result->get_msg();//save to db...
	}else{
		//other
	}
}

//统一转账处理
function bill_callback($bill,Result $result){
	//get_class($bill);//根据不同的类进行不同处理
	print_r($result);//保存到数据库或其他;
}

