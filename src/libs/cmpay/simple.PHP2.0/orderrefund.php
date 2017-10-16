<html>
	<head>
		<title>API退款测试</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
			require("common/globalParam.php"); 
		 	require("common/callcmpay.php"); 	
		  
		  //设置退款请求参数 start
			$type       = "OrderRefund";
			$reqUrl     = $GLOBALS['reqUrl'];
			$amount     =  $_POST['amount'];
			$orderId    = $_POST['orderId'];             
			$merchantId = $GLOBALS['merchantId'];
			$requestId  = $GLOBALS['requestId'];
			$signType   = $GLOBALS['signType'];
			$version    = $GLOBALS['version'];
			//设置退款请求参数 start
			
			//组织验签数据			
			$signData = $merchantId.$requestId.$signType.$type.$version.$orderId.$amount;

			$signKey=$GLOBALS['signKey'];
			//MD5方式签名
			$hmac=MD5sign($signKey,$signData);
			  				 
			$requestData = array(); 			
			$requestData['merchantId']=$merchantId;
			$requestData['requestId']=$requestId;
			$requestData['type']=$type;
			$requestData['version']=$version;
			$requestData['orderId']=$orderId;
			$requestData['amount']=$amount;
			$requestData['hmac'] = $hmac;
			$requestData['signType']=$signType;
							            
			$sTotalString = POSTDATA($reqUrl,$requestData);
			//订单查询接口返回 form格式数据处理
			$recv = $sTotalString["MSG"];
			$recvArray = parseRecv($recv);
			
			$code = $recvArray['returnCode'];
			$message =$recvArray['message'];
			if ("000000" != $code) {
				  //商户在此处做退款失败业务处理
					echo "退款失败".$code.decodeUtf8($message);
			}
			else
			{
				$vfsign=$recvArray['merchantId'].$recvArray['payNo']
					   .$recvArray['returnCode'].decodeUtf8($recvArray['message'])
					   .$recvArray['signType'].$recvArray['type'].$recvArray['version']
					   .$recvArray['amount'].$recvArray['orderId']
					   .$recvArray['status'];		
			  $vhmac = $recvArray['hmac'];
			  $hmac=MD5sign($signKey,$vfsign);
			  if($hmac!=$vhmac)
			  {
			    //此处商户无法信任数据为手机支付平台返回原始数据
			  	echo "商户端：验证签名失败!";
			  	exit();
			  }
			  else
			  {
			  	//商户此处做退款成功后续业务处理
			  	echo "================";
			  	echo "</br>";
			  	echo "交易成功";
			  	echo "</br>";
			  	echo "================";
			  	echo "</br>";
			  	echo "退款金额:".$recvArray['amount'];
			  	echo "</br>";
			  	echo "商户订单号:".$recvArray['orderId'];
			  	echo "</br>";
			  	echo "退款结果:".$recvArray['status'];
			  	echo "</br>";
			  	
			  }				
			}
		?>

	</body>
</html>
