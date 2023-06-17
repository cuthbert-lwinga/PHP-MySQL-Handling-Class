<?php
include_once("headers.php");

class MySql {
	private static $conn;
	private static $db;
	private static $username;
	private static $password;
	private static $tables = array();
	public function __construct($db, $username, $password) {
		self::$db = $db;
		self::$username = $username;
		self::$password = $password;
		self::setConn();
	}

    // Setters

    // Getters

	public static function getConn(){
		if (self::$conn == NULL){
			try {
					throw new CustomSqlException("tyring to do sql enquiry on NULL Sql Connection", ExceptionCode::SQL_FAILED_CONN);
				} catch (CustomSqlException $e) {
				    // Handle the exception
					echo "\nError: " . $e->getMessage();
					echo "\nCode: " . $e->getCode();
					echo "\nFile: " . $e->getFile();
					echo "\nLine: " . $e->getLine();
					echo "\nTrace: " . $e->getTraceAsString();
				}
				}
		return self::$conn;
	}

    // other functions
	public static function setConn() {
		return self::connectToServer(self::$db, self::$username, self::$password);
	}

	public static function getData() {
		return self::$data;
	}

	private static function connectToServer($database = 'test', $username = "Cuthbert", $password = 'Cuthbert1997%') {
		$server = "localhost";
		$array = array(
			"connected" => true,
			"error" => ""
		);

		$conn = new mysqli($server, $username, $password, $database);
		
		self::$conn = $conn;
		
		if ($conn->connect_error) {
			self::$conn = NULL;
			$array["connected"] = false;
			$array["error"] = $conn->connect_error;

			try {
				throw new CustomSqlException("Error establishing SQL connection", ExceptionCode::SQL_FAILED_CONN);
			} catch (CustomSqlException $e) {
		    // Handle the exception
				echo "\nError: " . $e->getMessage();
				echo "\nCode: " . $e->getCode();
				echo "\nFile: " . $e->getFile();
				echo "\nLine: " . $e->getLine();
				echo "\nTrace: " . $e->getTraceAsString();
			}


		}

		if (!$conn->set_charset("utf8")) {
			self::$conn = null;
			$array["connected"] = false;
			$array["error"] = "utf8 character was unable to be set";

			try {
				throw new CustomSqlException("Utf8 character was unable to be set", ExceptionCode::SQL_FAILED_CONN);
			} catch (CustomSqlException $e) {
		    // Handle the exception
				echo "\nError: " . $e->getMessage();
				echo "\nCode: " . $e->getCode();
				echo "\nFile: " . $e->getFile();
				echo "\nLine: " . $e->getLine();
				echo "\nTrace: " . $e->getTraceAsString();
			}

		}
		self::loadTables();		
		return $array;
	}


	public static function loadTables(){
		if(self::$conn == NULL){
			try {
				throw new CustomSqlException("tyring to do sql enquiry on NULL Sql Connection", ExceptionCode::SQL_FAILED_CONN);
			} catch (CustomSqlException $e) {
		    // Handle the exception
				echo "\nError: " . $e->getMessage();
				echo "\nCode: " . $e->getCode();
				echo "\nFile: " . $e->getFile();
				echo "\nLine: " . $e->getLine();
				echo "\nTrace: " . $e->getTraceAsString();
			}
			return;
		}

		$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".self::$db."';";
		$result = self::$conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$temp = new Table($row["TABLE_NAME"]);
				$temp->setName($row["TABLE_NAME"]);
				$temp->loadColumns();
				self::$tables[] = $temp;
			}

		}
	}


	private function constructDeleteSql($table)
	{
		$table_name = $table->getName();
		$condition = [];
		foreach ($data["variables"] as $key => $value) {
			$value = self::$conn->real_escape_string($value);
			if (!empty($value) || $value === "0") {
				$condition[] = "$key='$value'";
			}
		}
		$sqlCondition = (count($condition) > 0) ? implode(" AND ", $condition) : "0";
		$sql = "DELETE FROM $table_name WHERE $sqlCondition;";
		return $sql;
	}

	public static function constructInsertSql($data)
	{
		$sql = "";

		foreach ($data as $insertData) {
			if (isset($insertData["table"]) && isset($insertData["variables"])) {
				$table_name = $insertData["table"];
				$variables = $insertData["variables"];

            // Construct the insert statement for a single row
				$insertDataSql = self::constructSingleInsertSql($table_name, $variables);

            // Append the generated SQL to $sql
				$sql .= $insertDataSql;
			}
		}

		return $sql;
	}

	public static function constructSingleInsertSql($table_name, $variables)
	{
		$sql = "INSERT INTO $table_name ";

		$columns = array();
		$values = array();

		foreach ($variables as $column => $value) {
			$columns[] = $column;

			if (is_array($value)) {
            // Handle array values
				$serializedValue = json_encode($value);//self::serializeArray($value);
				$values[] = "'" . $serializedValue . "'";
			} else {
				$values[] = "'" . $value . "'";
			}
		}

		$columnsSql = implode(", ", $columns);
		$valuesSql = implode(", ", $values);

		$sql .= "($columnsSql) VALUES ($valuesSql);";

		return $sql;
	}

	public static function serializeArray($array)
	{
		$serializedArray = array();

		foreach ($array as $value) {
			if (is_array($value)) {
				$serializedArray[] = self::serializeArray($value);
			} else {
				$serializedArray[] = $value;
			}
		}

		return implode(",", $serializedArray);
	}


	public static function constructSelectSql($data)
	{
		$table_name = $data["table"];
		$variables = $data["variables"];
		$returnVar = [];
		$sqlCondition = [];
		$conn = self::$conn;

		foreach ($variables as $key => $value) {
			$value = $conn->real_escape_string($value);

			if (empty($value)) {
				$returnVar[] = $key;
			}else{
				$sqlCondition[] = "$key = '$value'";
			}
		}

		if(empty($returnVar)){
			try {
				throw new CustomSqlException("Empty return value, incorrect usage", ExceptionCode::SQL_INCORRECT_USAGE);
			} catch (CustomSqlException $e) {
		    // Handle the exception
				echo "\nError: " . $e->getMessage();
				echo "\nCode: " . $e->getCode();
				echo "\nFile: " . $e->getFile();
				echo "\nLine: " . $e->getLine();
				echo "\nTrace: " . $e->getTraceAsString();
				echo  "\n\n";
			}
		}

		$sqlVar = implode(",", array_keys($variables));
		$returnVar = implode(",", $returnVar);
		$sqlCondition = implode(" AND ", $sqlCondition);
		$sql = "SELECT $returnVar FROM $table_name WHERE $sqlCondition";

		return $sql;
	}

	private function constructSql($data)
	{
		if ($data["type"] == "delete") {
			return $this->constructDeleteSql($data);
		} else if ($data["type"] == "insert") {
			return $this->constructInsertSql($data);
		} else if ($data["type"] == "select") {
			return $this->constructSelectSql($data);
		}

		return "";
	}

	public static function Table($table){
		for ($i=0; $i < count(self::$tables); $i++) { 
			$temp = self::$tables[$i];
			if ($temp->getName()==$table) {
				return $temp;
			}
		}
		return NULL;
	}

	public static function execute($sql){
		$conn = self::getConn();
		return $conn->query($sql);
	}

	public static function isPhpTypeEquivalentToSqlType($phpType, $sqlType) {
    $phpToSqlTypeMap = [
        'integer' => ['INT', 'INTEGER', 'SMALLINT', 'BIGINT'],
        'double' => ['FLOAT', 'REAL', 'DOUBLE PRECISION'],
        'string' => ['CHAR', 'VARCHAR', 'TEXT'],
        'boolean' => ['BOOLEAN', 'BOOL'],
        'array' => ['JSON', 'ARRAY'],
        'object' => ['JSON', 'OBJECT'],
        'NULL' => ['NULL'],
        'resource' => ['BLOB'],
        // Add more mappings as needed
    ];

    $phpType = strtolower($phpType);
    $sqlType = strtoupper($sqlType);

    if (array_key_exists($phpType, $phpToSqlTypeMap)) {
        return in_array($sqlType, $phpToSqlTypeMap[$phpType]);
    }

    return false;
}


}





?>
