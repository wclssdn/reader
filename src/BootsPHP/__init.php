<?php
/**
 * 框架启动文件
 * Example:
 * require PATH_BootsPHP . '__init.php';
 * $configFile = PATH_CONFIG . 'context.conf.php';
 * BootsPHP::getInstance()->execute($configFile);
 * @author Wclssdn
 *
 */
namespace BootsPHP;

define('BootsPHP\PATH_BootsPHP', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('BootsPHP\PATH_ROOT', dirname(PATH_BootsPHP) . DIRECTORY_SEPARATOR);
require_once PATH_BootsPHP . 'Autoload.class.php';
$autoload = Autoload::getInstance(PATH_ROOT);
$autoload->hold();

