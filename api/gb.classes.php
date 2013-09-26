<?php

	class apiresponse {
		public $error = array();
		public $records = array();
		
		function __construct() {
			$this->clearRecords();
			$this->setError('No Error', 0, 200);
		}
		
		function setError($errorMessage, $errorLevel, $errorCode) {
			$this->error['message'] = $errorMessage;
			$this->error['level'] = $errorLevel;
			$this->error['code'] = $errorCode;
		}
		
		function addRecord($newRecord) {
			$this->records[] = $newRecord;
		}
		
		function addRecords($newRecords) {
			if (is_array($newRecords) ) {
				foreach ($newRecords as $newRecord) {
					$this->addRecord($newRecord);
				}
			}
		}
		
		function clearRecords() {
			$this->records = array();
		}
		
		function __toString() {
			// build the object version of the data and dump it as json.
			$obj = (object) array( 'status' => $this->error,
				'records' => $this->records);
			return json_encode($obj);
		}
	}


class gbdbaccess {
	private $db;
	private $className;
	
	public function __construct() {
		$this->db = new gbdb();
		$this->className = get_class($this);
	}
	
	public function addRecord ($fields) {
		$db = new gbdb();
		$db->insert($this->className, $fields, false, true);
		$vals = '200';
		return $vals;
	}
	
	public function editRecord ($fields) {
		$w = new gbdbwhere('id', '=', $fields['id']);
		$this->db->update($this->className, $fields, array($w));
		return '200';
	}
	
	public function getRecord ($field) {
		$w = new gbdbwhere('id', '=', $field);
		$retVal = $this->db->select($this->className, array('*'), array($w));
		return $retVal;
	}
	
	public function deleteRecord ($fields) {
	}
	
	public function getRecordList($params) {
		return $this->db->select($this->className);
	}
	
	public function getRecordListByParent($params, $keyField = 'parentid') {
		$w = new gbdbwhere($keyField, '=', $params);
		return $this->db->select($this->className, array('*'), array($w));
	}
}

class sqlgen {
	public function insert($table, $values, $quoteValues = false) {
		$numProperties = count($values);
		$quoteCharacter = '';
		if ($quotevalues) { 
			$quoteCharacter = "'";
		}
		$index = 0;
		$db_names =  '';
		$db_values = '';
		if (is_array($values)) {
			foreach($values as $key => $value) {
				$index++;
				$db_names .= $key;
				$db_values .= $quoteCharacter.$value.$quoteCharacter;
				if ($index < $numProperties) {
					$db_names .= ',';
					$db_values .= ',';
				}
			}
		} 
		$sql = 'INSERT INTO '.$table.' ('.$db_names.') VALUES ('.$db_values.')';
		return $sql;
	}
}

	/**
	 * gbdb - Geekback Database
	 *
	 * Database Abstraction
	 * @copyright Copyright 2008-2010, Trevor Merritt
	 * @author Trevor Merritt <trevor.merritt@gmail.com>
	 * @package gblib
	 * @subpackage gbcore
	 * @todo Add sqlite support
	 * @todo Add postgresql support
	 * @todo Add arbitrary database support
	 */

	/**
	 * gbdb Database Abstraction
	 * @package gblib
	 * @subpackage gbcore
	 * @todo Multiple Database Support ** Switch statement thing **
	 */
	class gbdb {

		/**
		 * Database Host
		 */
		private $DB_HOST = '';

		/**
		 * Database Username
		 */
		private $DB_USER = '';

		/**
		 * Database Password
		 */
		private $DB_PASS = '';

		/**
		 * Database Name
		 */
		private $DB_NAME = '';

		/**
		 * Database Link
		 */
		private $dbLink = null;

		/**
		 * Database Results
		 */
		public $results;

		/**
		 * Number of Rows in the results
		 */
		private $numRows = 0;

		/**
		 * mysql_real_escape_string processed version of the $SQL most recently executed
		 */
		private $sql = '';

		/**
		 * Database Type - mysql by default
		 * @todo make sqlite default
		 */
		private $dbType = DBTYPE_MYSQL;

		/**
		 * Create a gbdb Object
		 * @param string $query Query to execute
		 * @param string $DB_HOST Database Hostname
		 * @param string $DB_USER Database Username
		 * @param string $DB_PASS Database Password
		 * @param string $DB_NAME Database Name
		 * @param string $DB_TYPE Database Type
		 */
		public function __construct($query = "", $DB_HOST = DB_HOST,
									$DB_USER = DB_USER, $DB_PASS = DB_PASS,
									$DB_NAME = DB_NAME, $DB_TYPE = DBTYPE_MYSQL) {
			$this->setDBType($DB_TYPE);
			$this->DB_HOST = $DB_HOST;
			$this->DB_USER = $DB_USER;
			$this->DB_PASS = $DB_PASS;
			$this->DB_NAME = $DB_NAME;
			$this->setDBLink($this->connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME));
			if ($query != "") {
				return $this->query($query);
			}
		}

		/**
		 * Add a table to the database
		 * @param $tableName string Name of the table to create
		 * @param $fields array Associative array of the fields to create using the following format:
		 * 	$fields = array(array('name' => 'id', 	'type' => 'integer'),
		 *			  array('name' => 'fname', 		'type' => 'varchar(255)' ),
	 	 *			  array('name' => 'lame',		'type' => 'varchar(255)'));
		 */
		public function addTable($tableName, $fields) {
			switch($this->getDBType()) {
				case DBTYPE_MYSQL:
					/**
					 * @todo: implement add table for MySQL.
					 */
					echo ERROR_NOT_IMPLEMENTED;
				break;
				case 'sqlite':
					//     sqlite_query($db, 'CREATE TABLE foo (bar varchar(10))');
					$sql = 'CREATE TABLE '.$tableName.' (';
					$numRecords = count($fields);
					$i = 0;
					foreach($fields as $record) {
						$sql .= $record['name'].' '.$record['type'];
						$i++;
						if ($i != $numRecords) {
							$sql .= ',';
						}
					}
					$sql .= ')';
					echo $sql;
					// $this->query($sql);
				break;
			}
		}

		/**
		 * Execute an SQL query
		 * @param $sql string Query to execute
		 * @param $showSQL boolean True causes SQL to be returned rather then xecuting the query
		 */
		public function query($sql, $showSQL = false) {
			if ($this->getDBLink() == null) {
				$this->connect();
			}
			switch ($this->getdbType()) {
				case DBTYPE_MYSQL:
					if (!$showSQL) {
//						$this->sql = mysql_real_escape_string($sql);
						$this->sql = $sql;
						$this->results = mysql_query($this->sql) or die(ERROR_DB_QUERY.$this->sql.mysql_error());
						$this->insertID = mysql_insert_id();
						if (is_resource($this->results)) {
							$this->numRows = mysql_num_rows($this->results);
						}
					} else {
						return $sql;
					}
				break;
				case DBTYPE_SQLITE:
						echo 'Hey, gotta sqlite query with '.$sql."\n";
					break;
				break;
				default:
					die(ERROR_NOT_IMPLEMENTED);
			}
		}

		/**
		 * Connect to the database
		 * @param string $host Database Host
		 * @param string $user Database Username
		 * @param string $pass Database Password
		 * @param string $name Database Name
		 * @todo error checking
		 */
		private function connect($host = DB_HOST, $user = DB_USER,
								$pass = DB_PASS, $name = DB_NAME) {
			switch ($this->getDBType()) {
				case DBTYPE_MYSQL:
					$link = mysql_connect($host, $user, $pass) or die('unable to connect to db.'.mysql_error());
					$link = mysql_select_db($name, $link) or die('unable to select db'.$name.mysql_error());
					return $link;
				break;
				case DBTYPE_SQLITE:
					if ($db = sqlite_open('', 0666, $sqliteError)) {
						$this->setDBLink = $db;
					}
				break;
				default:
					echo ERROR_NOT_IMPLEMENTED;
				break;

			}
		}

		public function insertID() {
			return $this->insertID;
		}

		/**
		 * Get the results from a query as an array
		 * @return array Associative array of query results
		 */
		public function getArray() {

			$resultArray = array();

			switch ($this->getDBType()) {
				case DBTYPE_MYSQL:
					if ($this->results != null) {
						while ($row = mysql_fetch_array($this->results, MYSQL_ASSOC)) {
							$resultArray[] = $row;
						}		
					}
				break;
				default:
					echo ERROR_NOT_IMPLEMENTED;
				break;
			}
			return $resultArray;
		}

		/**
		 * Get a single row array iem
		 * @return array
		 */
		public function getSingleArray() {
			$val = $this->getArray();
			$val = isset($val[0]) ? $val[0] : false;
			return $val;
		}

		/**
		 * Get the number of rows for the query
		 * @return integer
		 */
		public function getNumRows() {
			return (integer)$this->numRows;
		}

		/**
		 * Set the database type
		 * @param string $newDBType New database type
		 */
		private function setDBType($newDBType) {
			$this->dbType = $newDBType;
		}

		/**
		 * Get the database Type
		 */
		public function getDBType() {
			return $this->dbType;
		}

		/**
		 * Update the internal DB link
		 * @param resource $newDBLink Live Database link
		 */
		private function setDBLink($newDBLink) {
			$this->dbLink = $newDBLink;
		}

		/**
		 * Get the current dbLink resource
		 */
		public function getDBLink() {
			return $this->dbLink;
		}

		/**
		 * Update a record in the table
		 * 
		 * @param string $table Table to update
		 * @param array $values Associative array of field->value pairs
		 */
		public function update($table, $values, $wheres, $showsql = false) {
			$numValues = count($values);
			$i = 0;
			$sql  = '';
			$sql .= 'UPDATE '.$table.' SET ';
			foreach($values as $key => $value) {
				$sql .= $key.'=\''.$value.'\'';
				$i++;
				if ($i < $numValues) {
					$sql .= ', ';
				}
			}
			$sql .= $this->buildWheres($wheres);
			return $this->query($sql, $showsql);
 		}

		/**
		 * Insert a record into the table
		 * @param string $table Table to insert data to
		 * @param array $values Associative array of ('field' => 'value') pairs
		 */
		public function insert($table, $values, $showsql = false, $quotevalues = false) {
			$numProperties = count($values);
			$quoteCharacter = '';
			if ($quotevalues) { 
				$quoteCharacter = "'";
			}
			$index = 0;
			$db_names =  '';
			$db_values = '';
			if (is_array($values)) {
				foreach($values as $key => $value) {
					$index++;
					$db_names .= $key;
					$db_values .= $quoteCharacter.$value.$quoteCharacter;
					if ($index < $numProperties) {
						$db_names .= ',';
						$db_values .= ',';
					}
				}
			} 
			$sql = 'INSERT INTO '.$table.' ('.$db_names.') VALUES ('.$db_values.')';
			return $this->query($sql, $showsql);
		}		

		/**
		 * Select records from the database
		 * 
		 * @param string $table Table to select records from
		 * @param array $fields Array of fields to get
		 * @param array $wheres Array of gbdbwhere objects setting conditions on the query
		 * @param boolean $showsql Return the generated SQL rather than running it
		 * @param boolean $single Expect a single record 
		 */ 
		public function select($table, $fields = array('*'), $wheres = array(), $showsql = false, $single = false) {
			$i = 0;
			$numFields = count($fields);
			$numWheres = count($wheres);
			$sql  = '';
			$sql .= 'SELECT ';
			foreach ($fields as $field) {
				$sql .= $field;
				$i++;
				if ($i != $numFields) {
					$sql .= ', ';
				}
			}
			$sql .= ' FROM '.$table;
			$sql .= (count($wheres)) ? $this->buildWheres($wheres) : '';
			if ($showsql) {
				$values = $sql;
			} else {
				$this->query($sql);
				if ($single) {
					$values = $this->getSingleArray();
				} else {
					$values = $this->getArray();
				}
			}
			return $values;
		}

		private function buildWheres($wheres) {
			$sql = '';
			$quoteChar = "'";
			if (count($wheres)) {
				$sql .= ' WHERE ( ';
				$i = 0;
				foreach ($wheres as $where) {
					if ($i > 0) {
						$sql .= ' AND ';
					}
					$i++;
					if (!$where->quote) {
						$quoteChar = "";
					}
					$sql .= $where->field.' '.$where->op.' '.$quoteChar.$where->where.$quoteChar;
				}
				$sql .= ')';
			} else {
				$sql = $wheres->field.' '.$wheres->op.' '.$quoteChar.$wheres->where.$quoteChar;
			}
			return $sql;
		}
	}	

	/**
	 * gbdbwhere Database Where clause
	 * @package gblib
	 * @subpackage gbcore
	 */
	class gbdbwhere {

		public $field = '';
		public $where = '';
		public $op = '';
		public $quote = false;

		public function __construct($field = '', $op = '=', $where = '', $quoteValue = true) {
			$this->field = $field;
			$this->where = $where;
			$this->op = $op;
			$this->quote = $quoteValue;
		}

	}
   
class gbwebclient {
   /**
   * Send a POST requst using cURL
   * @param string $url to request
   * @param array $post values to send
   * @param array $options for cURL
   * @return string
   */   
   static public function post($url, array $post = NULL, array $options = array())
   {
      $defaults = array(
         CURLOPT_POST => 1,
         CURLOPT_HEADER => 0,
         CURLOPT_URL => $url,
         CURLOPT_FRESH_CONNECT => 1,
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_FORBID_REUSE => 1,
         CURLOPT_TIMEOUT => 4,
         CURLOPT_POSTFIELDS => http_build_query($post)
      );
   
      $ch = curl_init();
      curl_setopt_array($ch, ($options + $defaults));
		
      if( ! $result = curl_exec($ch))
      {
         trigger_error(curl_error($ch));
      }
      curl_close($ch);
      return $result;
   }

   /**
   * Send a GET requst using cURL
   * @param string $url to request
   * @param array $get values to send
   * @param array $options for cURL
   * @return string
   */
   static public function get($url, array $get = NULL, array $options = array())
   {   
       $defaults = array(
           CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
           CURLOPT_HEADER => 0,
           CURLOPT_RETURNTRANSFER => TRUE,
           CURLOPT_TIMEOUT => 4
       );
      
       $ch = curl_init();
       curl_setopt_array($ch, ($options + $defaults));
       if( ! $result = curl_exec($ch))
       {
           trigger_error(curl_error($ch));
       }
       curl_close($ch);
       return $result;
   }
   
}