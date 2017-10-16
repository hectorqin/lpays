<?php
/**
 * lsys pay
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LPAY\Adapter;
use LPAY\Transfers\TransfersResult;
abstract class TransfersNotifyBatch implements  \LPAY\Transfers\TransfersNotifyBatch{
	protected $_result=array();
	protected $_total_rows  = 0;
	protected $_current_row = 0;
	protected function _add_transfers_result(TransfersResult $result){
		$this->_result[]=$result;
		$this->_total_rows=count($this->_result[]);
		return $this;
	}
	public function count()
	{
		return $this->_total_rows;
	}
	public function key()
	{
		return $this->_current_row;
	}
	public function next()
	{
		++$this->_current_row;
		return $this;
	}
	public function prev()
	{
		--$this->_current_row;
		return $this;
	}
	public function rewind()
	{
		$this->_current_row = 0;
		return $this;
	}
	public function valid()
	{
		return $this->_current_row >= 0 AND $this->_current_row < $this->_total_rows;
	}
	public function seek($offset)
	{
		if ($offset < 0 OR $offset >= $this->_total_rows)
		{
			return false;
		}
		$this->_current_row = $offset;
	}
	public function current(){
		if (!$this->valid()||!isset($this->_result[$this->_current_row])) return null;
		return $this->_result[$this->_current_row];
	}
}