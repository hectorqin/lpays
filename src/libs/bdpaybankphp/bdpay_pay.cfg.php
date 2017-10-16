<?php

final class sp_conf{
	public static $config=array(
			'sp_no'=>'9000100005',
			'key_file'=>__DIR__.'/sp.key',
	);
	
	public static function SP_NO(){
		return self::$config['sp_no'];
	}
	public static function SP_KEY_FILE(){
		return self::$config['key_file'];
	}
	// �̻��ڰٶ�Ǯ�����̻�ID
	const SP_NO = '9000100005';
	// ��Կ�ļ�·�������ļ��б������̻��İٶ�Ǯ��������Կ�����ļ���Ҫ����һ����ȫ�ĵط�������������֪��������������
	const SP_KEY_FILE = 'sp.key';
	// �̻�����֧���ɹ�
	const SP_PAY_RESULT_SUCCESS = 1;
	// �̻������ȴ�֧��
	const SP_PAY_RESULT_WAITING = 2;
	// ��־�ļ�
	const LOG_FILE = 'sdk.log';

	// �ٶ�Ǯ��PC������֧��ǰ�ýӿڣ����̼�ҳ��ֱ���������е�֧��ҳ�棬����Ҫ�û���¼�ٶ�Ǯ����
	const BFB_PAY_DIRECT_NO_LOGIN_URL = "https://www.baifubao.com/api/0/pay/0/direct";
	// �ٶ�Ǯ�������Ų�ѯ֧������ӿ�URL
	const BFB_QUERY_ORDER_URL = "https://www.baifubao.com/api/0/query/0/pay_result_by_order_no";
	// �ٶ�Ǯ�������Ų�ѯ���Դ���
	const BFB_QUERY_RETRY_TIME = 3;
	// �ٶ�Ǯ��֧���ɹ�
	const BFB_PAY_RESULT_SUCCESS = 1;
	// �ٶ�Ǯ��֧��֪ͨ�ɹ���Ļ�ִ
	const BFB_NOTIFY_META = "<meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\">";
	
	// ǩ��У���㷨
	const SIGN_METHOD_MD5 = 1;
	const SIGN_METHOD_SHA1 = 2;
	// �ٶ�Ǯ����ʱ���˽ӿڷ���ID
	const BFB_PAY_INTERFACE_SERVICE_ID = 1;
	// �ٶ�Ǯ����ѯ�ӿڷ���ID
	const BFB_QUERY_INTERFACE_SERVICE_ID = 11;
	// �ٶ�Ǯ���ӿڰ汾
	const BFB_INTERFACE_VERSION = 2;
	// �ٶ�Ǯ���ӿ��ַ�����
	const BFB_INTERFACE_ENCODING = 1;
	// �ٶ�Ǯ���ӿڷ��ظ�ʽ��XML
	const BFB_INTERFACE_OUTPUT_FORMAT = 1;
	// �ٶ�Ǯ���ӿڻ��ҵ�λ�������
	const BFB_INTERFACE_CURRENTCY = 1;
}

?>