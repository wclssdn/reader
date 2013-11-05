<?php

namespace BootsPHP;

class Controller {

	/**
	 * 请求对象
	 * @var \BootsPHP\Request
	 */
	protected $request;

	/**
	 * 响应对象
	 * @var \BootsPHP\Response
	 */
	protected $response;

	public function __construct(){
		$this->request = Request::getInstance();
		$this->response = Response::getInstance();
	}
	
	public function setUser(){
		
	}

	public function __destruct(){
		$this->response && $this->response->response();
	}
}