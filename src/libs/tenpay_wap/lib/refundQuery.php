<?php
//---------------------------------------------------------
//�Ƹ�ͨ��ʱ����֧������ʾ�����̻����մ��ĵ����п�������
//---------------------------------------------------------

require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
/* �̻��� */
$partner = "1900000109";

/* ��Կ */
$key = "8934e7d15453e97507ef794cf7b0519d";


/* ����֧��������� */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/normalrefundquery.xml");


$httpClient = new TenpayHttpClient();
//Ӧ�����
$resHandler = new ClientResponseHandler();
//----------------------------------------
//����֧������ 
//----------------------------------------
//$reqHandler->setParameter("ver", "2.0");  //�汾��
$reqHandler->setParameter("partner", $partner);  //�̻���

//transaction_id��sp_billno������һ��������������transaction_idΪ׼
$reqHandler->setParameter("out_trade_no", "1711217684");  //�̻�������
//$reqHandler->setParameter("sp_billno", "201010200898845817");  //�̻��ڲ������Ķ�����
//$reqHandler->setParameter("attach", iconv('UTF-8','GBK',"����"));  //�̻�������Ϣ��������������ַ�����Ҫת��ΪGBK
//$reqHandler->setParameter("charset", 1);  

//�������󷵻صĵȴ�ʱ��
   $httpClient->setTimeOut(5);	
    
    //���÷�������POST
   $httpClient->setMethod("POST");

$httpClient->setReqContent($reqHandler->getRequestURL());

//��̨����
if($httpClient->call()) {
	$resHandler->setContent($httpClient->getResContent());
	$resHandler->setKey($key);

	if ($resHandler->isTenpaySign() && $resHandler->getParameter("retcode") == 0 ) {
		
		$refund_count = $resHandler->getParameter("refund_count");
		echo "�˿����: ".$refund_count." <br/>";
		for ($i=0; $i<$refund_count; $i++){
			$refund_state_n = "refund_state_".$i;
		    $out_refund_no_n = "out_refund_no_".$i;
		    $refund_fee_n = "refund_fee_".$i;
			
			echo "��".$i."�ʣ�".$refund_state_n."=".$resHandler->getParameter($refund_state_n).",".$out_refund_no_n."=".$resHandler->getParameter($out_refund_no_n).",".$refund_fee_n."=".$resHandler->getParameter($refund_fee_n)." <br/>";
		}		
	} else {
		//����ʱ�����ؽ������û��ǩ������¼retcode��retmsg��ʧ�����顣
		echo "��֤ǩ��ʧ�� �� ҵ�������Ϣ:retcode=" . $resHandler->getParameter("retcode"). ",retmsg=" . $resHandler->getParameter("retmsg") . "<br>";
	}
	
} else {
	//��̨����ͨ��ʧ��
	echo "call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	//�п�����Ϊ����ԭ�������Ѿ�������δ�յ�Ӧ��
}