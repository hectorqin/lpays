<html>
	<head>
		<title>API订单查询</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
				require("common/globalParam.php"); 
		 		require("common/callcmpay.php"); 
					
				//设置订单查询请求参数start
			  $type       = "OrderQuery";
			  $reqUrl     = $GLOBALS['reqUrl'];
			  $orderId    = $_POST["orderId"];
				$merchantId = $GLOBALS['merchantId'];
				$requestId  = $GLOBALS['requestId'];
				$signType   = $GLOBALS['signType'];
				$version    = $GLOBALS['version'];
				//设置订单查询请求参数end
				
			  //组装前面字符串 顺序必须与接口文档说明保持一致
				$signData   = $merchantId.$requestId.$signType.$type.$version.$orderId;
						
				$signKey=$GLOBALS['signKey'];			
				
				//MD5方式签名
	      $hmac=MD5sign($signKey,$signData);       
				$requestData = array(); 
				$requestData['merchantId'] = $merchantId;
				$requestData['requestId'] = $requestId;
				$requestData['signType'] = $signType;
				$requestData['type'] = $type;
				$requestData['version'] = $version;
				$requestData['orderId'] = $orderId;
				$requestData['hmac'] = $hmac;

        				
				$sTotalString = POSTDATA($reqUrl,$requestData);
				$recv = $sTotalString["MSG"];
				
				//手机支付平台返回form格式数据处理
				$recvArray = parseRecv($recv);
				$code = $recvArray['returnCode'];
				$message = $recvArray['message'];
				
				if($code != "000000")
				{
				   //商户在此处做 订单查询失败业务处理
					 echo $code.decodeUtf8($message);
				}
				else
				{
					$vhmac = $recvArray['hmac'];
			    $payNo = $recvArray['payNo'];
			    $signType =  $recvArray['signType'];
			    $type = $recvArray['type'];
			    $version =$recvArray['version'];
			    $amount = $recvArray['amount'];
			    $amtItem = $recvArray['amtItem'];
			    $bankAbbr = $recvArray['bankAbbr'];
			    $mobile = $recvArray['mobile'];
			    $orderId = $recvArray['orderId'];
			    $payDate = $recvArray['payDate'];
			    $reserved1 = $recvArray['reserved1'];
			    $reserved2 = $recvArray['reserved2'];
			    $status = $recvArray['status'];
			    $orderDate = $recvArray['orderDate'];
			    $fee = $recvArray['fee'];			
				  $vfsign = $merchantId.$payNo    .$code     .$message
						      .$signType   .$type     .$version  .$amount
						      .$amtItem    .$bankAbbr .$mobile   .$orderId
						      .$payDate    .$reserved1.$reserved2.$status
						      .$orderDate.$fee;   
					$hmac=MD5sign($signKey,$vfsign);  
					if($hmac!=$vhmac)
					{
						//此处无法信任此数据是否为手机支付平台返回数据
						echo "验证签名失败!";
					}
					else
					{
						//商户在此处做订单查询成功业务处理
						echo "<br/>";
						echo "交易成功";
						echo "<br/>";
						echo "================";
						echo "商户编号".$merchantId;
						echo "<br/>";
						echo "流水号:".$payNo;
						echo "<br/>";
						echo "返回码:".$code;
						echo "<br/>";
						echo "返回码描述信息:".$message;
						echo "<br/>";
						echo "签名方式：<font color='red'>";
						echo  $signType."</font>";
						echo "<br/>";
						echo "接口类型：".$type;
						echo "<br/>";
						echo "版本号：".$version;
						echo "<br/>";
						echo "支付金额：".$amount;
						echo "<br/>";
						echo "金额 明细：".$amtItem;
						echo "<br/>";
						echo "支付银行：".$bankAbbr;
						echo "<br/>";
						echo "支付手机号：".$mobile;
						echo "<br/>";
						echo "商户订单号：".$orderId;
						echo "<br/>";
						echo "支付时间：".$payDate;
						echo "<br/>";
						echo "保留字段1：".decodeUtf8($reserved1);
						echo "<br/>";
						echo "保留字段2：".decodeUtf8($reserved2);
						echo "<br/>";
						echo "支付结果：".$status;
						echo "<br/>";
						echo "订单日期：".$orderDate;
						echo "<br/>";
						echo "费用：".$fee;
						echo "<br/>";
						echo "签名数据：".$hmac;
						echo "<br/>";	       
				 }
				
		   }			
		?>

	</body>
</html>
