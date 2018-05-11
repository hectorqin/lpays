<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY;
use LPAY\Adapter\PayAdapter;
use LPAY\Pay\RefundAdapter;
use LPAY\Transfers\TransfersAdapter;
class Pay{
	/**
	 * pc page
	 * @var integer
	 */
	const TYPE_PC=1;
	/**
	 * mobile wap page
	 * @var integer
	 */
	const TYPE_WAP=1<<1;
	/**
	 * android app
	 * @var integer
	 */
	const TYPE_ANDROID=1<<2;
	/**
	 * ios app
	 * @var integer
	 */
	const TYPE_IOS=1<<3;
	/**
	 * wexin
	 * @var integer
	 */
	const TYPE_WECHAT=1<<4;
	/**
	 * @var int
	 */
	protected $_type;
	/**
	 * @var PayAdapter[]
	 */
	protected $_pays=array();
	/**
	 * @var RefundAdapter[]
	 */
	protected $_refunds=array();
	/**
	 * @var TransfersAdapter[]
	 */
	protected $_transfers=array();
	
	public function __construct(){
		if ($this->_type===null) $this->_type=self::TYPE_PC|self::TYPE_WAP|self::TYPE_ANDROID|self::TYPE_IOS;
	}
	/**
	 * @param PayAdapter $handler
	 * @return \LPAY\Pay
	 */
	public function add_pay(PayAdapter $handler){
		$this->_pays[]=$handler;
		return $this;
	}
	/**
	 * 
	 * @param PayAdapter $handler
	 * @return \LPAY\Pay
	 */
	public function del_pay(PayAdapter $handler=null){
		if ($handler==null) return $this;
		foreach ($this->_pays as $k=>$v){
			if ($handler===$v)unset($this->_pays[$k]);
		}
		return $this;
	}
	
	/**
	 * @param int $type
	 * @return \LPAY\Pay
	 */
	public function set_type($type){
		$this->_type=intval($type);
		return $this;
	}
	/**
	 * @return \LPAY\Pay\PayAdapter[]
	 */
	public function get_pays(){
		$handler=array();
		foreach ($this->_pays as $v){
			if ($v->enable()&&($v->support_type()&$this->_type)) $handler[]=$v;
		}
		return $handler;
	}
	/**
	 * @param string $name
	 * @return \LPAY\Pay\PayAdapter
	 */
	public function find_pay($name){
		foreach ($this->_pays as $v){
			if ($v->match($name)) return $v->set_name($name);
		}
		return null;
	}
	/**
	 * @param RefundAdapter $refund
	 * @return \LPAY\Pay
	 */
	public function add_refund(RefundAdapter $refund){
		$this->_refunds[]=$refund;
		return $this;
	}
	/**
	 * @param string $name
	 * @return \LPAY\Pay\RefundAdapter
	 */
	public function find_refund($name){
		foreach ($this->_refunds as $v){
			if ($v->match($name)) return $v->set_name($name);
		}
		return null;
	}
	/**
	 * @param TransfersAdapter $transfers
	 * @return \LPAY\Pay
	 */
	public function add_transfers(TransfersAdapter $transfers){
		$this->_transfers[]=$transfers;
		return $this;
	}
	/**
	 * @param string $name
	 * @return \LPAY\Transfers\TransfersAdapter
	 */
	public function find_transfers($name){
		foreach ($this->_transfers as $v){
			if ($v->enable()&&$v->transfers_name()==$name) return $v;
		}
		return null;
	}
	/**
	 * @return TransfersAdapter[]
	 */
	public function get_transfers(){
		$handler=array();
		foreach ($this->_transfers as $v){
			if ($v->enable()) $handler[]=$v;
		}
		return $handler;
	}
}


