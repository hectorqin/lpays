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
		<lable>版本号：</lable>
		<lable><?php echo  $_SESSION['refund']['version']?></lable>
		<br />
		<lable>商户号：</lable>
		<lable><?php echo  $_SESSION['refund']['merchant']?></lable>
		<br />
		<lable>交易流水：</lable>
		<lable><?php echo  $_SESSION['refund']['tradeNum']?></lable>
		<br />
		<lable> 交易类型:</lable>
		<lable> 
		<?php 
		$tradeType = $_SESSION['refund']['tradeType'];
		if($tradeType=="0"){
			echo "消费";
		}
		if($tradeType=="1"){
			echo "退款";
		}
		
		?></lable>
		<br />
		<lable> 交易金额:</lable>
		<lable> <?php echo  $_SESSION['refund']['amount']?>分</lable>
		<br />
		<lable> 交易单位: </lable>
		<lable> <?php echo  $_SESSION['refund']['currency']?></lable>
		<br />
		<lable> 交易时间:</lable>
		<lable> <?php echo  $_SESSION['refund']['tradeTime']?></lable>
		<br />
		<lable> 交易状态：</lable>
		<lable>
		<?php 
			$status = $_SESSION['refund']['status'];
			if($status=="0"){
				echo "处理中";
			}
			if($status=="1"){
				echo "成功";
			}
			if($status=="2"){
				echo "失败";
			}
		?>
		</lable>
		<br />
		<lable> 交易返回码： </lable>
		<lable> <?php echo  $_SESSION['refund']['result']['code']?></lable>
		<br />
		<lable> 交易返回描述： </lable>
		<lable> <?php echo  $_SESSION['refund']['result']['desc']?> </lable>
		<br />
		
		
	</div>
	<textarea rows="20" cols="100" ><?php echo $_SESSION['jsonStr']?></textarea>
</body>
</html>
