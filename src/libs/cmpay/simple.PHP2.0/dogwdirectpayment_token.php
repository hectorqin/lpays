
<html>
	<head>
		<title>直接支付(银行网关)：GWDirectPay</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
		  //包含商户配置文件 于 共通函数
		  require("common/globalParam.php"); 
		  require("common/callcmpay.php"); 	
		
		  //设置商户请求报文参数 start
		  $type="GWDirectPay"; 
		  $reqUrl = $GLOBALS['reqUrl'];
		  $characterSet = $GLOBALS['characterSet'];
      $callbackUrl  = $GLOBALS['callbackUrl'];
      $notifyUrl    = $GLOBALS['notifyUrl'];
      $ipAddress    = getClientIP();
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
		  $showUrl 		  = $_POST['showUrl'];
		  $couponsFlag  = $_POST['couponsFlag']; 		   
		  //设置商户请求报文参数 end
		  
		    
			//签名字段	
			$signData = $characterSet.$callbackUrl  . $notifyUrl   . $ipAddress 
			          . $merchantId  . $requestId   . $signType    . $type
					      . $version     . $amount      . $bankAbbr    . $currency
					      . $orderDate   . $orderId     . $merAcDate   . $period 
					      . $periodUnit  . $merchantAbbr. $productDesc . $productId
					      . $productName . $productNum  . $reserved1   . $reserved2
					      . $userToken   . $showUrl     . $couponsFlag;			

			$signKey=$GLOBALS['signKey'];				
			//MD5方式签名
			$hmac=MD5sign($signKey,$signData);			
		?>
		<!--form 方式请求到手机支付平台-->
		<form method="post" action="<?php echo $reqUrl ?>">
			<input type="hidden" name="characterSet" value="<?php echo $characterSet ?>" />
			<input type="hidden" name="callbackUrl" value="<?php echo $callbackUrl ?>" />
			<input type="hidden" name="notifyUrl" value="<?php echo $notifyUrl ?>" />
			<input type="hidden" name="ipAddress" value="<?php echo $ipAddress ?>" />
			<input type="hidden" name="merchantId" value="<?php echo $merchantId ?>" />
			<input type="hidden" name="requestId" value="<?php echo $requestId ?>" />
			<input type="hidden" name="signType" value="<?php echo $signType ?>" />
			<input type="hidden" name="type" value="<?php echo $type ?>" />
			<input type="hidden" name="version" value="<?php echo $version ?>" />
			<input type="hidden" name="hmac" value="<?php echo $hmac ?>" />
			<input type="hidden" name="amount" value="<?php echo $amount ?>" />
			<input type="hidden" name="bankAbbr" value="<?php echo $bankAbbr ?>" />
			<input type="hidden" name="currency" value="<?php echo $currency ?>" />
			<input type="hidden" name="orderDate" value="<?php echo $orderDate ?>" />
			<input type="hidden" name="orderId" value="<?php echo $orderId ?>" />
			<input type="hidden" name="merAcDate" value="<?php echo $merAcDate ?>" />
			<input type="hidden" name="period" value="<?php echo $period ?>" />
			<input type="hidden" name="periodUnit" value="<?php echo $periodUnit ?>" />
			<input type="hidden" name="merchantAbbr" value="<?php echo $merchantAbbr ?>" />
			<input type="hidden" name="productDesc" value="<?php echo $productDesc ?>" />
			<input type="hidden" name="productId" value="<?php echo $productId ?>" />
			<input type="hidden" name="productName" value="<?php echo $productName ?>" />
			<input type="hidden" name="productNum" value="<?php echo $productNum ?>" />
			<input type="hidden" name="reserved1" value="<?php echo $reserved1 ?>" />
			<input type="hidden" name="reserved2" value="<?php echo $reserved2 ?>" />
			<input type="hidden" name="userToken" value="<?php echo $userToken ?>" />
			<input type="hidden" name="showUrl" value="<?php echo $showUrl ?>" />
			<input type="hidden" name="couponsFlag" value="<?php echo $couponsFlag ?>" />
			<input type="submit" value="确认" />
		</form>

	</body>
</html>
