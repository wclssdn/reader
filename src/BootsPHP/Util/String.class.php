<?php

namespace BootsPHP\Util;

class String {

	private function __construct(){
	}

	public static function isEmpty($str){
		return (boolean)preg_match('#^\s*$#', $str);
	}

	public static function isAlpha($str){
		return (boolean)preg_match('#^[a-z]+$#i', $str);
	}

	public static function isNumeric($str){
		return (boolean)preg_match('#^\d+$#i', $str);
	}

	public static function isAlphaNumeric($str){
		return (boolean)preg_match('#^[a-z\d]+$#i', $str);
	}

	public static function length($str){
		return mb_strlen($str, 'utf8');
	}

	private final function __clone(){
	}
}