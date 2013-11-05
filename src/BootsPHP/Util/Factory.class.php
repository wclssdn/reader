<?php

namespace BootsPHP\Util;

use BootsPHP\BootsPHP;
use BootsPHP\Pdo;

/**
 * 工厂
 * @author Wclssdn
 */
class Factory {

	/**
	 * 获取主从PDO对象数组
	 * @param string $handelName 数据库句柄缓存标识
	 * @param array $config 数据库配置
	 * @return boolean array \BootsPHP\Dao\Pdo\Pdo
	 */
	public static function getPdo($handelName, $config){
		static $cache = array();
		if (!isset($cache[$handelName])){
			if ($config === null){
				return false;
			}
			if (!isset($config['master'])){
				return false;
			}
			$pdos['master'] = Pdo::getInstance($config['master']['host'], $config['master']['port'], $config['master']['db'], $config['master']['user'], $config['master']['pass'], isset($config['master']['charset']) ? $config['master']['charset'] : null, isset($config['master']['driver']) ? $config['master']['driver'] : null, isset($config['master']['options']) ? $config['master']['options'] : array());
			if (isset($config['slave'])){
				if (isset($config['slave']['host'])){
					$pdos['slave'] = Pdo::getInstance($config['slave']['host'], $config['slave']['port'], $config['slave']['db'], $config['slave']['user'], $config['slave']['pass'], isset($config['slave']['charset']) ? $config['slave']['charset'] : null, isset($config['slave']['driver']) ? $config['slave']['driver'] : null, isset($config['slave']['options']) ? $config['slave']['options'] : array());
				}else{
					foreach ($config['slave'] as $slave){
						if (!isset($slave['host'])){
							continue;
						}
						$pdos['slave'] = Pdo::getInstance($slave['host'], $slave['port'], $slave['db'], $slave['user'], $slave['pass'], isset($slave['charset']) ? $slave['charset'] : null, isset($slave['driver']) ? $slave['driver'] : null, isset($slave['options']) ? $slave['options'] : array());
					}
				}
			}
			$cache[$handelName] = $pdos;
		}
		return $cache[$handelName];
	}
}