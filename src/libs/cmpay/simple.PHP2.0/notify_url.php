<?php
    //�̻���̨֪ͨ��ʾ
		require("common/globalParam.php"); 
		require("common/callcmpay.php"); 
		
		//�����ֻ�֧��ƽ̨��̨֪ͨ����start
    $merchantId 	  			= $_POST["merchantId"];
	  $payNo 	  			= $_POST["payNo"];
	  $returnCode 	  = $_POST["returnCode"];
	  $message	  		= $_POST["message"];
	  $signType       	= $_POST["signType"];
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
		$payType        = $_POST["payType"];
		$orderDate      = $_POST["orderDate"];
		$fee            = $_POST["fee"];
    $vhmac			  	= $_POST["hmac"];
    //�����ֻ�֧��ƽ̨��̨֪ͨ����end
    $signKey        = $GLOBALS['signKey'];
		if($returnCode!=000000)
		{
        //�˴���ʾ��̨֪ͨ��������
			  echo $returnCode.decodeUtf8($message);
				exit();
		}
		
		$signData = $merchantId .$payNo       .$returnCode .$message
               .$signType   .$type        .$version    .$amount
               .$amtItem    .$bankAbbr    .$mobile     .$orderId
               .$payDate    .$accountDate .$reserved1  .$reserved2
               .$status     .$orderDate  .$fee;
		$hmac=MD5sign($signKey,$signData);	
		if($hmac != $vhmac)
		  //�˴��޷���Ϣ���������ֻ�֧��ƽ̨
			echo "��ǩʧ��";
		else{
			//�̻��ڴ˴���ҵ����������ϱ�����ӦSUCCESS
			echo "SUCCESS";
		}

?>