<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter\Wechat;
use LPAY\Exception;
use LPAY\Utils;
use LPAY\Bill\Result;
class Bill implements \LPAY\Bill,\LPAY\Bill\Data{
	/**
	 * @var Config
	 */
	protected $_config;
	protected $_temp_data;
	protected $_date;
	protected $_bill_type;
	public function __construct(Config $config){
		//https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_6
		$this->_config=$config;
		$this->_bill_type='ALL';
	}
	public function set_date($date){
		$time=strtotime($date);
		if ($time<=0) return false;
		$this->_date=date("Ymd",strtotime($date));
		return $this;
	}
	protected $_bill_types=array(
		'ALL',
		'SUCCESS',
		'REFUND',
	);
	/**
	 * 获取账单类型
	 * 默认为全部
	 * ALL，返回当日所有订单信息，默认值
	 * SUCCESS，返回当日成功支付的订单
	 * REFUND，返回当日退款订单
	 * @param array $codes
	 * @return \LPAY\Adapter\Alipay\Bill
	 */
	public function set_bill_type($bill_type){
		if (!in_array($bill_type, $this->_bill_types))$bill_type='ALL';
		$this->_bill_type=$bill_type;
		return $this;
	}
	public function exec(){
		require_once Utils::lib_path("wechat/lib/WxPay.Api.php");
		\WxPayApi::$config=$this->_config->get_WxPayConfigObj();
		$bill_date = $this->_date;
		$bill_type = $this->_bill_type;
		$input = new \WxPayDownloadBill();
		$input->SetBill_date($bill_date);
		$input->SetBill_type($bill_type);
		try{
			$data = \WxPayApi::downloadBill($input,30,true);
		}catch (\Exception $e){
			throw new Exception($e->getMessage(),$e->getCode(),$e);
		}
		$this->_temp_data=$data;
	}
	protected $_off=0;
	protected $_header=array();
	/**
	 * {@inheritDoc}
	 * @see \LPAY\Bill\Data::get_result()
	 */
	public function get_result(){
		if ($this->_off===0){
			$this->_off=strpos($this->_temp_data, "\n");
			$this->_header=explode(",",substr($this->_temp_data,3,$this->_off-3));
		}
		if (empty($this->_temp_data)) return false;
		
		$noff=strpos($this->_temp_data, "\n",$this->_off+1);
		$item=trim(substr($this->_temp_data, $this->_off+1,$noff-$this->_off));
		$this->_off=$noff;
		if (empty($item)) return false;
		
		
		$item=explode(",`", $item);
		$item[0]=trim($item[0],'` ');
		
		
		
		if (count($this->_header)!=count($item)) return false;
		
		foreach ($item as $k=>$v){
			$item[$this->_header[trim($k)]]=$v;
			unset($item[$k]);
		}
		
		// 		第一行为表头，根据请求下载的对账单类型不同而不同(由bill_type决定),目前有：
		
		// 		当日所有订单
		// 		交易时间,公众账号ID,商户号,子商户号,设备号,微信订单号,商户订单号,用户标识,交易类型,交易状态,付款银行,货币种类,总金额,代金券或立减优惠金额,微信退款单号,商户退款单号,退款金额,代金券或立减优惠退款金额，退款类型，退款状态,商品名称,商户数据包,手续费,费率
		
		// 		当日成功支付的订单
		// 		交易时间,公众账号ID,商户号,子商户号,设备号,微信订单号,商户订单号,用户标识,交易类型,交易状态,付款银行,货币种类,总金额,代金券或立减优惠金额,商品名称,商户数据包,手续费,费率
		
		// 		当日退款的订单
		// 		交易时间,公众账号ID,商户号,子商户号,设备号,微信订单号,商户订单号,用户标识,交易类型,交易状态,付款银行,货币种类,总金额,代金券或立减优惠金额,退款申请时间,退款成功时间,微信退款单号,商户退款单号,退款金额,代金券或立减优惠退款金额,退款类型,退款状态,商品名称,商户数据包,手续费,费率
		
		
		if (isset($item['商户退款单号'])&&$item['商户退款单号']!='0'&&$item['商户退款单号']!=''){//退款记录
			$pay_sn=$item['商户退款单号'];
			$pay_no=$item['微信退款单号'];
			$type=Result::TYPE_REFUND;
			if (isset($item['退款成功时间']))$time=strtotime($item['退款成功时间']);
			else $time=strtotime($item['交易时间']);
			$fee=0;
			$money=$item['退款金额'];
		}else{
			$pay_sn=$item['商户订单号'];
			$pay_no=$item['微信订单号'];
			$type=Result::TYPE_PAY;
			$time=strtotime($item['交易时间']);
			$fee=$item['手续费'];
			$money=$item['总金额'];
		}
		return new Result(
				$type,
				$pay_sn,
				$pay_no,
				$item['用户标识'],
				$money,
				$fee,
				$time,
				$item
		);
	}
}