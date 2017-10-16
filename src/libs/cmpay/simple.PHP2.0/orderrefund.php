<html>
	<head>
		<title>API�˿����</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
			require("common/globalParam.php"); 
		 	require("common/callcmpay.php"); 	
		  
		  //�����˿�������� start
			$type       = "OrderRefund";
			$reqUrl     = $GLOBALS['reqUrl'];
			$amount     =  $_POST['amount'];
			$orderId    = $_POST['orderId'];             
			$merchantId = $GLOBALS['merchantId'];
			$requestId  = $GLOBALS['requestId'];
			$signType   = $GLOBALS['signType'];
			$version    = $GLOBALS['version'];
			//�����˿�������� start
			
			//��֯��ǩ����			
			$signData = $merchantId.$requestId.$signType.$type.$version.$orderId.$amount;

			$signKey=$GLOBALS['signKey'];
			//MD5��ʽǩ��
			$hmac=MD5sign($signKey,$signData);
			  				 
			$requestData = array(); 			
			$requestData['merchantId']=$merchantId;
			$requestData['requestId']=$requestId;
			$requestData['type']=$type;
			$requestData['version']=$version;
			$requestData['orderId']=$orderId;
			$requestData['amount']=$amount;
			$requestData['hmac'] = $hmac;
			$requestData['signType']=$signType;
							            
			$sTotalString = POSTDATA($reqUrl,$requestData);
			//������ѯ�ӿڷ��� form��ʽ���ݴ���
			$recv = $sTotalString["MSG"];
			$recvArray = parseRecv($recv);
			
			$code = $recvArray['returnCode'];
			$message =$recvArray['message'];
			if ("000000" != $code) {
				  //�̻��ڴ˴����˿�ʧ��ҵ����
					echo "�˿�ʧ��".$code.decodeUtf8($message);
			}
			else
			{
				$vfsign=$recvArray['merchantId'].$recvArray['payNo']
					   .$recvArray['returnCode'].decodeUtf8($recvArray['message'])
					   .$recvArray['signType'].$recvArray['type'].$recvArray['version']
					   .$recvArray['amount'].$recvArray['orderId']
					   .$recvArray['status'];		
			  $vhmac = $recvArray['hmac'];
			  $hmac=MD5sign($signKey,$vfsign);
			  if($hmac!=$vhmac)
			  {
			    //�˴��̻��޷���������Ϊ�ֻ�֧��ƽ̨����ԭʼ����
			  	echo "�̻��ˣ���֤ǩ��ʧ��!";
			  	exit();
			  }
			  else
			  {
			  	//�̻��˴����˿�ɹ�����ҵ����
			  	echo "================";
			  	echo "</br>";
			  	echo "���׳ɹ�";
			  	echo "</br>";
			  	echo "================";
			  	echo "</br>";
			  	echo "�˿���:".$recvArray['amount'];
			  	echo "</br>";
			  	echo "�̻�������:".$recvArray['orderId'];
			  	echo "</br>";
			  	echo "�˿���:".$recvArray['status'];
			  	echo "</br>";
			  	
			  }				
			}
		?>

	</body>
</html>
