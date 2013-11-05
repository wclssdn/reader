<?php

namespace BootsPHP\Util;

class Url {

	public function getCurrentUrl() {
		return $_SERVER['REQUEST_URI'];
	}

	public function getHost() {
		return $_SERVER['HTTP_HOST'];
	}

	public function getPathinfo() {
		return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	}

	public function getQueryString() {
		return $_SERVER['QUERY_STRING'];
	}
}