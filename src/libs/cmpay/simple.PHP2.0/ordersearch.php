<html>
	<head>
		<title>API������ѯ</title>
		<link href="sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
		<?php
				require("common/globalParam.php"); 
		 		require("common/callcmpay.php"); 
					
				//���ö�����ѯ�������start
			  $type       = "OrderQuery";
			  $reqUrl     = $GLOBALS['reqUrl'];
			  $orderId    = $_POST["orderId"];
				$merchantId = $GLOBALS['merchantId'];
				$requestId  = $GLOBALS['requestId'];
				$signType   = $GLOBALS['signType'];
				$version    = $GLOBALS['version'];
				//���ö�����ѯ�������end
				
			  //��װǰ���ַ��� ˳�������ӿ��ĵ�˵������һ��
				$signData   = $merchantId.$requestId.$signType.$type.$version.$orderId;
						
				$signKey=$GLOBALS['signKey'];			
				
				//MD5��ʽǩ��
	      $hmac=MD5sign($signKey,$signData);       
				$requestData = array(); 
				$requestData['merchantId'] = $merchantId;
				$requestData['requestId'] = $requestId;
				$requestData['signType'] = $signType;
				$requestData['type'] = $type;
				$requestData['version'] = $version;
				$requestData['orderId'] = $orderId;
				$requestData['hmac'] = $hmac;

        				
				$sTotalString = POSTDATA($reqUrl,$requestData);
				$recv = $sTotalString["MSG"];
				
				//�ֻ�֧��ƽ̨����form��ʽ���ݴ���
				$recvArray = parseRecv($recv);
				$code = $recvArray['returnCode'];
				$message = $recvArray['message'];
				
				if($code != "000000")
				{
				   //�̻��ڴ˴��� ������ѯʧ��ҵ����
					 echo $code.decodeUtf8($message);
				}
				else
				{
					$vhmac = $recvArray['hmac'];
			    $payNo = $recvArray['payNo'];
			    $signType =  $recvArray['signType'];
			    $type = $recvArray['type'];
			    $version =$recvArray['version'];
			    $amount = $recvArray['amount'];
			    $amtItem = $recvArray['amtItem'];
			    $bankAbbr = $recvArray['bankAbbr'];
			    $mobile = $recvArray['mobile'];
			    $orderId = $recvArray['orderId'];
			    $payDate = $recvArray['payDate'];
			    $reserved1 = $recvArray['reserved1'];
			    $reserved2 = $recvArray['reserved2'];
			    $status = $recvArray['status'];
			    $orderDate = $recvArray['orderDate'];
			    $fee = $recvArray['fee'];			
				  $vfsign = $merchantId.$payNo    .$code     .$message
						      .$signType   .$type     .$version  .$amount
						      .$amtItem    .$bankAbbr .$mobile   .$orderId
						      .$payDate    .$reserved1.$reserved2.$status
						      .$orderDate.$fee;   
					$hmac=MD5sign($signKey,$vfsign);  
					if($hmac!=$vhmac)
					{
						//�˴��޷����δ������Ƿ�Ϊ�ֻ�֧��ƽ̨��������
						echo "��֤ǩ��ʧ��!";
					}
					else
					{
						//�̻��ڴ˴���������ѯ�ɹ�ҵ����
						echo "<br/>";
						echo "���׳ɹ�";
						echo "<br/>";
						echo "================";
						echo "�̻����".$merchantId;
						echo "<br/>";
						echo "��ˮ��:".$payNo;
						echo "<br/>";
						echo "������:".$code;
						echo "<br/>";
						echo "������������Ϣ:".$message;
						echo "<br/>";
						echo "ǩ����ʽ��<font color='red'>";
						echo  $signType."</font>";
						echo "<br/>";
						echo "�ӿ����ͣ�".$type;
						echo "<br/>";
						echo "�汾�ţ�".$version;
						echo "<br/>";
						echo "֧����".$amount;
						echo "<br/>";
						echo "��� ��ϸ��".$amtItem;
						echo "<br/>";
						echo "֧�����У�".$bankAbbr;
						echo "<br/>";
						echo "֧���ֻ��ţ�".$mobile;
						echo "<br/>";
						echo "�̻������ţ�".$orderId;
						echo "<br/>";
						echo "֧��ʱ�䣺".$payDate;
						echo "<br/>";
						echo "�����ֶ�1��".decodeUtf8($reserved1);
						echo "<br/>";
						echo "�����ֶ�2��".decodeUtf8($reserved2);
						echo "<br/>";
						echo "֧�������".$status;
						echo "<br/>";
						echo "�������ڣ�".$orderDate;
						echo "<br/>";
						echo "���ã�".$fee;
						echo "<br/>";
						echo "ǩ�����ݣ�".$hmac;
						echo "<br/>";	       
				 }
				
		   }			
		?>

	</body>
</html>
