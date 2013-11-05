<?php

namespace BootsPHP\Util;

class TemplateHelper {

	/**
	 * 安全输出字符串到HTML代码中
	 * @param string $var 模板变量
	 * @param string $default 默认值
	 * @param boolean $return
	 * @return string null
	 */
	public static function S($var, $default = '', $return = false){
		if ($return){
			return $var ? htmlspecialchars($var) : htmlspecialchars($default);
		}
		echo $var ? htmlspecialchars($var) : htmlspecialchars($default);
	}

	/**
	 * 白名单标签方式输出HTML代码
	 * @param string $html
	 * @param array $config
	 * @param boolean $return
	 * @return string null
	 */
	public static function W($html, $config, $return = false){
	}

	/**
	 * 根据表达式决定输出trueText还是falseText
	 * @param boolean $expresion 表达式
	 * @param string $trueText
	 * @param string $falseText
	 * @param boolean $return
	 * @return string null
	 */
	public static function E($expresion, $trueText, $falseText, $return = false){
		if ($return){
			return $expresion ? $trueText : $falseText;
		}
		echo $expresion ? $trueText : $falseText;
	}
}
