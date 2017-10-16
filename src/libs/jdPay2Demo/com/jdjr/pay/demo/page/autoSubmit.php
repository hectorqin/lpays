<?php 
error_reporting(0);
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="expires" content="0"/>
<meta http-equiv="pragma" content="no-cache"/>
<meta http-equiv="cache-control" content="no-cache"/>
<title>"京东支付"PC版demo</title>
</head>
<body onload="autosubmit()">
	<form action="<?php echo $_SESSION['payUrl']?>"  method="post" id="batchForm" >
		<input type="text" name="version" value="<?php echo $_SESSION['param']['version']?>"/><br/>
		<input type="text" name="merchant" value="<?php echo $_SESSION['param']['merchant']?>"/><br/>
		<input type="text" name="device" value="<?php echo $_SESSION['param']['device']?>"/><br/>
		<input type="text" name="tradeNum" value="<?php echo $_SESSION['param']['tradeNum']?>"/><br/>
		<input type="text" name="tradeName" value="<?php echo $_SESSION['param']['tradeName']?>"/><br/>
		<input type="text" name="tradeDesc" value="<?php echo $_SESSION['param']['tradeDesc']?>"/><br/>
		<input type="text" name="tradeTime" value="<?php echo $_SESSION['param']['tradeTime']?>"/><br/>
		<input type="text" name="amount" value="<?php echo $_SESSION['param']['amount']?>"/><br/>
		<input type="text" name="currency" value="<?php echo $_SESSION['param']['currency']?>"/><br/>
		<input type="text" name="note" value="<?php echo $_SESSION['param']['note']?>"/><br/>
		<input type="text" name="callbackUrl" value="<?php echo $_SESSION['param']['callbackUrl']?>"/><br/>
		<input type="text" name="notifyUrl" value="<?php echo $_SESSION['param']['notifyUrl']?>"/><br/>
		<input type="text" name="ip" value="<?php echo $_SESSION['param']['ip']?>"/><br/>
		<input type="text" name="userType" value="<?php echo $_SESSION['param']['userType']?>"/><br/>
		<input type="text" name="userId" value="<?php echo $_SESSION['param']['userId']?>"/><br/>
		<input type="text" name="expireTime" value="<?php echo $_SESSION['param']['expireTime']?>"/><br/>
		<input type="text" name="orderType" value="<?php echo $_SESSION['param']['orderType']?>"/><br/>
		<input type="text" name="industryCategoryCode" value="<?php echo $_SESSION['param']['industryCategoryCode']?>"/><br/>
		<input type="text" name="specCardNo" value="<?php echo $_SESSION['param']['specCardNo']?>"/><br/>
		<input type="text" name="specId" value="<?php echo $_SESSION['param']['specId']?>"/><br/>
		<input type="text" name="specName" value="<?php echo $_SESSION['param']['specName']?>"/><br/>
		<input type="text" name="sign" value="<?php echo $_SESSION['param']['sign']?>"/><br/>
	</form>
	<script>
	function autosubmit(){
		document.getElementById("batchForm").submit();
	}	
	</script>

</body>
</html>