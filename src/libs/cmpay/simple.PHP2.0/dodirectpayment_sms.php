<html>
	<head>
		<title>即时到账(短信)：DirectPayoffline</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php 
		  require("common/callcmpay.php");
		  require("common/globalParam.php");
		  
		  //设置必要的参数 start
		  $reqUrl =  $GLOBALS['reqUrl'];
			$type = "DirectPayOffline";
			$characterSet = $GLOBALS['characterSet'];
      $notifyUrl    = $GLOBALS['notifyUrl'];
        $callbackUrl  = $GLOBALS['callbackUrl'];
      $merchantId = $GLOBALS['merchantId'];
      $requestId  = $GLOBALS['requestId'];
      $signType    = $GLOBALS['signType'];
			$version    = $GLOBALS['version'];				
			$amount = $_POST["amount"];
			$currency    = $_POST["currency"];
			$orderDate   = $_POST["orderDate"];
			$orderId     = $_POST["orderId"];
			$merAcDate   = $_POST["merAcDate"];
			$period      = $_POST["period"];
			$periodUnit  = $_POST["periodUnit"];
			$merchantAbbr = $_POST["merchantAbbr"];
			$productDesc = $_POST["productDesc"];
			$productId   = $_POST["productId"];
			$productName = $_POST["productName"];
			$productNum  = $_POST["productNum"];
			$reserved1   = $_POST["reserved1"];
			$reserved2   = $_POST["reserved2"];
			$userToken   = $_POST["userToken"];
			$showUrl     = $_POST["showUrl"];
			$couponsFlag = $_POST["couponsFlag"];
      //设置必要的参数 end
      
		  //组织签名数据 start 必须按照文档中标明的签名顺序组织
			$signData =  $characterSet.$callbackUrl. $notifyUrl .$merchantId   .$requestId
					         .$signType   . $type       .$version     .$amount
					         .$currency   .$orderDate   .$orderId     .$merAcDate
					         .$period     .$periodUnit  .$merchantAbbr.$productDesc
					         .$productId  .$productName .$productNum  .$reserved1
					         .$reserved2  .$userToken   .$showUrl
					         .$couponsFlag;
			//组织签名数据 end		         
	
	    //商户密钥 
 			$signKey=$GLOBALS['signKey'];
 						
		  //调用签名函数生成签名串                       
      $hmac=MD5sign($signKey,$signData);	
 	
 	    //组织http请求数据   start 
			$requestData = array();			
			$requestData["characterSet"] = $characterSet;
			 $requestData["callbackUrl"]  = $callbackUrl;
      $requestData["notifyUrl"]    = $notifyUrl;
      $requestData["merchantId"]   = $merchantId;
      $requestData["requestId"]    = $requestId;
      $requestData["signType"]     = $signType;
			$requestData["type"] = $type; 
			$requestData["version"]      = $version;
			$requestData["hmac"]         = $hmac;	 
      $requestData["amount"] = $amount;       
      $requestData["currency"] = $currency;      
      $requestData["orderDate"] = $orderDate;     
      $requestData["orderId"] = $orderId; 	
      $requestData["merAcDate"] = $merAcDate;    
      $requestData["period"] = $period; 	      
      $requestData["periodUnit"]  = $periodUnit;    
      $requestData["merchantAbbr"] = $merchantAbbr;
      $requestData["productDesc"] = $productDesc;   
      $requestData["productId"]   = $productId;     
      $requestData["productName"] = $productName;   
      $requestData["productNum"]  = $productNum;    
      $requestData["reserved1"]   = $reserved1;     
      $requestData["reserved2"]   = $reserved2;     
      $requestData["userToken"]   = $userToken;          	      
      $requestData["showUrl"] 	   = $showUrl; 		  
      $requestData["couponsFlag"] = $couponsFlag;
      //组织http请求数据 end  
      
      //发送数据至中心平台
			$sTotalString = POSTDATA($reqUrl,$requestData);
			$recv = $sTotalString["MSG"];
			
			//返回数据转换成数组
			$recvArray = parseRecv($recv);
			
			$code=$recvArray["returnCode"];
			//此处000000 仅代表程序无错误。下单是否成功要根据返回的status来判断
			 if ($code!="000000") {		
			  	echo $code.decodeUtf8($recvArray["message"]);
			 }
			 else
			 {
			 	 //组织签名数据  必须按照文档中返回字段顺序组织
			   $vfsign=$recvArray["merchantId"].$recvArray["payNo"]
					   .$recvArray["returnCode"].$recvArray["message"]
					   .$recvArray["signType"].$recvArray["type"]
				     .$recvArray["version"].$recvArray["amount"]
					   .$recvArray["orderId"].$recvArray["reserved1"]
					   .$recvArray["reserved2"].$recvArray["status"];	
			     
			   $vhmac=$recvArray["hmac"];
			   //商户端签名处理 用来验证数据是否被
				 $hmac=MD5sign($signKey,$vfsign);	
				 if($hmac!=$vhmac)
				 {
				 	 echo "验证签名失败!";
				 }
				 else
				 {
				 	 $status=$recvArray["status"];
				 	 if($status="success")
				 	 {
				 		 echo $status;
				 	 }
				 	 else
				 	 {
				 		 echo $status;
				 	 }
				 }
			 }
		?>
	</body>
</html>
