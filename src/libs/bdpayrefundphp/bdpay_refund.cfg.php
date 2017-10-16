<?php

final class sp_conf{
	
	
	public static $config=array(
			'sp_no'=>'9000100005',
			'key_file'=>'sp.key',
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
	// ��־�ļ�
	const LOG_FILE = 'sdk.log';
	

	// �ٶ�Ǯ���˿�ӿ�URL
	const BFB_REFUND_URL = "https://www.baifubao.com/api/0/refund";
	// �ٶ�Ǯ���˿��ѯ�ӿ�URL
	const BFB_REFUND_QUERY_URL = "https://www.baifubao.com/api/0/refund/0/query";
	// �ٶ�Ǯ����ѯ���Դ���
	const BFB_QUERY_RETRY_TIME = 3;
	// �ٶ�Ǯ���˿���֪ͨ�ɹ���Ļ�ִ
	const BFB_NOTIFY_META = "<meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\">";
	
	// ǩ��У���㷨
	const SIGN_METHOD_MD5 = 1;
	const SIGN_METHOD_SHA1 = 2;
	// �ٶ�Ǯ���˿�ӿڷ���ID
	const BFB_REFUND_INTERFACE_SERVICE_ID = 2;
	// �ٶ�Ǯ���˿��ѯ�ӿڷ���ID
	const BFB_QUERY_INTERFACE_SERVICE_ID = 12;
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