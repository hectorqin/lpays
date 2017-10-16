<html>
    <head>
        <title>退款</title>
        <link href="css/sdk.css" rel="stylesheet" type="text/css" />
        <meta http-equiv = "Content-Type" content = "text/html; charset=gb2312">
    </head>

    <body>
		<form name="form1" method="post" action="orderrefund.php">
			<br>
			<center>

				<input type="hidden" name="merCert" value="">
				<font size=2 color=black face=Verdana><b>退款</b>
				</font>
				<br>
				<br>
				<table class="api">
					<tr>
						<td class="field">
							退款金额
						</td>
						<td>
							<input type="text" name="amount" maxlength='20' value="10">
							<font color="red">*</font>
						</td>
					</tr>

					<tr>
						<td class="field">
							原交易订单号
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
			<a id="HomeLink" class="home" href="index.php">首页</a>
		</form>
	</body>
</html>
