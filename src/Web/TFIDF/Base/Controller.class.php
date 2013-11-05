<?php

namespace Web\TFIDF\Base;

use Resource\Base\HtmlController;
use BootsPHP\Response;

class Controller extends HtmlController {

	public function __construct(){
		parent::__construct();
		$this->view->setTemplatePath(PATH_ROOT . 'Web/TFIDF/View');
	}
}