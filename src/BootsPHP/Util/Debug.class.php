<?php

namespace BootsPHP\Util;

class Debug {
	const DISPLAY_TYPE_HTML = 1;
	const DISPLAY_TYPE_TEXT = 2;
	const DISPLAY_TYPE_JOSN = 3;
	const DISPLAY_TYPE_HEADER = 4;

	private static $displayType = self::DISPLAY_TYPE_HTML;

	public static function setDiaplayType($displayType){
		if (in_array($displayType, array(
			self::DISPLAY_TYPE_TEXT, 
			self::DISPLAY_TYPE_HTML, 
			self::DISPLAY_TYPE_JOSN, 
			self::DISPLAY_TYPE_HEADER), true)){
			self::$displayType = $displayType;
		}
	}

	public static function varDump($var){
		switch (self::$displayType){
			case self::DISPLAY_TYPE_TEXT:
				for ($i = 0; $i < func_num_args(); ++$i){
					var_dump(func_get_arg($i));
				}
				break;
			case self::DISPLAY_TYPE_HTML:
				break;
			case self::DISPLAY_TYPE_JOSN:
				break;
			case self::DISPLAY_TYPE_HEADER:
				break;
		}
	}
}