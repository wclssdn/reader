<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use BootsPHP\BootsPHP;
date_default_timezone_set('PRC');
define('PATH_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('PATH_CONFIG', PATH_ROOT . 'Config/');
define('PATH_BootsPHP', PATH_ROOT . 'BootsPHP/');
require PATH_BootsPHP . '__init.php';
$configFile = PATH_CONFIG . 'context.conf.php';
try {
	BootsPHP::getInstance()->execute($configFile);
}catch (Exception $e){
	if ($e->getCode() == -1){
		exit(-1);
	}
	echo $e->getMessage();
}