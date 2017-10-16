<html>
	<head>
		<title>订单查询</title>
		<link href="css/sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<form name="form1" method="post" action="ordersearch.php">
			<br>
			<center>

				<input type="hidden" name="merCert" value="">
				<font size=2 color=black face=Verdana><b>订单查询</b> </font>
				<br>
				<br>
				<table class="api">
					<tr>
						<td class="field">
							订单号
						</td>
						<td>
							<input type="text" name="orderId" maxlength='20' value="">
							<font color="red">*</font>
						</td>
					</tr>
					<tr>
						<td class="field">
						</td>
						<td>

							<input type="Submit" value="提交" id="Submit" name="submit" />
						</td>
					</tr>
				</table>
			</center>
			<a id="HomeLink" class="home" href="index.jsp">首页</a>
		</form>
	</body>
</html>
