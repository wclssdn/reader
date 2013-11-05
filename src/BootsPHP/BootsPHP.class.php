<?php

namespace BootsPHP;

use BootsPHP\Exception\ResponseException;
use BootsPHP\Exception\QuitException;

/**
 * 框架入口
 * @author Wclssdn
 */
class BootsPHP {

	/**
	 * 配置对象
	 * @var \BootsPHP\Config
	 */
	private $config;

	/**
	 * 请求对象
	 * @var \BootsPHP\Request
	 */
	private $request;

	/**
	 * 响应对象
	 * @var \BootsPHP\Response
	 */
	private $response;

	private function __construct(){
	}

	public static function getInstance(){
		static $instance = null;
		if ($instance === null){
			$instance = new self();
		}
		return $instance;
	}

	public function execute($configFile){
		$this->config = new Config($configFile);
		$router = $this->config->get('router');
		if (!is_array($router)){
			throw new \Exception('Router config is invalid');
		}
		$define = $this->config->get('define');
		$define && is_file($define) && include $define;
		try{
			Dispatcher::router($router);
		}catch (ResponseException $e){
			$response = Response::getInstance();
			$response->setStatus($e->getCode());
			$response->response();
		}catch (QuitException $e){
			if ($e->getCode()){
				throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
			}
		}
	}

	/**
	 * 获取配置信息
	 * @param string $key
	 * @return Ambigous <NULL, array>
	 */
	public function getConfig($key){
		return $this->config->get($key);
	}

	final private function __clone(){
	}
}

