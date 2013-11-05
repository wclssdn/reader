<?php

namespace Resource\Base;

use BootsPHP\Request;
use BootsPHP\Exception\FileNotExistsException;

class View {

	protected $templateFile;

	protected $templatePath;

	protected $templateSubPath;

	private $templateData;

	public function setTemplatePath($templatePath){
		$this->templatePath = $this->getPath($templatePath);
	}

	public function getTemplatePath(){
		return $this->templatePath;
	}

	public function setTemplateSubPath($templateSubPath){
		$this->templateSubPath = $this->getPath($templateSubPath);
		$this->templateSubPath == DIRECTORY_SEPARATOR && $this->templateSubPath = '';
	}

	public function getTemplateSubPath(){
		return $this->templateSubPath;
	}

	public function setTemplateFile($templateFile){
		$this->templateFile = $templateFile;
	}

	public function getTemplateFile(){
		return $this->templateFile;
	}

	public function setTemplateData(array $templateData){
		$this->templateData = $templateData;
	}

	public function getTemplateData(){
		return $this->templateData;
	}

	public function display($templateFile = '', $templateSubPath = ''){
		$templateFile && $this->templateFile = $templateFile;
		$templateSubPath && $templateSubPath = $this->getPath($templateSubPath);
		$filename = $this->templatePath . ($templateSubPath ? $templateSubPath : $this->templateSubPath) . $this->templateFile;
		if (!is_file($filename)){
			throw new FileNotExistsException("File {$filename} is not found!");
		}
		call_user_func(function ($filename, $vars){
			$vars && extract($vars);
			include $filename;
		}, $filename, $this->templateData);
	}

	public function fetch($templateFile = ''){
		ob_start();
		$this->display($templateFile);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	private function getPath($path){
		return rtrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
}