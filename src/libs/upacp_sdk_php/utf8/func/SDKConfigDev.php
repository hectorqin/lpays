<?php
// cvn2���� 1������ 0:������
const SDK_CVN2_ENC = 0;
// ��Ч�ڼ��� 1:���� 0:������
const SDK_DATE_ENC = 0;
// ���ż��� 1������ 0:������
const SDK_PAN_ENC = 0;
 
$DIR=dirname(__FILE__)."/../../";

// ǩ��֤��·�� (���̻���̨���)
define('SDK_SIGN_CERT_PATH', $DIR."PM_700000000000001_acp.pfx");
//const SDK_SIGN_CERT_PATH = 'D:\xampp\htdocs\route_os\upacp_sdk_php\PM_700000000000001_acp.pfx';

// ǩ��֤������ (���̻���̨���)
 const SDK_SIGN_CERT_PWD = '000000';


// ��ǩ֤�飨�����ò�����������䣩
 define('SDK_VERIFY_CERT_PATH', $DIR."UPOP_VERIFY.cer");
//const SDK_VERIFY_CERT_PATH = 'D:/certs/UPOP_VERIFY.cer';

// �������֤��
 define('SDK_ENCRYPT_CERT_PATH', $DIR."RSA2048_PROD_index_22.cer");
//const SDK_ENCRYPT_CERT_PATH = 'D:\xampp\htdocs\route_os\upacp_sdk_php\RSA2048_PROD_index_22.cer';

// ��ǩ֤��·�������䵽�ļ��У���Ҫ�䵽�����ļ���
 define('SDK_VERIFY_CERT_DIR', $DIR);
//const SDK_VERIFY_CERT_DIR = 'D:\xampp\htdocs\route_os\upacp_sdk_php/';

// ǰ̨�����ַ
const SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';

// ��̨�����ַ
const SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';

// ��������
const SDK_BATCH_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';

//���ʲ�ѯ�����ַ
const SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';

//�ļ����������ַ
const SDK_FILE_QUERY_URL = 'https://101.231.204.80:9080/';

//�п����׵�ַ
const SDK_Card_Request_Url = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';

//App���׵�ַ
const SDK_App_Request_Url = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';

// ǰ̨֪ͨ��ַ (�̻���������֪ͨ��ַ)
const SDK_FRONT_NOTIFY_URL = 'http://localhost:8085/upacp_demo_app/demo/api_05_app/FrontReceive.php';

// ��̨֪ͨ��ַ (�̻���������֪ͨ��ַ�������������ܷ��ʵĵ�ַ)
const SDK_BACK_NOTIFY_URL = 'http://222.222.222.222/upacp_demo_app/demo/api_05_app/BackReceive.php';

//�ļ�����Ŀ¼
const SDK_FILE_DOWN_PATH = './';

//��־ Ŀ¼
// const SDK_LOG_FILE_PATH = $DIR.'./';
define('SDK_LOG_FILE_PATH',$DIR.'logs/');

//��־����
define('SDK_LOG_LEVEL',6);//OFF

