<?php
header ( 'Content-type:text/html;charset=utf-8' );
include_once $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/sdk/acp_service.php';

/**
 * 重要：联调测试时请仔细阅读注释！
 *
 * 产品：跳转网关支付产品<br>
 * 交易：文件传输类接口：后台获取对账文件交易，只有同步应答 <br>
 * 日期： 2015-09<br>
 * 版本： 1.0.0
 * 版权： 中国银联<br>
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考，不提供编码性能规范性等方面的保障<br>
 * 该接口参考文档位置：open.unionpay.com帮助中心 下载 产品接口规范 《网关支付产品接口规范》<br>
 * 《平台接入接口规范-第5部分-附录》（内包含应答码接口规范，全渠道平台银行名称-简码对照表）<br>
 * 《全渠道平台接入接口规范 第3部分 文件接口》（对账文件格式说明）<br>
 * 测试过程中的如果遇到疑问或问题您可以：1）优先在open平台中查找答案：
 * 调试过程中的问题或其他问题请在 https://open.unionpay.com/ajweb/help/faq/list 帮助中心 FAQ 搜索解决方案
 * 测试过程中产生的6位应答码问题疑问请在https://open.unionpay.com/ajweb/help/respCode/respCodeList 输入应答码搜索解决方案
 * 2） 咨询在线人工支持： open.unionpay.com注册一个用户并登陆在右上角点击“在线客服”，咨询人工QQ测试支持。
 * 交易说明： 对账文件的格式请参考《全渠道平台接入接口规范 第3部分 文件接口》
 * 对账文件示例见目录file下的802310048993424_20150905.zip
 * 解析落地后的对账文件可以参考BaseDemo.java中的parseZMFile();parseZMEFile();方法
 */

$params = array (

		// 以下信息非特殊情况不需要改动
		'version' => '5.0.0', // 版本号
		'encoding' => 'utf-8', // 编码方式
		'txnType' => '76', // 交易类型
		'signMethod' => '01', // 签名方法
		'txnSubType' => '01', // 交易子类
		'bizType' => '000000', // 业务类型
		'accessType' => '0', // 接入类型
		'fileType' => '00', // 文件类型

		// TODO 以下信息需要填写
		'txnTime' => $_POST ["txnTime"], // 订单发送时间，取北京时间，格式为YYYYMMDDhhmmss，此处默认取demo演示页面传递的参数
		'merId' => $_POST ["merId"], // 商户代码，请替换实际商户号测试，如使用的是自助化平台注册的商户号（777开头的），该商户号没有权限测文件下载接口的，请使用测试参数里写的文件下载的商户号和日期测，如需真实交易文件，请使用自助化平台下载文件，此处默认取demo演示页面传递的参数
		'settleDate' => $_POST ["settleDate"]
) // 清算日期，格式为MMDD，此处默认取demo演示页面传递的参数
;

com\unionpay\acp\sdk\AcpService::sign ( $params );
$url = com\unionpay\acp\sdk\SDK_FILE_QUERY_URL;

$result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url);
if(count($result_arr)<=0) { //没收到200应答的情况
	printResult ( $url, $params, "" );
	return;
}

printResult ($url, $params, $result_arr ); //页面打印请求应答数据

if (!com\unionpay\acp\sdk\AcpService::validate ($result_arr) ){
	echo "应答报文验签失败<br>\n";
	return;
}

echo "应答报文验签成功<br>\n";
if ($result_arr["respCode"] == "98"){
	//文件不存在
	//TODO
	echo "文件不存在。<br>\n";
	return;
} else if ($result_arr["respCode"] != "00") {
	//其他应答码做以失败处理
	//TODO
	echo "失败：respCode=" . $result_arr["respCode"] . "。<br>\n";
	return;
}

echo "返回成功。<br>\n";

//TODO 处理文件，保存路径在配置文件中修改，注意预先建立文件夹并授读写权限
if ( com\unionpay\acp\sdk\AcpService::deCodeFileContent( $result_arr, com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH ) == false) {
	echo '文件保存失败，请查看日志提示的错误信息<br\n>';
	return;
}

echo '文件已成功保存到' . com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH . "目录下<br\n>";

//=================================================================
//TODO 下面是调用的方法是分析对账文件的样例代码，请按照自己的需要修改并集成到自己的代码中
analyze_file($result_arr ["fileName"]);

function analyze_file($fileName){
	//解压
	$zip = new ZipArchive ();
	if ($zip->open ( com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH . "/" . $fileName ) === TRUE) {
		$zip->extractTo ( com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH );
		$zip->close ();

		//遍历解压之后目录下的所有文件，对流水文件分析
		foreach (scandir(com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH) as $fileName) {
			$list = null;
			if (substr($fileName, 0, 3) == "INN" && substr($fileName, 11, 3) == "ZM_")
				$list = parse_file_zm ( com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH."/".$fileName ); //处理流水文件
			else if (substr($fileName, 0, 3) == "INN" && substr($fileName, 11, 4) == "ZME_")
				$list = parse_file_zme ( com\unionpay\acp\sdk\SDK_FILE_DOWN_PATH."/".$fileName ); //处理差错流水文件
				
			if ($list != null) {
				echo ($fileName . "部分参数读取（读取方式请参考Form_6_6_FileTransfer的代码）:<br>\n");
				echo ("<table border='1'>\n");
				echo ("<tr><th>txnType</th><th>orderId</th><th>txnTime（MMDDhhmmss）</th></tr>");
				foreach ($list as $dic) {
					//TODO 参看https://open.unionpay.com/ajweb/help?id=258，根据编号获取即可，例如订单号12、交易类型20。
					//具体写代码时可能边读文件边修改数据库性能会更好，请注意自行根据parseFile中的读取方法修改。
					echo("<tr>\n");
					echo("<td>" . $dic[20] . "</td>\n");//txnType
					echo("<td>" . $dic[12] . "</td>\n");//orderId
					echo("<td>" . $dic[5] . "</td>\n");//txnTime不带年份
					echo("</tr>\n");
				}
				echo ("</table>\n");
			}
		}
	} else {
		echo '解压失败';
	}
}

//全渠道商户一般交易明细流水文件
function parse_file_zm($filePath){
	$lengthArray = array(3, 11, 11, 6, 10, 19, 12, 4, 2, 21, 2, 32, 2, 6, 10, 13, 13, 4, 15, 2, 2, 6, 2, 4, 32, 1, 21, 15, 1, 15, 32, 13, 13, 8, 32, 13, 13, 12, 2, 1, 131 );
	return parse_file($filePath, $lengthArray);
}

//全渠道商户差错交易明细流水文件
function parse_file_zme($filePath){
	$lengthArray = array(3, 11, 11, 6, 10, 19, 12, 4, 2, 2, 6, 10, 4, 12, 13, 13, 15, 15, 1, 12, 2, 135);
	return parse_file($filePath, $lengthArray);
}

function parse_file($filePath, $lengthArray) {
	if (! file_exists ( $filePath ))
		return false;

	// 解析的结果MAP，key为对账文件列序号，value为解析的值
	$dataList = array ();
	$s = "";
	foreach ( file ( $filePath ) as $s ) {
		$dataMap = array ();
		$leftIndex = 0;
		$rightIndex = 0;
		for($i = 0; $i < count ( $lengthArray ); $i ++) {
			$rightIndex = $leftIndex + $lengthArray [$i];
			$filed = substr ( $s, $leftIndex, $lengthArray [$i] );
			$leftIndex = $rightIndex + 1;
			$dataMap [$i + 1] = $filed;
		}
		$dataList [] = $dataMap;
	}
	return $dataList;
}

/**
 * 打印请求应答
 *
 * @param
 *        	$url
 * @param
 *        	$req
 * @param
 *        	$resp
 */
function printResult($url, $req, $resp) {
	echo "=============<br>\n";
	echo "地址：" . $url . "<br>\n";
	echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $req, false, true ) ) ) . "<br>\n";
	echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $resp , false, true )) ) . "<br>\n";
	echo "=============<br>\n";
}