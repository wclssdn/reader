<?php

namespace Web\TFIDF\Dao;

use Resource\Base\Dao;
use BootsPHP\Config;

class WordTFDao extends Dao {

	protected $tableName = 'tf';
	
	/* (non-PHPdoc)
	 * @see \Resource\Base\Dao::getDbConfig()
	 */
	protected function getDbConfig($tableName, $dbConfig = array()){
		$config = new Config(PATH_CONFIG . 'tfidf.com/db.conf.php');
		return parent::getDbConfig($tableName, $config->getAll());
	}
}