<?php

namespace BootsPHP;

class Request {
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';

	private $webRootFilePath;

	private $webRootUrlPath;

	private $controller;

	private $action;

	private $params;

	private $headers;

	private function __construct(){
		session_start();
	}

	/**
	 * 获取\BootsPHP\Request对象
	 * @return \BootsPHP\Request
	 */
	public static function getInstance(){
		static $instance = null;
		if ($instance === null){
			$instance = new self();
		}
		return $instance;
	}

	public function setWebRootFilePath($path){
		$this->webRootFilePath = $path;
	}

	public function getWebRootFilePath(){
		return $this->webRootFilePath;
	}

	public function setWebRootUrlPath($path){
		$this->webRootUrlPath = $path;
	}

	public function getWebRootUrlPath(){
		return $this->webRootUrlPath;
	}

	public function setController($controller){
		$this->controller = $controller;
	}

	public function getController(){
		return $this->controller;
	}

	public function setAction($action){
		$this->action = $action;
	}

	public function getAction(){
		return $this->action;
	}

	public function setParams(array $params){
		$this->params = $params;
	}

	public function getParams(){
		return $this->params;
	}

	public function getParam($name, $type = 'string', $default = null){
		return isset($this->params[$name]) ? $this->params[$name] : $default;
	}

	public function getHeader($key){
		if ($this->headers === null){
			if (function_exists('getallheaders')){
				$this->headers = \getallheaders();
			}else{

				function getallheaders(){
					$headers = '';
					foreach ($_SERVER as $name => $value){
						if (substr($name, 0, 5) == 'HTTP_'){
							$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
						}
					}
					return $headers;
				}
				$this->headers = getallheaders();
			}
		}
		return isset($this->headers[$key]) ? $this->headers[$key] : null;
	}

	/**
	 * 获取请求方法
	 * @see self const
	 * @return string
	 */
	public function getRequestMethod(){
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * 判断是否是GET请求
	 * @return boolean
	 */
	public function isGet(){
		return $this->getRequestMethod() === 'GET';
	}

	/**
	 * 判断是否是POST请求
	 * @return boolean
	 */
	public function isPost(){
		return $this->getRequestMethod() === 'POST';
	}

	/**
	 * 判断是否是PUT请求
	 * @return boolean
	 */
	public function isPut(){
		return $this->getRequestMethod() === 'PUT';
	}

	/**
	 * 判断是否是DELETE请求
	 * @return boolean
	 */
	public function isDelete(){
		return $this->getRequestMethod() === 'DELETE';
	}

	/**
	 * 判断是否是ajax请求
	 * @return boolean
	 */
	public function isAjax(){
		$requestWith = isset($_SERVER['X-Requested-With']) ? $_SERVER['X-Requested-With'] : (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '');
		return $requestWith == 'XMLHttpRequest';
	}

	/**
	 * 获取PATHINFO
	 * @return string
	 */
	public function getPathInfo(){
	}

	/**
	 * 获取URL参数
	 * @return array
	 */
	public function getQuery(){
	}

	/**
	 * 获取用户IP地址
	 * @return Ambigous <string, boolean>
	 */
	public function getIp(){
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip){
				array_unshift($ips, $ip);
				$ip = false;
			}
			foreach ($ips as $i){
				if (!preg_match('^(10|172\.16|192\.168)\.', trim($i))){
					$ip = $i;
					break;
				}
			}
		}
		return $ip ? $ip : $_SERVER['REMOTE_ADDR'];
	}

	final private function __clone(){
	}
}