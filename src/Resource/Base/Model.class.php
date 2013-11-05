<?php

namespace Resource\Base;

class Model extends \BootsPHP\Model {

	/**
	 * DAO对象
	 * @var \BootsPHP\Dao
	 */
	protected $dao;

	/**
	 * 获取DAO的错误信息
	 * @return NULL \BootsPHP\Exception
	 */
	public function getErrorMessage(){
		if ($this->dao === null){
			return null;
		}
		if ($this->dao->getException() === null){
			return null;
		}
		return $this->dao->getException()->getMessage();
	}

	/**
	 * 获取DAO的错误代码
	 * @return NULL \BootsPHP\Exception
	 */
	public function getErrorCode(){
		if ($this->dao === null){
			return null;
		}
		if ($this->dao->getException() === null){
			return null;
		}
		return $this->dao->getException()->getCode();
	}
}