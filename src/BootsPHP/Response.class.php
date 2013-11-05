<?php

namespace BootsPHP;

use BootsPHP\Exception\QuitException;
use BootsPHP\Exception\ResponseException;

class Response {
	const STATUS_OK = 200;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_FOUND = 302;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_BAD_REQUEST = 400;
	const STATUS_FORBIDDEN = 403;
	const STATUS_FILE_NOT_FOUND = 404;
	const STATUS_METHOD_NOT_ALLOWED = 405;
	const STATUS_INTERNAL_SERVER_ERROR = 500;
	const STATUS_SERVICE_UNAVAILABLE = 503;
	const CACHE_CONTROL_MAX_AGE = 1;
	const CACHE_CONTROL_MUST_REVALIDATE = 2;
	const CACHE_CONTROL_NO_CACHE = 4;
	const CACHE_CONTROL_NO_STORE = 8;
	const CACHE_CONTROL_PRIVATE = 16;
	const CACHE_CONTROL_PUBLIC = 32;

	private $status = self::STATUS_OK;

	private $contentType = 'text/html';

	private $charset = 'utf-8';

	private $contentEncoding = 'gzip';

	private $cacheControl = 'Public';

	private $cacheTime = 0;

	private $headers = array();

	private function __construct(){
		ob_start();
	}

	public static function getInstance(){
		static $instance = null;
		if ($instance === null){
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * 设置响应状态码
	 * @see self::STATUS_
	 * @param number $status
	 */
	public function setStatus($status){
		$this->status = $status;
	}

	/**
	 * 设置文档类型
	 * @param string $contentType
	 */
	public function setContentType($contentType){
		$this->contentType = $contentType;
	}

	/**
	 * 设置页面文本编码
	 * @param string $charset
	 */
	public function setCharset($charset){
		$this->charset = $charset;
	}

	/**
	 * 设置页面压缩编码
	 * @param string $contentEncoding
	 */
	public function setContentEncoding($contentEncoding){
		$this->contentEncoding = $contentEncoding;
	}

	/**
	 * 设置缓存控制
	 * TODO 设置缓存控制以及头控制
	 * @see self::CACHE_CONTROL_
	 * @param string $cache
	 */
	public function setCacheControl($cacheControl){
		$this->cacheControl = $cacheControl;
	}

	/**
	 * 设置缓存相对时间(单位:秒)
	 * TODO 根据http1.0 1.1分别设置缓存头
	 * @param number $cacheTime
	 */
	public function setCacheTime($cacheTime){
		$this->cacheTime = $cacheTime;
	}

	public function noModifed(){
		$this->setStatus(self::STATUS_NOT_MODIFIED);
	}

	public function isModifyed($time){
		if (strtotime(Request::getInstance()->getHeader('If-Modified-Since')) == $time){
			return false;
		}
		return true;
	}

	public function setLastModify($mtime){ // Tue, 23 Jul 2013 07:48:07 GMT
		$this->headers['Last-Modified'] = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
	}

	public function setHeader($name, $value){
		$this->headers[$name] = $value;
	}

	/**
	 * 输出响应
	 */
	public function response(){
		$this->sendHeader();
		ob_end_flush();
		$this->end();
	}

	/**
	 * 跳转
	 * @param string $url
	 */
	public function redirect($url){
		$this->setHeader('Location', $url);
		$this->sendHeader();
		$this->end();
	}

	/**
	 * 处理文件响应
	 * TODO 添加range断点续传
	 * @param string $file
	 * @param boolean|string $saveAs 是否下载文件, 可通过字符串参数指定文件名
	 */
	public function holdFile($file, $saveAs = ''){
		if (!is_file($file)){
			$this->setStatus(self::STATUS_FILE_NOT_FOUND);
			$this->sendHeader();
			$this->end();
		}
		$mime = $this->getMime($file);
		$this->setHeader('X-Powered-By', 'BootsPHP');
		if ($mime === 'text/x-php'){
			$this->setContentType('text/html');
			$this->sendHeader();
			include $file;
		}else{
			if (!$this->isModifyed(filemtime($file))){
				$this->setStatus(self::STATUS_NOT_MODIFIED);
			}
			$this->setContentType($mime);
			$this->setLastModify(filemtime($file));
			if ($saveAs){
				$saveAs === true && $saveAs =\pathinfo($file, PATHINFO_BASENAME);
				$this->setHeader('Content-Disposition', array(
					'attachment', 
					'filename' => $saveAs));
			}
			$this->sendHeader();
			$mime && readfile($file);
		}
		$this->end(true);
	}

	/**
	 * 发送header
	 */
	private function sendHeader(){
		if ($this->contentType){
			if ($this->charset){
				header("content-type:{$this->contentType}; charset={$this->charset}", null, $this->status);
			}else{
				header("content-type:{$this->contentType}", null, $this->status);
			}
		}
		if ($this->cacheControl){
			$cacheControl = array($this->cacheControl);
			$this->cacheTime && $cacheControl['max-age'] = $this->cacheTime;
			$this->setHeader('Cache-Control', $cacheControl);
		}
		foreach ($this->headers as $header => $value){
			if (is_array($value)){
				$tmp = array();
				foreach ($value as $k => $v){
					if (is_numeric($k)){
						$tmp[] = $v;
					}else{
						$tmp[] = "{$k}={$v}";
					}
				}
				$value = implode(';', $tmp);
			}
			header("{$header}:{$value}", true);
		}
	}

	/**
	 * 结束执行
	 * @param boolean $immediately 是否立即退出
	 * @throws QuitException
	 */
	private function end($immediately = false){
		exit();
	}

	/**
	 * 获取文件mime类型
	 * @param string $file
	 * @return string
	 */
	private function getMime($file){
		$mime = array(
			// applications
			'ai' => 'application/postscript', 
			'eps' => 'application/postscript', 
			'exe' => 'application/octet-stream', 
			'doc' => 'application/vnd.ms-word', 
			'xls' => 'application/vnd.ms-excel', 
			'ppt' => 'application/vnd.ms-powerpoint', 
			'pps' => 'application/vnd.ms-powerpoint', 
			'pdf' => 'application/pdf', 
			'xml' => 'application/xml', 
			'odt' => 'application/vnd.oasis.opendocument.text', 
			'swf' => 'application/x-shockwave-flash', 
			// archives
			'gz' => 'application/x-gzip', 
			'tgz' => 'application/x-gzip', 
			'bz' => 'application/x-bzip2', 
			'bz2' => 'application/x-bzip2', 
			'tbz' => 'application/x-bzip2', 
			'zip' => 'application/zip', 
			'rar' => 'application/x-rar', 
			'tar' => 'application/x-tar', 
			'7z' => 'application/x-7z-compressed', 
			// texts
			'txt' => 'text/plain', 
			'php' => 'text/x-php', 
			'html' => 'text/html', 
			'htm' => 'text/html', 
			'js' => 'text/javascript', 
			'css' => 'text/css', 
			'rtf' => 'text/rtf', 
			'rtfd' => 'text/rtfd', 
			'py' => 'text/x-python', 
			'java' => 'text/x-java-source', 
			'rb' => 'text/x-ruby', 
			'sh' => 'text/x-shellscript', 
			'pl' => 'text/x-perl', 
			'sql' => 'text/x-sql', 
			// images
			'bmp' => 'image/x-ms-bmp', 
			'jpg' => 'image/jpeg', 
			'jpeg' => 'image/jpeg', 
			'gif' => 'image/gif', 
			'png' => 'image/png', 
			'tif' => 'image/tiff', 
			'tiff' => 'image/tiff', 
			'tga' => 'image/x-targa', 
			'psd' => 'image/vnd.adobe.photoshop', 
			// audio
			'mp3' => 'audio/mpeg', 
			'mid' => 'audio/midi', 
			'ogg' => 'audio/ogg', 
			'mp4a' => 'audio/mp4', 
			'wav' => 'audio/wav', 
			'wma' => 'audio/x-ms-wma', 
			// video
			'avi' => 'video/x-msvideo', 
			'dv' => 'video/x-dv', 
			'mp4' => 'video/mp4', 
			'mpeg' => 'video/mpeg', 
			'mpg' => 'video/mpeg', 
			'mov' => 'video/quicktime', 
			'wm' => 'video/x-ms-wmv', 
			'flv' => 'video/x-flv', 
			'mkv' => 'video/x-matroska');
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		return isset($mime[$ext]) ? $mime[$ext] : 'application/octet-stream';
	}

	final private function __clone(){
	}
}