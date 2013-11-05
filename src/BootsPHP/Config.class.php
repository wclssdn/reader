<?php

namespace BootsPHP;

use BootsPHP\Exception\FileNotExistsException;
use BootsPHP\Exception\ConfigFileFormatError;

/**
 * 配置读取基类
 * TODO 支持多格式配置, 构造方法提供文件格式选项.
 * 最终解析成数组
 * @author Wclssdn
 */
class Config {

	/**
	 * 配置数组
	 * @var array
	 */
	protected $config;

	/**
	 * 载入配置文件
	 * @param string $configFile 配置文件
	 * @throws FileNotExistsException
	 * @throws ConfigFileFormatError
	 */
	public function __construct($configFile){
		if (!is_file($configFile)){
			throw new FileNotExistsException("Config File {$configFile} not exists!");
		}
		$this->config = include $configFile;
		if (!$this->config){
			throw new ConfigFileFormatError("Config File {$configFile} format error!");
		}
	}

	/**
	 * 获取配置值
	 * @param string $key
	 * @return mixed
	 */
	public function get($key){
		return isset($this->config[$key]) ? $this->config[$key] : null;
	}

	public function getAll(){
		return $this->config;
	}
}