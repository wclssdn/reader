<?php

namespace Web\TFIDF\Model;

use Web\TFIDF\Dao\DocTotalDao;
use Web\TFIDF\Dao\DocumentsDao;
use Resource\Base\Model;
use Web\TFIDF\Dao\WordTFDao;
use Web\TFIDF\Dao\WordIDFDao;

class DocumentsModel extends Model {

	public function __construct(){
		$this->dao = new DocumentsDao();
	}

	public function addDocument($title, $content){
		if ($this->dao->insert(array('title', 'content'), array(
			$title, 
			$content))){
			$id = $this->dao->getLastId();
			$this->incrementDocumentsTotal();
			return $id;
		}
		return false;
	}

	private function incrementDocumentsTotal($inc = 1){
		$docTotalDao = new DocTotalDao();
		$total = $docTotalDao->select(array('total'), array())->fetch();
		if ($total === false){
			return $docTotalDao->insert(array('total'), array($inc));
		}
		return $docTotalDao->update(array('total'), array($total['total'] + $inc), array());
	}
}