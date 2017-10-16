<?php
   error_reporting(E_ALL ^ E_NOTICE);
   session_start();
   require("common/globalParam.php");
 
?>
<html>
	<head>
		<title>商户联机交易演示</title>
		<link href="css/sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<center>
			<h4>&nbsp;&nbsp;
				Welcome to the CMPAY <font color="red">PHP</font> SDK Simple Edition Main Page
			</h4>
			<table>
			    <tr>
					<th>
						<p>
							<h4>
								商户[<?php echo $merchantId?>]联机交易演示
							</h4>
						</p>
						<hr>
					</th>
				</tr>
				 <tr>
					<td>
						<a href="dodirectpayment_token_input.php">即时到账(双向确认)</a>
					</td>
				</tr>
		 	  <tr>
					<td>
						<a href="dogwdirectpayment_token_input.php">直接支付(银行网关)</a>
					</td>
				</tr>
		 	  <tr>
					<td>
						<a href="dowapdirectpayment_token_input.php">直接支付WAP(TOKEN)</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="dodirectpayment_sms_input.php">即时到账(短信)</a>
					</td>
				</tr>
				
				<tr>
					<td>
						<a href="orderrefund_input.php">退款</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="ordersearch_input.php">订单查询</a>
					</td>
				</tr>
	            <tr>
			</table>
		</center>
	</body>
</html>