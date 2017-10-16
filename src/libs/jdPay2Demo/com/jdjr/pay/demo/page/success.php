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

</head>
<body>

	<div class="content">
		<div class="content_0" align="center">
			<br /> <br /> <br />
			<lable> 交易流水号：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['tradeNum']?></lable>
			<br />
			<lable> 金额：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['amount']?>分</lable>
			<br />
			<lable> 币种：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['currency']?></lable>
			<br />
			<lable> 交易时间：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['tradeTime']?></lable>
			<br />
			<lable> 交易备注：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['note']?></lable>
			<br />
			<lable> 状态：</lable>
			<lable><?php echo $_SESSION['tradeResultRes']['status']?></lable>
		</div>
	</div>

</body>
</html>