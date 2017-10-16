<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;
use LPAY\Pay;
use LPAY\Utils;
use LPAY\Pay\Adapter;
use LPAY\Exception;
use LPAY\Pay\PayParam;
use LPAY\Pay\PayRender;
use LPAY\Pay\PayAdapterCallback;
use LPAY\Pay\PayResult;
use LPAY\Pay\QueryParam;
class PayWap extends PayNotify implements PayAdapterCallback{
	public static $save_key="__lpay_param__";
	const NAME="lpay_wechat";
	public function __construct(PayWapConfig $config){
		$this->set_name($this->support_name());
		$this->_config=$config;
	}
	protected static function _init_sess(){
		if(!session_id()) session_start();
	}
	
	public function enable(){
		return Utils::user_agent(Utils::BROWSER_WECHAT);
	}
	public function support_type(){
		return Pay::TYPE_WECHAT;
	}
	public function support_name(){
		return PayWap::NAME;
	}
	public function match($name){
		if ($name==PayWap::NAME) return true;
	}
	/**
	 * next action : pay_js
	 * {@inheritDoc}
	 * @see \LPAY\Pay\PayAdapter::pay_render()
	 */
	public function pay_render(PayParam $pay_param){
		require_once Utils::lib_path("wechat/lib/WxPay.JsApiPay.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$state=uniqid();
		self::_init_sess();
		$_SESSION["__LPAY_WECAHT_PAY__"]=array(
			'_param_'=>serialize($pay_param),
			'_state_'=>$state
		);
		$pay_url =$this->_config->get_oauth_return_url();
		$tools = new \JsApiPay();
		$url = $tools->GetOpenidUrl(urlencode($pay_url),$state);
		return new PayRender(PayRender::OUT_URL, $url);
	}
	/**
	 * refund page,get pay param
	 * @return NULL|\LPAY\Pay\PayParam
	 */
	public static function get_pay_param(){
		self::_init_sess();
		if (!isset($_SESSION["__LPAY_WECAHT_PAY__"]['_param_'])) return null;
		/**
		 * @var PayParam $pay_param
		 */
		$pay_param=@unserialize($_SESSION["__LPAY_WECAHT_PAY__"]['_param_']);
		if (!$pay_param instanceof PayParam) return null;
		//check other param
		if (!isset($_GET['state'])||!isset($_GET['code'])) return null;
		if (!isset($_SESSION["__LPAY_WECAHT_PAY__"]['_state_'])) return null;
		if ($_GET['state']!=$_SESSION["__LPAY_WECAHT_PAY__"]['_state_']) return null;
		return $pay_param;
	}
	
	/**
	 * 获取支付JS
	 * @param null $pay_param
	 * @throws Exception
	 * @return boolean|json数据，可直接填入js函数作为参数
	 */
	public function get_pay_js(PayParam $pay_param){
		require_once Utils::lib_path("wechat/lib/WxPay.JsApiPay.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$pay_url =$this->_config->get_return_url();
		if (!isset($_GET['code'])) throw new Exception('oauth code is miss,plase try pay again');
		$tools = new \JsApiPay();
		try{
			$openid = @$tools->getOpenidFromMp($_GET['code']);
		}catch (\WxPayException $e){
			throw new Exception($e->getMessage(),$e->getCode(),$e);
		}
		$notify_url =$this->_config->get_notify_url();
		
		$body=$pay_param->get_title();
		$attach=$pay_param->get_body();
		$sn=$pay_param->get_sn();
		$money=intval($pay_param->get_pay_money()*100);
		$timeout=$pay_param->get_timeout();
		$timeout||$timeout=time()+3600*24*7;
		$pids=implode(",",$pay_param->get_goods());
		if(empty($pids))$pids='0';
		$ctime=$pay_param->get_create_time();
		
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($body);
		$input->SetAttach($attach);
		$input->SetOut_trade_no($sn);
		$input->SetTotal_fee($money);
		$input->SetTime_start(date("YmdHis",$ctime));
		$input->SetTime_expire(date("YmdHis",$timeout));
		$input->SetProduct_id($pids);
// 		$input->SetGoods_tag("");
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openid);
		try{
			$order = \WxPayApi::unifiedOrder($input);
			if (isset($order['return_code'])&&$order['return_code']=='FAIL'){
				throw new Exception(@$order['return_msg']);
			}
			if (isset($order['result_code'])&&$order['result_code']=='FAIL'){
				throw new Exception(@$order['return_msg']);
			}
			$jsApiParameters = $tools->GetJsApiParameters($order);
		}catch (\WxPayException $e){
			throw new Exception($e->getMessage(),$e->getCode(),$e);
		}
		return $jsApiParameters;
	}
	/**
	 * 获取支付HTML
	 * @param PayParam $pay_param
	 * @param unknown $jsApiParameters
	 * @param string $auto_pay
	 * @return string
	 */
	public function render_js(PayParam $pay_param,$jsApiParameters,$auto_pay=true){
		$auto_pay=$auto_pay?1:0;
		$return_url=$this->_config->get_return_url();
		$op=strpos($return_url, "?")!==false?"&":"?";
		$sn=Utils::encode_url($pay_param->get_sn(),$this->_config->get_WxPayConfigObj()->APPSECRET);
		$return_url.=$op.self::$save_key."=".$sn;
		$cancel_url=$pay_param->get_cancel_url();
		ob_start();
		require_once Utils::lib_path("wechat/utils/pay.php");
		$html=ob_get_contents();
		ob_end_clean();
		return $html;
	}
	public function pay_callback(){
		//不能在渲染完删除SESSION,因为微信电脑版有次后台的页面请求..
		self::_init_sess();unset($_SESSION["__LPAY_WECAHT_PAY__"]);
		if (!isset($_GET[self::$save_key]))  return PayResult::unkown($this->get_name(),'pay sn not find');
		$sn=Utils::decode_url($_GET[self::$save_key],$this->_config->get_WxPayConfigObj()->APPSECRET);
		if (empty($sn)) return PayResult::unkown($this->get_name(),PayResult::$sign_invalid);
		$param=new QueryParam($sn, null, null);
		return $this->query($param);
	}
	
}