<?php
   error_reporting(E_ALL ^ E_NOTICE);
   session_start();
   require("common/globalParam.php");
 
?>
<html>
	<head>
		<title>�̻�����������ʾ</title>
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
								�̻�[<?php echo $merchantId?>]����������ʾ
							</h4>
						</p>
						<hr>
					</th>
				</tr>
				 <tr>
					<td>
						<a href="dodirectpayment_token_input.php">��ʱ����(˫��ȷ��)</a>
					</td>
				</tr>
		 	  <tr>
					<td>
						<a href="dogwdirectpayment_token_input.php">ֱ��֧��(��������)</a>
					</td>
				</tr>
		 	  <tr>
					<td>
						<a href="dowapdirectpayment_token_input.php">ֱ��֧��WAP(TOKEN)</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="dodirectpayment_sms_input.php">��ʱ����(����)</a>
					</td>
				</tr>
				
				<tr>
					<td>
						<a href="orderrefund_input.php">�˿�</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="ordersearch_input.php">������ѯ</a>
					</td>
				</tr>
	            <tr>
			</table>
		</center>
	</body>
</html>