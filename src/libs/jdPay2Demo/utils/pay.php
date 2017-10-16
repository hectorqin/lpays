<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="expires" content="0"/>
<meta http-equiv="pragma" content="no-cache"/>
<meta http-equiv="cache-control" content="no-cache"/>
<title>京东支付</title>
</head>
<body onload="autosubmit()">
	<form action="<?php echo $oriUrl?>"  method="post" id="batchForm" >
		<input type="hidden" name="version" value="<?php echo $param['version']?>"/><br/>
		<input type="hidden" name="merchant" value="<?php echo $param['merchant']?>"/><br/>
		<input type="hidden" name="device" value="<?php echo $param['device']?>"/><br/>
		<input type="hidden" name="tradeNum" value="<?php echo $param['tradeNum']?>"/><br/>
		<input type="hidden" name="tradeName" value="<?php echo $param['tradeName']?>"/><br/>
		<input type="hidden" name="tradeDesc" value="<?php echo $param['tradeDesc']?>"/><br/>
		<input type="hidden" name="tradeTime" value="<?php echo $param['tradeTime']?>"/><br/>
		<input type="hidden" name="amount" value="<?php echo $param['amount']?>"/><br/>
		<input type="hidden" name="currency" value="<?php echo $param['currency']?>"/><br/>
		<input type="hidden" name="note" value="<?php echo $param['note']?>"/><br/>
		<input type="hidden" name="callbackUrl" value="<?php echo $param['callbackUrl']?>"/><br/>
		<input type="hidden" name="notifyUrl" value="<?php echo $param['notifyUrl']?>"/><br/>
		<input type="hidden" name="ip" value="<?php echo $param['ip']?>"/><br/>
		<input type="hidden" name="userType" value="<?php echo $param['userType']?>"/><br/>
		<input type="hidden" name="userId" value="<?php echo $param['userId']?>"/><br/>
		<input type="hidden" name="expireTime" value="<?php echo $param['expireTime']?>"/><br/>
		<input type="hidden" name="orderType" value="<?php echo $param['orderType']?>"/><br/>
		<input type="hidden" name="industryCategoryCode" value="<?php echo $param['industryCategoryCode']?>"/><br/>
		<input type="hidden" name="specCardNo" value="<?php echo $param['specCardNo']?>"/><br/>
		<input type="hidden" name="specId" value="<?php echo $param['specId']?>"/><br/>
		<input type="hidden" name="specName" value="<?php echo $param['specName']?>"/><br/>
		<input type="hidden" name="sign" value="<?php echo $param['sign']?>"/><br/>
	</form>
	<script>
	function autosubmit(){
		document.getElementById("batchForm").submit();
	}	
	</script>
</body>
</html>