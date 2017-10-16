<?php
    //商户后台通知演示
		require("common/globalParam.php"); 
		require("common/callcmpay.php"); 
		
		//接收手机支付平台后台通知数据start
    $merchantId 	  			= $_POST["merchantId"];
	  $payNo 	  			= $_POST["payNo"];
	  $returnCode 	  = $_POST["returnCode"];
	  $message	  		= $_POST["message"];
	  $signType       	= $_POST["signType"];
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
		$payType        = $_POST["payType"];
		$orderDate      = $_POST["orderDate"];
		$fee            = $_POST["fee"];
    $vhmac			  	= $_POST["hmac"];
    //接收手机支付平台后台通知数据end
    $signKey        = $GLOBALS['signKey'];
		if($returnCode!=000000)
		{
        //此处表示后台通知产生错误
			  echo $returnCode.decodeUtf8($message);
				exit();
		}
		
		$signData = $merchantId .$payNo       .$returnCode .$message
               .$signType   .$type        .$version    .$amount
               .$amtItem    .$bankAbbr    .$mobile     .$orderId
               .$payDate    .$accountDate .$reserved1  .$reserved2
               .$status     .$orderDate  .$fee;
		$hmac=MD5sign($signKey,$signData);	
		if($hmac != $vhmac)
		  //此处无法信息数据来自手机支付平台
			echo "验签失败";
		else{
			//商户在此处做业务处理，处理完毕必须响应SUCCESS
			echo "SUCCESS";
		}

?>