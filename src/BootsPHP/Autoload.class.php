<?php

namespace BootsPHP;

use BootsPHP\Exception\ClassNotFoundException;

/**
 * 自动加载
 * @author Wclssdn
 */
class Autoload {

	/**
	 * 根路径
	 * @var string
	 */
	private $path;

	/**
	 * 搜寻目录
	 * @var array
	 */
	private $findPath = array();

	/**
	 * 文件扩展名
	 * @var array
	 */
	private $fileExt = array('.class.php', '.php');

	private function __construct($path){
		$this->path = $path;
	}

	/**
	 * 相同路径只保留一个对象
	 * @param string $path
	 * @return \BootsPHP\Autoload
	 */
	public static function getInstance($path){
		static $instance = array();
		if (isset($instance[$path])) {
			return $instance[$path];
		}
		return $instance[$path] = new self($path);
	}
	
	/**
	 * 设置搜索文件夹(相对站点根目录)
	 * @param array $findPath
	 * @return \BootsPHP\Autoload
	 */
	public function setFindPath(array $findPath){
		$this->findPath = $findPath;
		return $this;
	}

	public function setFileExtension(array $extension){
		$this->fileExt = $extension;
		return $this;
	}

	public function addFindPath($path){
		!in_array($path, $this->findPath) && $this->findPath[] = $path;
		return $this;
	}

	public function addFileExt($fileExt){
		!is_array($fileExt, $this->fileExt) && $this->fileExt[] = $fileExt;
		return $this;
	}

	public function registerAutoloadFunction($callable){
		spl_autoload_register($callable);
	}

	public function unregisterAutoloadFunction($callable){
		spl_autoload_unregister($callable);
	}

	/**
	 * 对此路径的自动加载生效
	 */
	public function hold(){
		$this->registerAutoloadFunction(array($this, '__autoload'));
	}

	private function __autoload($classname){
		$file = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
		is_array($this->findPath) && !in_array('', $this->findPath) && array_unshift($this->findPath, '');
		reset($this->findPath);
		reset($this->fileExt);
		do {
			$path = current($this->findPath);
			$path == '.' && $path = '';
			$path && $path .= DIRECTORY_SEPARATOR;
			do {
				$ext = current($this->fileExt);
				$filename = "{$this->path}{$path}{$file}{$ext}";
				if (is_file($filename)){
					include $filename;
					return;
				}
			}while (next($this->fileExt));
			reset($this->fileExt);
		}while (next($this->findPath));
		throw new ClassNotFoundException("Class {$classname} is not found!");
	}
}