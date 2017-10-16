<?php 
use com\jdjr\pay\demo\common\ConfigUtil;
include '../common/ConfigUtil.php';

error_reporting(0);

date_default_timezone_set("PRC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
<title>"京东支付"PC版demo</title>

</head>
<body>
	<form action="../action/ClientOrder.php" method="post" >
		<div class="content">
			<div class="content_0">
				<lable>version:</lable>
				<input type="txt" name="version" value="V2.0"> <br />
				<lable>merchant:</lable>
				<input type="txt" name="merchant" value="<?php echo ConfigUtil::get_val_by_key('merchantNum');?>"> <br />
				<lable>device:</lable>
				<input type="txt" name="device" value="111"> <br />
				<lable>tradeNum:</lable>
				<input type="txt" name="tradeNum"
					value="<?php echo time()?>"> <br />
				<lable>tradeName:</lable>
				<input type="txt" name="tradeName" value="商品1111"> <br />
				<lable>tradeDesc:</lable>
				<input type="txt" name="tradeDesc" value="交易描述"> <br />
				<lable>tradeTime:</lable>
				<input type="txt" name="tradeTime" value="<?php echo  date('YmdHis')?>"> <br />
				<lable>amount:</lable>
				<input type="txt" name="amount" value="1"> <br />
				<lable>currency:</lable>
				<input type="txt" name="currency" value="CNY"> <br />
				<lable>note:</lable>
				<input type="txt" name="note" value="备注"> <br />
				<lable>callbackUrl:</lable>
				<input type="txt" name="callbackUrl"
					value="<?php echo ConfigUtil::get_val_by_key('callbackUrl');?>"> <br />
				<lable>notifyUrl:</lable>
				<input type="txt" name="notifyUrl"
					value="<?php echo ConfigUtil::get_val_by_key('notifyUrl');?>"> <br />
				<lable>ip:</lable>
				<input type="txt" name="ip" value="10.45.251.153"> <br />
				<lable>userType:</lable>
				<input type="txt" name="userType" value=""> <br />
				<lable>userId:</lable>
				<input type="txt" name="userId" value=""> <br />
				<lable>expireTime:</lable>
				<input type="txt" name="expireTime" value=""> <br />
				<lable>industryCategoryCode:</lable>
				<input type="txt" name="industryCategoryCode" value=""> <br />
				<lable>orderType:</lable>
				<input type="txt" name="orderType" value="1"> <br />
				<lable>specCardNo:</lable>
				<input type="txt" name="specCardNo" value=""> <br />
				<lable>specId:</lable>
				<input type="txt" name="specId" value=""> <br />
				<lable>specName:</lable>
				<input type="txt" name="specName" value=""> <br />
				<lable>saveUrl:</lable>
				<input type="txt" name="saveUrl"
					value="<?php echo ConfigUtil::get_val_by_key('serverPayUrl');?>"> <br /> 
					<input type="submit" value="京东支付" id="showlayerButton" class="btn">
				</li>
			</div>
		</div>
	</form>

</body>
</html>
