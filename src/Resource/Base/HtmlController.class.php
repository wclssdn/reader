<?php

namespace Resource\Base;

use BootsPHP\Exception\ResponseException;
use BootsPHP\Response;
use BootsPHP\Exception\FileNotExistsException;

class HtmlController extends \BootsPHP\Controller {

	protected $templateData = array();

	protected $view;

	public function __construct(){
		parent::__construct();
		defined('URL_ROOT') || define('URL_ROOT', $this->request->getWebRootUrlPath());
		$this->view = new View();
		$this->view->setTemplateData(array('title' => null));
	}

	protected function assign($key, $val){
		$this->templateData[$key] = $val;
	}

	protected function show(array $templateData = array(), $templateFile = '', $templatePath = ''){
		if ($templatePath){
			$this->view->setTemplatePath($templatePath);
		}
		if ($templateFile){
			$this->view->setTemplateFile($templateFile);
		}else{
			$this->view->setTemplateFile($this->request->getAction() . '.tpl.php');
		}
		$templateData && $this->templateData = array_merge($this->templateData, $templateData);
		$this->templateData && $this->view->setTemplateData($this->templateData);
		try{
			$this->view->display();
		}catch (FileNotExistsException $e){
			$this->response->setStatus(Response::STATUS_FILE_NOT_FOUND);
		}
	}

	protected function success($message, $code = 0, array $data = array()){
		if ($this->request->isAjax()){
			$this->outputJson($code, $message, $data);
		}else{
			$this->view->setTemplatePath($this->request->getWebRootFilePath() . '/View/Public');
			$this->view->setTemplateFile('success.tpl.php');
			$this->view->setTemplateData(array(
				'code' => $code, 
				'message' => $message, 
				'data' => $data));
			try{
				$this->view->display();
			}catch (FileNotExistsException $e){
				throw new ResponseException('Default success file not found!', Response::STATUS_FILE_NOT_FOUND);
			}
		}
		$this->response->response();
	}

	protected function error($message, $code = 1, array $data = array()){
		if ($this->request->isAjax()){
			$this->outputJson($code, $message, $data);
		}else{
			$this->view->setTemplatePath($this->request->getWebRootFilePath() . '/View/Public');
			$this->view->setTemplateFile('error.tpl.php');
			$this->view->setTemplateData(array(
				'code' => $code, 
				'message' => $message, 
				'data' => $data));
			try{
				$this->view->display();
			}catch (FileNotExistsException $e){
				throw new ResponseException('Default error file not found!', Response::STATUS_FILE_NOT_FOUND);
			}
		}
		$this->response->response();
	}

	protected function outputJson($code = 0, $message = '', array $data = array()){
		$json = json_encode(array(
			'code' => $code, 
			'message' => $message, 
			'data' => $data));
		echo $json;
		$this->response->response();
	}
}