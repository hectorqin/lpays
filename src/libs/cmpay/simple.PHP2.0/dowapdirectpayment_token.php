<html>
	<head>
		<title>直接支付WAP(TOKEN)：WAPDirectPayConfirm</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
		    require("common/globalParam.php"); 
		    require("common/callcmpay.php"); 	
		    //设置请求参数start
			  $type         = "WAPDirectPayConfirm";	
			  $reqUrl       =  $GLOBALS['reqUrl'];
			  $ipAddress    = getClientIP();
			  $characterSet = $GLOBALS['characterSet'];
        $callbackUrl  = $GLOBALS['callbackUrl'];
        $notifyUrl    = $GLOBALS['notifyUrl'];
        $merchantId   = $GLOBALS['merchantId'];
        $requestId    = $GLOBALS['requestId'];
        $signType     = $GLOBALS['signType'];
			  $version      = $GLOBALS['version'];		
		    $amount 	    = $_POST['amount'];
   		  $bankAbbr     = $_POST['bankAbbr'];
		    $currency     = $_POST['currency'];
		    $orderDate    = $_POST['orderDate'];
		    $orderId 	    = $_POST['orderId'];
		    $merAcDate    = $_POST['merAcDate'];
		    $period 	    = $_POST['period'];
	      $periodUnit   = $_POST['periodUnit'];
	      $merchantAbbr = $_POST['merchantAbbr'];
	      $productDesc  = $_POST['productDesc'];
		    $productId    = $_POST['productId'];
		    $productName  = $_POST['productName'];
		    $productNum   = $_POST['productNum'];
		    $reserved1    = $_POST['reserved1'];
	      $reserved2    = $_POST['reserved2'];
	      $userToken    = $_POST['userToken'];
		    $showUrl 	  	= $_POST['showUrl'];
		    $couponsFlag  = $_POST['couponsFlag'];  
		    //设置请求参数 end
		    
		  	//组织签名数据	
			  $signData = $characterSet.$callbackUrl.$notifyUrl.$ipAddress
					         .$merchantId  .$requestId  .$signType .$type
					         .$version     .$amount     .$bankAbbr .$currency
					         .$orderDate   .$orderId    .$merAcDate .$period   .$periodUnit
					         .$merchantAbbr.$productDesc.$productId.$productName
					         .$productNum  .$reserved1  .$reserved2.$userToken
					         .$showUrl     .$couponsFlag;			
					
			  $signKey=$GLOBALS['signKey'];		
			  //MD5方式签名				
			  $hmac=MD5sign($signKey,$signData);
			
			  $requestData = array();
			  $requestData["characterSet"] = $characterSet;
        $requestData["callbackUrl"]  = $callbackUrl;
        $requestData["notifyUrl"]    = $notifyUrl;
        $requestData["ipAddress"]    = $ipAddress;
        $requestData["merchantId"]   = $merchantId;
        $requestData["requestId"]    = $requestId;
        $requestData["signType"]     = $signType;
			  $requestData["type"]         = $type; 
			  $requestData["version"]      = $version;
			  $requestData["hmac"]         = $hmac;	 
        $requestData["amount"]       = $amount; 	      
        $requestData["bankAbbr"]     = $bankAbbr;      
        $requestData["currency"]     = $currency;      
        $requestData["orderDate"]    = $orderDate;     
        $requestData["orderId"]      = $orderId; 	 
        $requestData["merAcDate"]    = $merAcDate;   
        $requestData["period"]       = $period; 	      
        $requestData["periodUnit"]   = $periodUnit; 
        $requestData["merchantAbbr"] = $merchantAbbr;   
        $requestData["productDesc"]  = $productDesc;   
        $requestData["productId"]    = $productId;     
        $requestData["productName"]  = $productName;   
        $requestData["productNum"]   = $productNum;    
        $requestData["reserved1"]    = $reserved1;     
        $requestData["reserved2"]    = $reserved2;     
        $requestData["userToken"]    = $userToken;         	      
        $requestData["showUrl"] 	   = $showUrl; 		  
        $requestData["couponsFlag"]  = $couponsFlag;

				//http请求到手机支付平台
			  $sTotalString = POSTDATA($reqUrl,$requestData);
			  $recv = $sTotalString["MSG"];
			  $recvArray = parseRecv($recv);

        $code=$recvArray["returnCode"];
        $payUrl;
			  if ($code!="000000") {
			  	 echo "code:".$code."</br>msg:".decodeUtf8($recvArray["message"]);
			 	   exit();
			  }
			  else
			  {
			  	$vfsign=$recvArray["merchantId"].$recvArray["requestId"]
					     .$recvArray["signType"]  .$recvArray["type"]
					     .$recvArray["version"]   .$recvArray["returnCode"]
					     .$recvArray["message"]   .$recvArray["payUrl"];
					$hmac=MD5sign($signKey,$vfsign);
				  $vhmac=$recvArray["hmac"];   
				  if($hmac!=$vhmac)
				  {
					 echo "验证签名失败!";
					 exit();
				  }
				  else
				  {
				  	$payUrl = $recvArray["payUrl"];
			      //返回url处理
			      $rpayUrl= parseUrl($payUrl);
				  }     
			  }			  	       			   			  
		?>
		<form action="<?php echo $rpayUrl["url"]?>" method="<?php echo $rpayUrl["method"];?>">
			<input type="submit" value="提交"/>
		</form>
	</body>
</html>
