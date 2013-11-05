<?php

namespace BootsPHP;

/**
 * PDO数据库类
 * 懒加载, 支持对象缓存
 * @author Wclssdn
 */
class Pdo extends \PDO {

	/**
	 * 数据库连接选项
	 * @var array
	 */
	private $options;

	/**
	 * dsn
	 * @var string
	 */
	private $dsn;

	/**
	 * 用户名
	 * @var string
	 */
	private $username;

	/**
	 * 密码
	 * @var string
	 */
	private $password;

	/**
	 * 初始化方法, 不建议调用, 此方式无法使用对象缓存
	 * @param string $host
	 * @param number $port
	 * @param string $dbName
	 * @param string $username
	 * @param string $password
	 * @param string $charset
	 * @param string $driver
	 * @param array $options
	 */
	public function __construct($host, $port, $dbName, $username, $password, $charset = 'utf8', $driver = 'mysql', array $options = array()){
		$charset || $charset = 'utf8';
		$driver || $driver = 'mysql';
		$this->options = array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, 
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
		$options && $this->options = array_merge($this->options, $options);
		$this->dsn = $this->getDsn($driver, $host, $port, $dbName, $charset);
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * 带缓存的实例化方法
	 * @param string $host
	 * @param number $port
	 * @param string $dbName
	 * @param string $username
	 * @param string $password
	 * @param string $charset
	 * @param string $driver
	 * @param array $options
	 * @return \BootsPHP\Pdo
	 */
	public static function getInstance($host, $port, $dbName, $username, $password, $charset = 'utf8', $driver = 'mysql', array $options = array()){
		static $instance = array();
		$key = crc32(var_export(func_get_args(), true));
		if (!isset($instance[$key])){
			$instance[$key] = new self($host, $port, $dbName, $username, $password, $charset, $driver, $options);
		}
		return $instance[$key];
	}

	/**
	 * 连接数据库
	 * @throws \PdoException
	 * @return \PDO
	 */
	public function connect(){
		static $connected = array();
		$key = crc32(var_export($this, true));
		if (isset($connected[$key]) && $connected[$key] instanceof \Exception){
			throw new \Exception($connected[$key]->getMessage(), (int)$connected[$key]->getCode());
		}
		if (!isset($connected[$key])){
			try{
				parent::__construct($this->dsn, $this->username, $this->password, $this->options);
			}catch (\PDOException $e){
				$connected[$key] = $e;
				throw new \Exception($e->getMessage(), (int)$e->getCode());
			}
			$connected[$key] = true;
		}
		return $this;
	}

	public function prepare($statement, $driver_options = array()){
		$this->connect();
		return parent::prepare($statement, $driver_options);
	}

	/**
	 * Initiates a transaction
	 * @link http://www.php.net/manual/en/pdo.begintransaction.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function beginTransaction(){
		$this->connect();
		return parent::beginTransaction();
	}

	/**
	 * Commits a transaction
	 * @link http://www.php.net/manual/en/pdo.commit.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function commit(){
		$this->connect();
		return parent::commit();
	}

	/**
	 * Rolls back a transaction
	 * @link http://www.php.net/manual/en/pdo.rollback.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function rollBack(){
		$this->connect();
		return parent::rollBack();
	}

	/**
	 * Set an attribute
	 * @link http://www.php.net/manual/en/pdo.setattribute.php
	 * @param attribute int
	 * @param value mixed
	 * @return bool Returns true on success or false on failure.
	 */
	public function setAttribute($attribute, $value){
		$this->connect();
		return parent::setAttribute($attribute, $value);
	}

	/**
	 * Execute an SQL statement and return the number of affected rows
	 * @link http://www.php.net/manual/en/pdo.exec.php
	 * @param statement string <p>
	 * The SQL statement to prepare and execute.
	 * </p>
	 * <p>
	 * Data inside the query should be properly escaped.
	 * </p>
	 * @return int PDO::exec returns the number of rows that were modified
	 * or deleted by the SQL statement you issued. If no rows were affected,
	 * PDO::exec returns 0.
	 * </p>
	 * &return.falseproblem;
	 * <p>
	 * The following example incorrectly relies on the return value of
	 * PDO::exec, wherein a statement that affected 0 rows
	 * results in a call to die:
	 * exec() or die(print_r($db->errorInfo(), true));
	 * ?>
	 * ]]>
	 */
	public function exec($statement){
		$this->connect();
		return parent::exec($statement);
	}

	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object
	 * @link http://www.php.net/manual/en/pdo.query.php
	 * @param statement string <p>
	 * The SQL statement to prepare and execute.
	 * </p>
	 * <p>
	 * Data inside the query should be properly escaped.
	 * </p>
	 * @return PDOStatement PDO::query returns a PDOStatement object, or false
	 * on failure.
	 */
	public function query($statement){
		$this->connect();
		return parent::query($statement);
	}

	/**
	 * Returns the ID of the last inserted row or sequence value
	 * @link http://www.php.net/manual/en/pdo.lastinsertid.php
	 * @param name string[optional] <p>
	 * Name of the sequence object from which the ID should be returned.
	 * </p>
	 * @return string If a sequence name was not specified for the name
	 * parameter, PDO::lastInsertId returns a
	 * string representing the row ID of the last row that was inserted into
	 * the database.
	 * </p>
	 * <p>
	 * If a sequence name was specified for the name
	 * parameter, PDO::lastInsertId returns a
	 * string representing the last value retrieved from the specified sequence
	 * object.
	 * </p>
	 * <p>
	 * If the PDO driver does not support this capability,
	 * PDO::lastInsertId triggers an
	 * IM001 SQLSTATE.
	 */
	public function lastInsertId($name = null){
		$this->connect();
		return parent::lastInsertId($name);
	}

	/**
	 * Fetch the SQLSTATE associated with the last operation on the database handle
	 * @link http://www.php.net/manual/en/pdo.errorcode.php
	 * @return mixed a SQLSTATE, a five characters alphanumeric identifier defined in
	 * the ANSI SQL-92 standard. Briefly, an SQLSTATE consists of a
	 * two characters class value followed by a three characters subclass value. A
	 * class value of 01 indicates a warning and is accompanied by a return code
	 * of SQL_SUCCESS_WITH_INFO. Class values other than '01', except for the
	 * class 'IM', indicate an error. The class 'IM' is specific to warnings
	 * and errors that derive from the implementation of PDO (or perhaps ODBC,
	 * if you're using the ODBC driver) itself. The subclass value '000' in any
	 * class indicates that there is no subclass for that SQLSTATE.
	 * </p>
	 * <p>
	 * PDO::errorCode only retrieves error codes for operations
	 * performed directly on the database handle. If you create a PDOStatement
	 * object through PDO::prepare or
	 * PDO::query and invoke an error on the statement
	 * handle, PDO::errorCode will not reflect that error.
	 * You must call PDOStatement::errorCode to return the error
	 * code for an operation performed on a particular statement handle.
	 * </p>
	 * <p>
	 * Returns &null; if no operation has been run on the database handle.
	 */
	public function errorCode(){
		$this->connect();
		return parent::errorCode();
	}

	/**
	 * Fetch extended error information associated with the last operation on the database handle
	 * @link http://www.php.net/manual/en/pdo.errorinfo.php
	 * @return array PDO::errorInfo returns an array of error information
	 * about the last operation performed by this database handle. The array
	 * consists of the following fields:
	 * <tr valign="top">
	 * <td>Element</td>
	 * <td>Information</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>0</td>
	 * <td>SQLSTATE error code (a five characters alphanumeric identifier defined
	 * in the ANSI SQL standard).</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>1</td>
	 * <td>Driver-specific error code.</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>2</td>
	 * <td>Driver-specific error message.</td>
	 * </tr>
	 * </p>
	 * <p>
	 * If the SQLSTATE error code is not set or there is no driver-specific
	 * error, the elements following element 0 will be set to &null;.
	 * </p>
	 * <p>
	 * PDO::errorInfo only retrieves error information for
	 * operations performed directly on the database handle. If you create a
	 * PDOStatement object through PDO::prepare or
	 * PDO::query and invoke an error on the statement
	 * handle, PDO::errorInfo will not reflect the error
	 * from the statement handle. You must call
	 * PDOStatement::errorInfo to return the error
	 * information for an operation performed on a particular statement handle.
	 */
	public function errorInfo(){
		$this->connect();
		return parent::errorInfo();
	}

	/**
	 * Retrieve a database connection attribute
	 * @link http://www.php.net/manual/en/pdo.getattribute.php
	 * @param attribute int <p>
	 * One of the PDO::ATTR_* constants. The constants that
	 * apply to database connections are as follows:
	 * PDO::ATTR_AUTOCOMMIT
	 * PDO::ATTR_CASE
	 * PDO::ATTR_CLIENT_VERSION
	 * PDO::ATTR_CONNECTION_STATUS
	 * PDO::ATTR_DRIVER_NAME
	 * PDO::ATTR_ERRMODE
	 * PDO::ATTR_ORACLE_NULLS
	 * PDO::ATTR_PERSISTENT
	 * PDO::ATTR_PREFETCH
	 * PDO::ATTR_SERVER_INFO
	 * PDO::ATTR_SERVER_VERSION
	 * PDO::ATTR_TIMEOUT
	 * </p>
	 * @return mixed A successful call returns the value of the requested PDO attribute.
	 * An unsuccessful call returns null.
	 */
	public function getAttribute($attribute){
		$this->connect();
		return parent::getAttribute($attribute);
	}

	/**
	 * Quotes a string for use in a query.
	 * @link http://www.php.net/manual/en/pdo.quote.php
	 * @param string string <p>
	 * The string to be quoted.
	 * </p>
	 * @param parameter_type int[optional] <p>
	 * Provides a data type hint for drivers that have alternate quoting styles.
	 * </p>
	 * @return string a quoted string that is theoretically safe to pass into an
	 * SQL statement. Returns false if the driver does not support quoting in
	 * this way.
	 */
	public function quote($string, $parameter_type = null){
		$this->connect();
		return parent::quote($string, $parameter_type);
	}

	/**
	 * Return an array of available PDO drivers
	 * @link http://www.php.net/manual/en/pdo.getavailabledrivers.php
	 * @return array PDO::getAvailableDrivers returns an array of PDO driver names. If
	 * no drivers are available, it returns an empty array.
	 */
	public static function getAvailableDrivers(){
		$this->connect();
		return parent::getAvailableDrivers();
	}

	/**
	 * 获取DSN字符串, 以及可能设置options选项
	 * @param string $driver
	 * @param string $host
	 * @param number $port
	 * @param string $dbName
	 * @param string $charset
	 * @return string boolean
	 */
	private function getDsn($driver, $host, $port, $dbName, $charset){
		switch ($driver){
			case 'mysql':
				$dsnParams[] = "host={$host}";
				$port && $dsnParams[] = "port={$port}";
				$dsnParams[] = "dbname={$dbName}";
				$this->options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$charset}";
				if (version_compare(PHP_VERSION, '5.3.6', '>=')){
					$dsnParams[] = "charset={$charset}";
				}else{
					$this->options[\PDO::ATTR_EMULATE_PREPARES] = false;
				}
				$dsn = "{$driver}:" . implode(';', $dsnParams);
				return $dsn;
			case 'sqlite':
				$dsn = "sqlite:{$dbName}";
				return $dsn;
			default:
				return false;
		}
	}
}