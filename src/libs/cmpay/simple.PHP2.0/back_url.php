<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
<title>֪ͨ������ʾ</title>
</head>
<body>
<?php
    //�����̻������빲ͨ�����ļ�
		require("common/globalParam.php"); 
		require("common/callcmpay.php"); 
		
		//�����ֻ�֧��ƽ̨ҳ��֪ͨ���ݵı��� start
    $merchantId 	  = $_POST["merchantId"];
	  $payNo 	  			= $_POST["payNo"];
	  $returnCode 	  = $_POST["returnCode"];
	  $message	  		= $_POST["message"];
	  $signType      	= $_POST["signType"];
	  $type         	= $_POST["type"];
	  $version        	= $_POST["version"];
	  $amount         = $_POST["amount"];
		$amtItem			  = $_POST["amtItem"];		
    $bankAbbr	  		= $_POST["bankAbbr"];
    $mobile 			  = $_POST["mobile"];
    $orderId			  = $_POST["orderId"];
    $payDate			  = $_POST["payDate"];
		$accountDate    = $_POST["accountDate"];
    $reserved1	  	= $_POST["reserved1"];
		$reserved2	  	= $_POST["reserved2"];
    $status				  = $_POST["status"];
		$orderDate      = $_POST["orderDate"];
		$fee            = $_POST["fee"];
    $vhmac			  	= $_POST["hmac"];
    $signKey        = $GLOBALS['signKey'];
		//�����ֻ�֧��ƽ̨ҳ��֪ͨ���ݵı��� end
		
	  //��װǩ�ַ���
		$signData = $merchantId .$payNo.$returnCode .$message
               .$signType   .$type        .$version    .$amount
               .$amtItem    .$bankAbbr    .$mobile     .$orderId
               .$payDate    .$accountDate .$reserved1  .$reserved2
               .$status     .$orderDate   .$fee;
               
		//MD5��ʽǩ��
    $hmac=MD5sign($signKey,$signData);
		
		//�˴�000000����������޴��󡣶����Ƿ�֧���ɹ�����֧�������status��Ϊ׼
		if($returnCode!=000000)
		{
			 echo $returnCode.decodeUtf8($message);
		}
		if($hmac != $vhmac)
			echo "��ǩʧ��";
		else{
			  /*�̻��ڴ˴�ҵ����*/
			  echo "�̻����:".$orderId;
        echo "</br>";
        echo "֧�����:".$amount;
        echo "</br>";
        echo "֧������:".$bankAbbr;
        echo "</br>";
        echo "֧���ˣ�".$mobile;
        echo "</br>";
        echo "֧��ʱ�䣺".$payDate;
        echo "</br>";
        echo "�����ֶ�1��".decodeUtf8($reserved1);
        echo "</br>";
        echo "�����ֶ�2��".decodeUtf8($reserved2);
        echo "</br>";
			  echo "֧�����:".$status;
			  echo "</br>";
		}

?>
</body>
</html>