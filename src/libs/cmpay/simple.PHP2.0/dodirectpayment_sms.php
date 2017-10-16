<html>
	<head>
		<title>��ʱ����(����)��DirectPayoffline</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php 
		  require("common/callcmpay.php");
		  require("common/globalParam.php");
		  
		  //���ñ�Ҫ�Ĳ��� start
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
      //���ñ�Ҫ�Ĳ��� end
      
		  //��֯ǩ������ start ���밴���ĵ��б�����ǩ��˳����֯
			$signData =  $characterSet.$callbackUrl. $notifyUrl .$merchantId   .$requestId
					         .$signType   . $type       .$version     .$amount
					         .$currency   .$orderDate   .$orderId     .$merAcDate
					         .$period     .$periodUnit  .$merchantAbbr.$productDesc
					         .$productId  .$productName .$productNum  .$reserved1
					         .$reserved2  .$userToken   .$showUrl
					         .$couponsFlag;
			//��֯ǩ������ end		         
	
	    //�̻���Կ 
 			$signKey=$GLOBALS['signKey'];
 						
		  //����ǩ����������ǩ����                       
      $hmac=MD5sign($signKey,$signData);	
 	
 	    //��֯http��������   start 
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
      //��֯http�������� end  
      
      //��������������ƽ̨
			$sTotalString = POSTDATA($reqUrl,$requestData);
			$recv = $sTotalString["MSG"];
			
			//��������ת��������
			$recvArray = parseRecv($recv);
			
			$code=$recvArray["returnCode"];
			//�˴�000000 ����������޴����µ��Ƿ�ɹ�Ҫ���ݷ��ص�status���ж�
			 if ($code!="000000") {		
			  	echo $code.decodeUtf8($recvArray["message"]);
			 }
			 else
			 {
			 	 //��֯ǩ������  ���밴���ĵ��з����ֶ�˳����֯
			   $vfsign=$recvArray["merchantId"].$recvArray["payNo"]
					   .$recvArray["returnCode"].$recvArray["message"]
					   .$recvArray["signType"].$recvArray["type"]
				     .$recvArray["version"].$recvArray["amount"]
					   .$recvArray["orderId"].$recvArray["reserved1"]
					   .$recvArray["reserved2"].$recvArray["status"];	
			     
			   $vhmac=$recvArray["hmac"];
			   //�̻���ǩ������ ������֤�����Ƿ�
				 $hmac=MD5sign($signKey,$vfsign);	
				 if($hmac!=$vhmac)
				 {
				 	 echo "��֤ǩ��ʧ��!";
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
