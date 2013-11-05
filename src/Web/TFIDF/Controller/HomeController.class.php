<?php

namespace Web\TFIDF\Controller;

use Web\TFIDF\Model\DocumentsModel;
use Web\TFIDF\Dao\DocumentsDao;
use Web\TFIDF\Model\TFIDFModel;
use \Web\TFIDF\Base\Controller;
use BootsPHP\Exception\QuitException;

class HomeController extends Controller {

	public function __construct(){
		parent::__construct();
		$this->view->setTemplatePath(PATH_ROOT . 'Web/TFIDF/View');
	}

	public function indexAction(){
		$this->assign('title', 'TF-IDF');
		$this->show();
	}

	public function learnAction(){
		if ($this->request->isPost()){
			try{
				$title = $this->request->getParam('title');
				$content = $this->request->getParam('content');
				if (!$title){
					$this->error('标题不能为空');
				}
				if (!$content){
					$this->error('正文不能为空');
				}
				$documentModel = new DocumentsModel();
				$docId = $documentModel->addDocument($title, $content);
				if (!$docId){
					$this->error('新建文章失败');
				}
				$tfidfModel = new TFIDFModel();
				if (($result = $tfidfModel->learn($docId, $content)) !== false){
					$this->assign('result', $result);
					$this->show(array('title' => '学习结果'));
				}
			}catch (\Exception $e){
				var_dump($e);
			}
		}
	}
	
	public function banAction(){
		$word = $this->request->getParam('word');
		if ($word){
			$tfidfModel = new TFIDFModel();
			if (($result = $tfidfModel->banWord($word)) !== false){
				$this->success('success');
			}
			$this->error('failed');
		}
		$this->error('empty');
	}
}
