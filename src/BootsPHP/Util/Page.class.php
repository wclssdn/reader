<?php

namespace BootsPHP\Util;

class Page {

	private $page;

	private $size;

	private $total;

	private $totalPages;

	private $urlParamName;

	private $maxPagesShow;

	private $firstPageInfo;

	private $lastPageInfo;

	private $pages = array();

	/**
	 * 初始化分页对象
	 * @param number $page 当前页数
	 * @param number $size 每页显示条数
	 * @param number $total 总条数
	 * @param number $maxPagesShow 返回的分页数组最多包含多少个元素. 例如 当前页数为6,限制为3,则返回:5,6,7
	 * @param string $urlParamName URL中分页参数名, 默认为page
	 */
	public function __construct($page, $size, $total, $maxPagesShow = 10, $urlParamName = 'page'){
		$this->size = $this->setSize($size);
		$this->total = $this->setTotal($total);
		$this->totalPages = $this->setTotalPages($size, $total);
		$this->page = $this->setPage($page, $this->totalPages);
		$this->urlParamName = $this->setUrlParamName($urlParamName);
		$this->maxPagesShow = $this->setMaxPagesShow($maxPagesShow);
		$this->pages = $this->setPages();
	}

	private function setPage($page, $totalPages){
		return max(1, min($page, $totalPages));
	}

	/**
	 * 获取当前页数
	 * @return number
	 */
	public function getPage(){
		return $this->page;
	}

	private function setSize($size){
		return max(1, $size);
	}

	/**
	 * 获取每页显示条数
	 * @return number
	 */
	public function getSize(){
		return $this->size;
	}

	private function setTotal($total){
		return max(0, $total);
	}

	/**
	 * 获取数据总条数
	 * @return number
	 */
	public function getTotal(){
		return $this->total;
	}

	private function setTotalPages($size, $total){
		return max(1, ceil($total / $size));
	}

	/**
	 * 获取总页数
	 * @return number
	 */
	public function getTotalPages(){
		return $this->totalPages;
	}

	private function setUrlParamName($urlParamName){
		return trim($urlParamName);
	}

	private function setMaxPagesShow($maxPagesShow){
		return max(1, $maxPagesShow);
	}

	/**
	 * 是否为首页
	 * @return boolean
	 */
	public function isFirstPage(){
		return $this->page == 1;
	}

	/**
	 * 是否为当前页
	 * @param number $page
	 * @return boolean
	 */
	public function isCurrentPage($page){
		return $page == $this->page;
	}

	/**
	 * 是否为尾页
	 * @return boolean
	 */
	public function isLastPage(){
		if ($this->totalPages == 0){
			return true;
		}
		return $this->page >= $this->totalPages;
	}

	public function getFirstPage($key = 'url'){
		return $key ? (isset($this->firstPageInfo[$key]) ? $this->firstPageInfo[$key] : $this->firstPageInfo) : '';
	}

	public function getLastPage($key = 'url'){
		return $key ? (isset($this->lastPageInfo[$key]) ? $this->lastPageInfo[$key] : $this->lastPageInfo) : '';
	}

	private function setPages(){
		$query = array();
		parse_str($_SERVER['QUERY_STRING'], $query);
		if ($this->totalPages > $this->maxPagesShow){
			if ($this->totalPages - $this->page < $this->maxPagesShow){
				$x = max(0, $this->maxPagesShow - $this->totalPages + $this->page - 2);
			}else{
				$x = 0;
			}
			$before = floor($this->maxPagesShow / 2) + $x;
			if ($this->page <= $before){
				$y = $before - $this->page + 1;
			}else{
				$y = 0;
			}
			$end = max(0, $this->maxPagesShow - $before - 1) + $y;
			$startPos = max(1, $this->page - $before);
			$endPos = min($this->totalPages, $this->page + $end);
		}else{
			$startPos = 1;
			$endPos = $this->totalPages;
		}
		$pages = array();
		for ($i = 1; $i <= $this->totalPages; ++$i){
			$pageInfo = array(
				'page' => $i, 
				'url' => $this->getUrl($_SERVER['SCRIPT_URL'], $query, $i));
			if ($i == 1){
				$this->firstPageInfo = $pageInfo;
			}
			if ($i == $this->totalPages){
				$this->lastPageInfo = $pageInfo;
			}
			if ($i >= $startPos && $i <= $endPos){
				$pages[$i] = $pageInfo;
			}
		}
		return $pages;
	}

	/**
	 * 获取分页数据数组(二维数组)
	 * @return array (page => array('page' => 1, 'url' => ''), ...);
	 */
	public function getPages(){
		return $this->pages;
	}

	private function getUrl($url, $query, $page){
		$query[$this->urlParamName] = $page;
		$query = http_build_query($query);
		return "{$url}?{$query}";
	}
}