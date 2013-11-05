<?php
/**
 * 全局配置文件
 * router:路由配置
 * db:数据库配置
 * define:常量定义
 */
return array(
	'router' => include PATH_CONFIG . 'router.conf.php', 
	'define' => PATH_CONFIG . 'define.conf.php'
/* end */
);