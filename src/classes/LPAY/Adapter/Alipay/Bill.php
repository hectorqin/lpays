<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Alipay;
use LPAY\Exception;
use LPAY\Utils;
use LPAY\Bill\Result;

class Bill implements \LPAY\Bill,\LPAY\Bill\Data{
	/**
	 * @var Config
	 */
	protected $_config;
	protected $_page=1;
	protected $_codes=array();
	protected $_start_time;
	protected $_end_time;
	public function __construct(Config $config){
		$this->_config=$config;
		$this->_config->set_md5();
	}
	public function set_date($date){
		$time=strtotime($date);
		if ($time<=0) return false;
		$this->set_start_time(strtotime("today",$time));
		$this->set_end_time(strtotime("today",$time+3600*24));
		return $this;
	}
	public function exec(){
		require_once Utils::lib_path("alipay_account_page_query/lib/alipay_submit.class.php");
		return $this->_request();
	}
	public function set_start_time($time){
		$this->_start_time=date("Y-m-d H:i:s",$time);
		return $this;
	}
	public function set_end_time($time){
		$this->_end_time=date("Y-m-d H:i:s",$time);
		return $this;
	}
	/**
	 * 获取账单类型
	 * 默认为全部
	 * 	3011 转账（含红包、集分宝等） 
	* 	3012 收费 
 	* 	4003 充值 
 	* 	5004 提现 
 	* 	5103 退票 
 	* 	6001 在线支付 
	 * @param array $codes
	 * @return \LPAY\Adapter\Alipay\Bill
	 */
	public function set_trans_code(array $codes){
		$this->_codes=$codes;
		return $this;
	}
	public function set_page($page){
		$this->_page=abs(intval($page));
		return $this;
	}
	protected $_next_data=true;
	protected $_temp_data=array();
	protected function _request(){
		$alipay_config=$this->_config->as_array();
		if ($this->_start_time==null||$this->_end_time==null) throw new Exception('plase set date');
		//页号
		$page_no = $this->_page;
		//必填，必须是正整数
		//账务查询开始时间
		$gmt_start_time = $this->_start_time;
		//格式为：yyyy-MM-dd HH:mm:ss
		//账务查询结束时间
		$gmt_end_time = $this->_end_time;
		//格式为：yyyy-MM-dd HH:mm:ss
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "account.page.query",
				"partner" => trim($alipay_config['partner']),
				"page_no"	=> $page_no,
				"gmt_start_time"	=> $gmt_start_time,
				"gmt_end_time"	=> $gmt_end_time,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		if (count($this->_codes)>0){
			$parameter['trans_code']=implode(",",$this->_codes);
		}
		//建立请求
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		//解析XML
		//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
		$xml = simplexml_load_string($html_text);
		$data = json_decode(json_encode($xml),TRUE);
		if (!isset($data['is_success'])||$data['is_success']!='T') throw new Exception(@$data['error']);
		
		if(isset($data['response']['account_page_query_result']['account_log_list']['AccountQueryAccountLogVO'])){
			$this->_temp_data=$data['response']['account_page_query_result']['account_log_list']['AccountQueryAccountLogVO'];
			//转格式
		}
		if(isset($data['response']['account_page_query_result']['has_next_page'])){
			if ($data['response']['account_page_query_result']['has_next_page']=='F') $this->_next_data=false;
			if (count($this->_temp_data)==0)$this->_next_data=false;
		}
		
		
		return true;
	}
	/**
	 * {@inheritDoc}
	 * @see \LPAY\Bill\Data::get_result()
	 */
	public function get_result(){
		if (count($this->_temp_data)==0&&$this->_next_data){
			$this->_page++;
			$this->_request();
		}
		if (count($this->_temp_data)==0) return false;
		$item=array_shift($this->_temp_data);
		
		$fee=$item['service_fee'];
		if ($item['total_fee']>0.1){
			foreach ($this->_temp_data as $k=>$v){
				if ($v['merchant_out_order_no']==$item['merchant_out_order_no']&&$v['trans_code_msg']=='收费'){
					$fee=$v['outcome'];
					$item['fee_item']=$this->_temp_data[$k];
					unset($this->_temp_data[$k]);
					break;
				}
			}
		}
		
		if ($item['trans_code_msg']=='提现'){
			$type=Result::TYPE_TRANSFERS;
		}else if ($item['trans_code_msg']=='退票'){
			$type=Result::TYPE_REFUND;
		}else if ($item['trans_code_msg']=='在线支付'){
			$type=Result::TYPE_PAY;
		}else{
			$type=Result::TYPE_OTHER;
		}
		
		
		//{"balance":"0.01","buyer_account":"2088002930536912","currency":"156","deposit_bank_no":"20161227523219729106","goods_title":"pay 0.01","income":"0.01","iw_account_log_id":"300310999157911","memo":[" "],"merchant_out_order_no":"MY161227135100662","outcome":"0.00","partner_id":"2088521471438854","rate":"0.006000","seller_account":"2088521471438854","seller_fullname":"\u6df1\u5733\u5e02\u5143\u548c\u901a\u7f51\u7edc\u6709\u9650\u516c\u53f8","service_fee":"0.00","service_fee_ratio":[" "],"sign_product_name":"\u5feb\u6377\u624b\u673awap\u652f\u4ed8","sub_trans_code_msg":"\u5feb\u901f\u652f\u4ed8,\u652f\u4ed8\u7ed9\u4e2a\u4eba\uff0c\u652f\u4ed8\u5b9d\u5e10\u6237\u5168\u989d","total_fee":"0.01","trade_no":"2016122721001004910236554338","trade_refund_amount":"0.00","trans_code_msg":"\u5728\u7ebf\u652f\u4ed8","trans_date":"2016-12-27 13:51:35"}
		
		return new Result(
			$type,
			$item['merchant_out_order_no'], 
			$item['trade_no'], 
			$item['buyer_account'], 
			$item['total_fee'], 
			$fee, 
			strtotime($item['trans_date']), 
			$item
		);
	}
}