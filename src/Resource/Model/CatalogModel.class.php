<?php

namespace Resource\Model;

use Resource\Base\Model;
use Resource\Dao\CatalogDao;

class CatalogModel extends Model {

	protected $levelLength = 3;

	public function getCatalog($id){
		$dao = new CatalogDao();
		return $dao->select(array(), array('id' => $id))->fetch();
	}

	public function getCatalogList($pid){
		$dao = new CatalogDao();
		return $dao->select(array('id', 'name'), array('pid >' => $pid))->fetchAll();
	}

	public function addCatalog($name, $pid = 0){
		$dao = new CatalogDao();
		if ($dao->insert(array('name', 'pid'), array($name, $pid)) !== false){
			return $dao->getLastId();
		}
		return false;
	}

	public function editCatalog($id, $name, $pid){
		$dao = new CatalogDao();
		if (($catalog = $dao->select(array(), array('id' => $id))->fetch()) !== false){
			if ($dao->update(array('name', 'pid'), array($name, $pid), array(
				'id' => $id)) !== false){
				return $dao->getAffectedRows();
			}
		}
		return false;
	}

	public function delCatalog($id){
		$dao = new CatalogDao();
		return $dao->delete(array('id' => $id));
	}
}