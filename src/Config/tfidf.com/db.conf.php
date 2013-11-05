<?php
if (defined('SAE_MYSQL_DB')){
	return array(
		'*' => array(
			'master' => array(
				'host' => SAE_MYSQL_HOST_M, 
				'port' => SAE_MYSQL_PORT, 
				'user' => SAE_MYSQL_USER, 
				'pass' => SAE_MYSQL_PASS, 
				'db' => SAE_MYSQL_DB), 
			'slave' => array(
				'host' => SAE_MYSQL_HOST_S, 
				'port' => SAE_MYSQL_PORT, 
				'user' => SAE_MYSQL_USER, 
				'pass' => SAE_MYSQL_PASS, 
				'db' => SAE_MYSQL_DB)));
}else{
	return array(
		'*' => array(
			'master' => array(
				'host' => 'localhost', 
				'port' => 3306, 
				'user' => 'tfidf', 
				'pass' => '123456', 
				'db' => 'tfidf'), 
			'slave' => array(
				'host' => 'localhost', 
				'port' => 3306, 
				'user' => 'tfidf', 
				'pass' => '123456', 
				'db' => 'tfidf')),
	/* end */
	);
}