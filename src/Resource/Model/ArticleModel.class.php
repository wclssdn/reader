<?php
/**
 * TODO 根据此模型创建自动生成脚本
 * 需要设置namespace, Dao目录, namespace
 * 方法列表. 
 * 
 * 自动:
 * 读取表结构, 主键信息
 * 可选生成多个方法 , 需要条件不相同
 * getListByXxx, updateYyyByPK
 * 读取表注释, 写入到函数注释上
 * 
 * feature:
 * 表关联查询
 * 自动生成表关联查询
 */
namespace Resource\Model;

use Resource\Base\Model;
use Resource\Dao\ArticleDao;

class ArticleModel extends Model {

	public function getArticle($id){
		$dao = new ArticleDao();
		return $dao->select(array(), array('id' => $id))->fetch();
	}

	public function getArticleList($page, $size){
		$dao = new ArticleDao();
		return $dao->select(array('id', 'title', 'content as html'), array(
			'id >' => 0), array('id' => 'desc', 'title' => 'asc'))->fetchAll();
	}

	public function searchArticle($keywords, $page, $size){
		$dao = new ArticleDao();
		return $dao->select(array(), array(
			array('title like' => "%{$keywords}%"), 
			array('content like' => "%{$keywords}%")), array('id' => 'desc'))->fetchAll();
	}

	public function addArticle($title, $content){
		$dao = new ArticleDao();
		if ($dao->insert(array('title', 'content'), array($title, $content))){
			return $dao->getLastId();
		}
		return false;
	}

	public function editArticle($id, $title, $content){
		$dao = new ArticleDao();
		if (($article = $dao->select(array(), array('id' => $id))->fetch()) !== false){
			if ($dao->update(array('title', 'content'), array($title, $content), array(
				'id' => $id)) === false){
				return false;
			}
			return $dao->getAffectedRows();
		}
		return false;
	}

	public function delArticle($id){
		$dao = new ArticleDao();
		return $dao->delete(array('id' => $id));
	}
}