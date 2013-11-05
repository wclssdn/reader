<?php

namespace BootsPHP;

/**
 * 数据访问层
 * TODO pdo操作结果的判断方法 try catch?
 * @author Wclssdn
 */
class Dao {

	/**
	 * 表名
	 * @var string
	 */
	protected $tableName;

	/**
	 * PDO对象
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * PDO从库对象数组
	 * @var \PDO
	 */
	protected $slave;

	/**
	 * 解析条件变量得到的绑定值
	 * @var array
	 */
	protected $bindValues = array();

	/**
	 * 缓存数组
	 * @var \PDOStatement
	 */
	protected $statements = array();

	/**
	 * 最后一次执行的statement
	 * @var \PDOStatement
	 */
	protected $lastStatement;

	/**
	 * 执行的SQL记录
	 * @var array
	 */
	protected $sqls = array();

	/**
	 * 异常信息
	 * @var \Exception
	 */
	protected $exception;

	public function __construct(\PDO $pdo){
		$this->pdo = $pdo;
	}

	/**
	 * 添加从库
	 * @param \PDO $pdo
	 * @return \BootsPHP\Dao\Dao
	 */
	protected function addSlave(\PDO $slave){
		$this->slave && in_array($slave, $this->slave) || $this->slave[] = $slave;
		return $this;
	}

	/**
	 * 获取单行
	 * @param $field 指定某列的值
	 * @return array string
	 */
	public function fetch($field = null){
		if (!$this->lastStatement){
			return false;
		}
		if ($field){
			$result = $this->lastStatement->fetch();
			if (isset($result[$field])){
				return $result[$field];
			}
			return null;
		}
		return $this->lastStatement->fetch();
	}

	/**
	 * 获取所有行
	 * @param string $key 使用哪列作为第一维数组的键
	 * @param string $val 使用哪列作为数组的值, 退化为一维数组
	 * @return boolean array
	 */
	public function fetchAll($key = null, $val = null){
		if (!$this->lastStatement){
			return false;
		}
		if ($key){
			$result = $this->lastStatement->fetchAll();
			if (!$result){
				return $result;
			}
			$final = array();
			foreach ($result as $v){
				if (!isset($v[$key]) || !isset($v[$val])){
					throw new \Exception('there is not such a key or val');
				}
				$final[$v[$key]] = $val ? $v[$val] : $v;
			}
			return $final;
		}
		return $this->lastStatement->fetchAll();
	}

	/**
	 * 获取多行
	 * @param array $fields
	 * @param array $condition
	 * @param array $order
	 * @param number $offset
	 * @param number $limit
	 * @param unknown_type $group
	 * @param unknown_type $having
	 * @return Dao
	 */
	public function select(array $fields, array $condition, array $order = array(), $page = 0, $limit = 100, $group = '', $having = ''){
		$condition = $this->prepareCondition($condition);
		$fields = $this->prepareFields($fields);
		$group = $this->prepareGroup($group);
		$having = $this->prepareHaving($having);
		$order = $this->prepareOrder($order);
		$limit = $this->prepareLimit($page, $limit);
		$sql = "SELECT {$fields} FROM `{$this->tableName}`{$condition}{$group}{$having}{$order}{$limit}";
		$this->execute($sql, true);
		return $this;
	}

	/**
	 * 插入
	 * @param array $fields
	 * @param array $values
	 * @param string $options ignore/replace
	 */
	public function insert(array $fields, array $values, $options = null){
		if (empty($fields) || count($fields) != count($values)){
			return false;
		}
		$fields = $this->prepareFields($fields);
		$values = $this->prepareValues($values);
		$sql = "INSERT INTO {$this->tableName} ({$fields}) VALUES ({$values})";
		$stat = $this->execute($sql);
		if ($stat === false){
			return false;
		}
		return $stat->errorCode() === '00000';
	}

	/**
	 * 更新
	 * @param array $fields
	 * @param array $values
	 * @param array $condition
	 * @return boolean
	 */
	public function update(array $fields, array $values, array $condition){
		if (empty($fields) || count($fields) != count($values)){
			return false;
		}
		$values = $this->prepareValues($values, $fields);
		$condition = $this->prepareCondition($condition);
		$sql = "UPDATE {$this->tableName} SET {$values}{$condition}";
		$stat = $this->execute($sql);
		if ($stat === false){
			return false;
		}
		return $stat->errorCode() === '00000';
	}

	/**
	 * 删除
	 * @param array $condition
	 * @return boolean
	 */
	public function delete(array $condition){
		$sql = "DELETE FROM `{$this->tableName}`" . $this->prepareCondition($condition);
		$stat = $this->execute($sql);
		if ($stat === false){
			return false;
		}
		return $stat->errorCode() === '00000';
	}

	public function beginTransaction(){
		return $this->pdo->beginTransaction();
	}

	public function commit(){
		return $this->pdo->commit();
	}

	public function rollback(){
		return $this->pdo->rollBack();
	}

	public function getLastId(){
		return $this->pdo->lastInsertId();
	}

	/**
	 * 获取影响行数
	 * @return boolean
	 */
	public function getAffectedRows(){
		return $this->lastStatement ? $this->lastStatement->rowCount() : false;
	}

	/**
	 * 获取所有执行过的SQL
	 * @return array
	 */
	public function getSqls(){
		return $this->sqls;
	}

	/**
	 * 获取最后一条SQL
	 * @return string
	 */
	public function getLastSql(){
		return end($this->sqls);
	}

	/**
	 * 获取异常信息
	 * @return Exception
	 */
	public function getException(){
		return $this->exception;
	}

	/**
	 * 将字符串用于SQL查询
	 * @param string $string
	 * @return string
	 */
	public function quote($string){
		return $this->pdo->quote($string);
	}

	/**
	 * 执行自定义SQL
	 * @param string $sql
	 * @return boolean
	 */
	public function query($sql){
		$stat = $this->execute($sql);
		return $stat->errorCode() === '00000';
	}

	/**
	 * 执行SQL
	 * @param string $sql
	 * @param boolean $slave 使用从库
	 * @return PDOStatement
	 */
	private function execute($sql, $slave = false){
		$this->sqls[] = $sql;
		$key = crc32($sql);
		if (isset($this->statements[$key])){
			$stat = $this->statements[$key];
		}else{
			if ($slave && $pdo = $this->getSlave()){
			}else{
				$pdo = $this->pdo;
			}
			try{
				$stat = $this->statements[$key] = $pdo->prepare($sql);
			}catch (\Exception $e){
				$this->exception = $e;
				return false;
			}
		}
		$this->lastStatement = $stat;
		$stat->execute($this->bindValues);
		$this->bindValues = array();
		return $stat;
	}

	/**
	 * 随机获取从库
	 * @return Ambigous <boolean, PDO>
	 */
	private function getSlave(){
		return empty($this->slave) ? false : $this->slave[array_rand($this->slave)];
	}

	/**
	 * fields
	 * @param array $fields
	 * @return string
	 */
	private function prepareFields(&$fields){
		if (empty($fields)){
			return '*';
		}
		if (is_array($fields)){
			array_walk($fields, array($this, 'prepareFields'));
			$fields = implode(',', $fields);
			return $fields;
		}else{
			$fields = trim($fields);
			$fixed = false;
			if (stripos($fields, ' AS ')){
				if (strpos($fields, '(')){
					$fields = preg_replace('#^(.+?)\s+AS\s+([a-z0-9_]+)$#i', '\1 AS `\2`', $fields);
				}else{
					$fields = preg_replace('#^([a-z0-9_]+)\s+AS\s+([a-z0-9_]+)$#i', '`\1` AS `\2`', $fields);
				}
				$fixed = true;
			}
			if (strpos($fields, '(')){
				$fields = preg_replace('#^(.+?)\(([^*]+?)\)(.*?)$#i', '\1(`\2`)\3', $fields);
				$fixed = true;
			}
			if (!$fixed){
				$fields = "`{$fields}`";
			}
			return $fields;
		}
	}

	/**
	 * value
	 * @param array $values
	 * @param array $fields
	 * @return string
	 */
	private function prepareValues(array $values, array $fields = array()){
		$result = array();
		foreach ($values as $value){
			$randomStr = 'f_' . substr(uniqid(), -5);
			$this->bindValues[$randomStr] = $value === null ? '' : $value;
			if ($fields){
				$tmp = array_shift($fields);
				$field = $this->prepareFields($tmp);
				$result[] = "{$field} = :{$randomStr}";
			}else{
				$result[] = ":{$randomStr}";
			}
		}
		return implode(',', $result);
	}

	/**
	 * where
	 * @param mixed $condition
	 * @param boolean $first 非递归调用标志
	 * @throws \Exception
	 * @return string
	 */
	private function prepareCondition(array $condition, $first = true){
		if (empty($condition)){
			return '';
		}
		if ($first && !is_numeric(key($condition))){
			$condition = array($condition);
		}
		foreach ($condition as $field => $value){
			// 数字索引数组按照OR关系算
			if (is_numeric($field)){
				$or[] = $this->prepareCondition($value, false);
			}else{ // 非数字索引数组按AND关系算
				$randomStr = 'f_' . substr(uniqid(), -5);
				if (is_array($value)){
					$i = 0;
					foreach ($value as $v){
						$this->bindValues[$randomStr . '_' . $i] = $v;
						$randomStrs[] = ":{$randomStr}_{$i}";
						++$i;
					}
					$randomStr = implode(',', $randomStrs);
					$condition[$field] = "`{$field}` IN ({$randomStr})";
				}elseif (is_scalar($value)){
					if (strpos($field, ' ')){
						$parts = explode(' ', $field, 2);
						$parts[1] = strtoupper($parts[1]);
						$condition[$field] = "`{$parts[0]}` {$parts[1]} :{$randomStr}";
					}else{
						$condition[$field] = "`{$field}` = :{$randomStr}";
					}
					$this->bindValues[$randomStr] = $value;
				}else{
					throw new \Exception('type not support');
				}
			}
		}
		if ($first){
			return ' WHERE ' . implode(' OR ', array_filter($or));
		}
		return implode(' AND ', $condition);
	}

	/**
	 * group by
	 * 待完善
	 * @param string $group
	 * @return string
	 */
	private function prepareGroup($group){
		return $group ? " GROUP BY {$group}" : '';
	}

	/**
	 * having
	 * 待完善
	 * @param string $having
	 * @return string
	 */
	private function prepareHaving($having){
		return $having ? " HAVING {$having}" : '';
	}

	/**
	 * order
	 * @param mixed $order
	 * @return string
	 */
	private function prepareOrder(array $order){
		if (!$order){
			return '';
		}
		foreach ($order as $field => $type){
			$type = strtoupper($type);
			if ($type !== 'DESC' && $type !== 'ASC'){
				continue;
			}
			$order[$field] = "`{$field}` {$type}";
		}
		return ' ORDER BY ' . implode(',', $order);
	}

	/**
	 * limit
	 * @param number $page
	 * @param number $limit
	 * @return string
	 */
	private function prepareLimit($page, $limit){
		if (!$limit){
			return '';
		}
		$page = max(0, $page - 1);
		$offset = $page * $limit;
		return ' LIMIT ' . $offset . ', ' . max(1, intval($limit));
	}
}