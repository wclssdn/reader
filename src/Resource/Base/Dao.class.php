<?php

namespace Resource\Base;

use BootsPHP\BootsPHP;
use BootsPHP\Exception\DatabaseException;
use BootsPHP\Util\Factory;

class Dao extends \BootsPHP\Dao {

	public function __construct($pdo = null){
		if (!$this->tableName){
			throw new DatabaseException('table name is empty');
		}
		$pdos = Factory::getPdo($this->getDbHandelName(), $this->getDbConfig($this->tableName));
		if ($pdos === false){
			throw new DatabaseException("{$this->tableName}'s config is not available");
		}
		parent::__construct($pdos['master']);
		if (isset($pdos['slave'])){
			if (is_array($pdos['slave'])){
				foreach ($pdos['slave'] as $slave){
					$this->addSlave($slave);
				}
			}elseif (is_object($pdos['slave'])){
				$this->addSlave($pdos['slave']);
			}
		}
	}

	/**
	 * 获取数据库配置
	 * @param string $tableName
	 * @param array $dbConfig 数据库配置数组
	 * @return null array
	 */
	protected function getDbConfig($tableName, $dbConfig = array()){
		!$dbConfig && $dbConfig = BootsPHP::getInstance()->getConfig('db');
		return isset($dbConfig[$tableName]) ? $dbConfig[$tableName] : (isset($dbConfig['*']) ? $dbConfig['*'] : null);
	}

	/**
	 * 获取数据库操作句柄缓存标识
	 * 默认使用表名, 如果多个网站可能存在表名重复, 需要重写此函数
	 * @return string
	 */
	protected function getDbHandelName(){
		return $this->tableName;
	}
}