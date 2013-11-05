<?php
namespace Resource\Helper;

class Httpcws {

	private $server;

	public function __construct($server = '127.0.0.1:1985'){
		$this->server = $server;
	}

	/**
	 * 分词
	 * @param string $string
	 * @param string $fromCharset gbk/gb2312/utf8/...
	 * @return string
	 */
	public function segment($string, $fromCharset = 'utf8'){
		if ($fromCharset != 'gbk'){
			$string = mb_convert_encoding($string, 'gbk', $fromCharset);
		}
		$opts = array(
			'http' => array(
				'method' => "POST", 
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-length:" . strlen($string) . "\r\n\r\n", 
				'content' => urlencode($string)));
		$context = stream_context_create($opts);
		$result = file_get_contents("http://{$this->server}", false, $context);
		$result = mb_convert_encoding($result, $fromCharset, 'gbk');
		return explode(' ', $result);
	}
}