<?php

namespace Resource\Model;

use Resource\Base\Model;
use Resource\Dao\UserDao;

class UserModel extends Model {

	public function __construct(){
		if ($this->dao === null){
			$this->dao = new UserDao();
		}
	}

	public function getUser($id){
		return $this->dao->select(array(), array('id' => $id))->fetch();
	}

	public function getUserByName($username){
		return $this->dao->select(array(), array('username' => $username))->fetch();
	}

	public function getUserList($page, $size){
		return $this->dao->select(array(), array(), array('id' => 'desc'), $page, $size)->fetchAll();
	}

	public function addUser($username, $password, $ip){
		if ($this->dao->insert(array(
			'username', 
			'password', 
			'reg_time', 
			'reg_ip'), array($username, $this->getSavedPassword($password), date('Y-m-d H:i:s'), $ip))){
			return $this->dao->getLastId();
		}
		return false;
	}

	public function isBlackUsername($username){
		$blackUsername = array('admin', 'manager', 'administrator', 'root');
		return in_array($username, $blackUsername, true);
	}

	public function editUser($id, $title, $content){
		if (($User = $this->dao->select(array(), array('id' => $id))->fetch()) !== false){
			if ($this->dao->update(array('title', 'content'), array(
				$title, 
				$content), array('id' => $id)) === false){
				return false;
			}
			return $this->dao->getAffectedRows();
		}
		return false;
	}

	public function delUser($id){
		return $this->dao->delete(array('id' => $id));
	}

	public function isRightPassword($password, $userPassword){
		return $userPassword == str_rot13($password);
	}
	
	private function getSavedPassword($userPassword){
		return str_rot13($userPassword);
	}
}