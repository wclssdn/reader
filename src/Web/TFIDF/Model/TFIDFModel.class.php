<?php

namespace Web\TFIDF\Model;
use Resource\Helper\Httpcws;
use Web\TFIDF\Dao\BlackWordDao;
use Web\TFIDF\Dao\DocumentsDao;
use Web\TFIDF\Dao\WordDocTotalDao;
use Resource\Base\Model;
use Web\TFIDF\Dao\WordTFDao;
use Web\TFIDF\Dao\WordIDFDao;
use Web\TFIDF\Dao\DocTotalDao;

class TFIDFModel extends Model {

	public function __construct(){
		$this->tf = new WordTFDao();
		$this->idf = new WordIDFDao();
		$this->wordDocTotal = new WordDocTotalDao();
		$this->docTotal = new DocTotalDao();
	}

	public function learn($docId, $content){
		$httpcws = new Httpcws();
		$words = $httpcws->segment($content);
		if (!$words){
			return false;
		}
		$words = array_filter($words);
		$tf = array_count_values($words);
		$blackWords = $this->getBlackWords();
		$tf = array_diff_key($tf, $blackWords);
		$this->updateWordDocTotal(array_keys($tf));
		arsort($tf);
		$most = null;
		foreach ($tf as $word => &$t){
			$most === null && $most = $t;
			$t /= $most;
		}
		unset($t);
		$wordsDocTotal = $this->getWordsDocTotal($words);
		$docTotal = $this->docTotal->select(array('total'), array())->fetch('total');
		$idf = array();
		foreach ($words as $word){
			!isset($wordsDocTotal[$word]) && $wordsDocTotal[$word] = 1;
			$idf[$word] = log($docTotal / $wordsDocTotal[$word]);
		}
		$this->updateWordsIDF($idf);
		$result = array();
		foreach ($tf as $word => $t){
			$result[$word] = $idf[$word] * $t;
		}
		arsort($result);
		return $result;
	}

	public function banWord($word){
		$banWordDao = new BlackWordDao();
		if ($banWordDao->insert(array('word'), array($word))){
			$this->idf->delete(array('word' => $word));
			$this->tf->delete(array('word' => $word));
			return true;
		}
		return false;
	}

	public function getBlackWords(){
		$banWordDao = new BlackWordDao();
		return $banWordDao->select(array('word'), array())->fetchAll('word', 'word');
	}

	private function updateWordDocTotal(array $words){
		try{
			$values = array();
			foreach ($words as $word){
				$word = $this->wordDocTotal->quote($word);
				$values[] = "({$word}, 1)";
			}
			$values = implode(',', $values);
			$sql = "insert into word_doc_total (word, doc_total) values {$values} on duplicate key update doc_total = doc_total + 1";
			return $this->wordDocTotal->query($sql);
		}catch (\Exception $e){
			var_dump($e);
		}
	}

	private function getWordsDocTotal(array $words){
		return $this->wordDocTotal->select(array('word', 'doc_total'), array(
			'word' => $words))->fetchAll('word', 'doc_total');
	}

	private function updateWordsIDF(array $idfInfo){
		$values = array();
		foreach ($idfInfo as $word => $idf){
			$word = $this->idf->quote($word);
			$values[] = "({$word}, {$idf})";
		}
		$values = implode(',', $values);
		$sql = "insert into idf (word, idf) values {$values} on duplicate key update idf = values(idf)";
		return $this->idf->query($sql);
	}
}