<?php 
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>京东支付</title>
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
</head>
<body>

	<div class="content" align="center">

		<br />
		<lable> 交易号: </lable>
		<lable><?php echo  $_SESSION['refund']['tradeNum']?> </lable>
		<br />
		<lable> 原交易号: </lable>
		<lable><?php echo $_SESSION['refund']['oTradeNum']?></lable>
		<br />
		<lable> 交易币种:</lable>
		<lable><?php echo $_SESSION['refund']['currency']?> </lable>
		<br />
		<lable> 交易日期:</lable>
		<lable><?php echo $_SESSION['refund']['tradeTime']?></lable>
		<br />
		<lable> 交易金额:</lable>
		<lable> <?php echo $_SESSION['refund']['amount']?> 分</lable>
		<br />
		<lable> 交易备注: </lable>
		<lable> <?php echo $_SESSION['refund']['note']?></lable>
		<br />
		<lable> 交易状态: </lable>
		<lable><?php echo $_SESSION['refund']['status']?></lable>
		<br />
		<lable> 交易返回码：</lable>
		<lable><?php echo $_SESSION['refund']['result']['code']?></lable>
		<br />
		<lable> 交易返回描述：</lable>
		<lable><?php echo $_SESSION['refund']['result']['desc']?></lable>
		<br />
	</div>

</body>
</html>