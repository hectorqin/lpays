<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<title>通知服务演示</title>
</head>
<body>
<?php
    //包含商户配置与共通函数文件
		require("common/globalParam.php"); 
		require("common/callcmpay.php"); 
		
		//接收手机支付平台页面通知传递的报文 start
    $merchantId 	  = $_POST["merchantId"];
	  $payNo 	  			= $_POST["payNo"];
	  $returnCode 	  = $_POST["returnCode"];
	  $message	  		= $_POST["message"];
	  $signType      	= $_POST["signType"];
	  $type         	= $_POST["type"];
	  $version        	= $_POST["version"];
	  $amount         = $_POST["amount"];
		$amtItem			  = $_POST["amtItem"];		
    $bankAbbr	  		= $_POST["bankAbbr"];
    $mobile 			  = $_POST["mobile"];
    $orderId			  = $_POST["orderId"];
    $payDate			  = $_POST["payDate"];
		$accountDate    = $_POST["accountDate"];
    $reserved1	  	= $_POST["reserved1"];
		$reserved2	  	= $_POST["reserved2"];
    $status				  = $_POST["status"];
		$orderDate      = $_POST["orderDate"];
		$fee            = $_POST["fee"];
    $vhmac			  	= $_POST["hmac"];
    $signKey        = $GLOBALS['signKey'];
		//接收手机支付平台页面通知传递的报文 end
		
	  //组装签字符串
		$signData = $merchantId .$payNo.$returnCode .$message
               .$signType   .$type        .$version    .$amount
               .$amtItem    .$bankAbbr    .$mobile     .$orderId
               .$payDate    .$accountDate .$reserved1  .$reserved2
               .$status     .$orderDate   .$fee;
               
		//MD5方式签名
    $hmac=MD5sign($signKey,$signData);
		
		//此处000000仅代表程序无错误。订单是否支付成功是以支付结果（status）为准
		if($returnCode!=000000)
		{
			 echo $returnCode.decodeUtf8($message);
		}
		if($hmac != $vhmac)
			echo "验签失败";
		else{
			  /*商户在此处业务处理*/
			  echo "商户编号:".$orderId;
        echo "</br>";
        echo "支付金额:".$amount;
        echo "</br>";
        echo "支付银行:".$bankAbbr;
        echo "</br>";
        echo "支付人：".$mobile;
        echo "</br>";
        echo "支付时间：".$payDate;
        echo "</br>";
        echo "保留字段1：".decodeUtf8($reserved1);
        echo "</br>";
        echo "保留字段2：".decodeUtf8($reserved2);
        echo "</br>";
			  echo "支付结果:".$status;
			  echo "</br>";
		}

?>
</body>
</html>