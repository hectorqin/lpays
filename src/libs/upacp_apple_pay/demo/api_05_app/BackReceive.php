<?php
include_once $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/sdk/acp_service.php';

?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>银联在线交易测试-结果</title>
<style type="text/css">
body table tr td {
	font-size: 14px;
	word-wrap: break-word;
	word-break: break-all;
	empty-cells: show;
}
</style>
</head>
<body>
	<table width="800px" border="1" align="center">
		<tr>
			<th colspan="2" align="center">银联在线交易测试-交易结果</th>
		</tr>
			<?php
			foreach ( $_POST as $key => $val ) {
				?>
			<tr>
			<td width='30%'><?php echo isset($mpi_arr[$key]) ?$mpi_arr[$key] : $key ;?></td>
			<td><?php echo $val ;?></td>
		</tr>
			<?php }?>
			<tr>
			<td width='30%'>验证签名</td>
			<td><?php			
			if (isset ( $_POST ['signature'] )) {
				
				echo com\unionpay\acp\sdk\AcpService::validate ( $_POST ) ? '验签成功' : '验签失败';
				$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
				$respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功

			} else {
				echo '签名为空';
			}
			?></td>
		</tr>
	</table>
	<?php 
		//如果卡号我们业务配了会返回且配了需要加密的话，请按此方法解密
// 		if(array_key_exists ("accNo", $_POST)){
// 			$accNo = com\unionpay\acp\sdk\AcpService::decryptData($_POST["accNo"]);
// 			echo  "accNo=" . $accNo . "<br>\n";
// 		}
	?>
</body>
</html>