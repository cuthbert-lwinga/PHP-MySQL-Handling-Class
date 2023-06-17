<?PHP
include_once("headers.php");

use Utility as UT;

class Table {
	private $name;
	private $columns = array();
	private $stack_insert = array();
	private $stack_select = array();
	private $stack_delete = array();
	private $stackedTables = array();
	private $tempSqlstring;
	public function setName($name) {
		$this->name = $name;
		$this->resetStackedtables();
	}
	// Getters
	public function getName() {
		return $this->name;
	}

	public function appendColumn(TableColumn $column) {
		$this->columns[] = $column;
	}

	public function getColumns() {
		return $this->columns;
	}

	public function getStackselect() {
		return $this->stack_select;
	}

	public function getInsertselect() {
		return $this->stack_insert;
	}

	public function getDeleteselect() {
		return $this->stack_delete;
	}

	public function loadColumns() {
		$sql = "SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CASE WHEN COLUMN_KEY = 'PRI' THEN True ELSE False END AS IsPrimaryKey FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'test' AND TABLE_NAME = '".$this->name."';";
		$result = (MySql::execute($sql));
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$column = new TableColumn($row["ColumnName"], $row["DataType"] , ($row["IsPrimaryKey"]==1?True:False));
				$this->appendColumn($column);
			}
		}
	}

    // Other functions 

	public function resetStackedtables()
	{
		$stackedTables = array();
		$stackedTables[] = $this->name;
	}

	public function getTablecol($column){
		$col = $this->ColumnExists($column);
		if ($col!=NULL) {
			return $this->name.".".$col->getName();
		}
		return NULL;
	}

	public function ResetInsetStack(){
		$this->stack_insert = array();
	}

	public function ResetSelectStack(){
		$this->stack_select = array();
	}

	public function ResetDeleteStack(){
		$this->stack_delete = array();
	}

	public function ColumnExists($column){

		for($i = 0; $i < count($this->columns);$i++){
			$temp = $this->columns[$i];
			if ($temp->getName()==$column) {
				return $temp;
			}
		}
		return NULL;
	}

	public function ShowStructure() {
		echo "TABLE: " . $this->name . "\n";
		foreach ($this->columns as $column) {
			echo "Column: " . $column->getName().(($column->getIsPrimary())?" (IS PRIMARY)":""). "\n";
			echo "\tType: " . $column->getType() . "\n";
		}
		return false;
	}

	public function insert($variables){

		if (UT::isArrayofArrays($variables)) {
			for ($i=0; $i < count($variables); $i++) { 
						$this->insert($variables[$i]);
					}
					return;
		}

		$insertData = array("table" => $this->name,"variables"=>array());
		foreach( $variables as $column => $data){
			$col = $this->ColumnExists($column);
			if ($col!=NULL) {
				if(MySql::isPhpTypeEquivalentToSqlType(gettype($data), $col->getType())){

				}else{

					unset($variables[$column]);
					try {
						throw new CustomSqlException("Column ($column) removed beacuse of data type Missmatch: ".gettype($data)."!=".$col->getType()." TABLE".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
					} catch (CustomSqlException $e) {
						    // Handle the exception
						echo "\nWarning: " . $e->getMessage();
						echo "\nCode: " . $e->getCode();
						echo "\nFile: " . $e->getFile();
						echo "\nLine: " . $e->getLine();
						echo "\nTrace: " . $e->getTraceAsString();
					}

				}

			}else{
				unset($variables[$column]);
				try {
					throw new CustomSqlException("tyring to Access an unidentified column on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
				} catch (CustomSqlException $e) {
				    // Handle the exception
					echo "\nWarning: " . $e->getMessage();
					echo "\nCode: " . $e->getCode();
					echo "\nFile: " . $e->getFile();
					echo "\nLine: " . $e->getLine();
					echo "\nTrace: " . $e->getTraceAsString();
				}

			}
		}

		$insertData["variables"] = $variables;

		if(empty($insertData["variables"])){
			try {
				throw new CustomSqlException("Insert aborted, varibles are empty on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
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

		$this->stack_insert[] = $insertData;

	}


	public function select($variables,$canBeempty = false){
		$selectData = array("table" => $this->name,"variables"=>array());
		foreach( $variables as $column => $data){
			$col = $this->ColumnExists($column);
			if ($col!=NULL) {
				if($data==""||MySql::isPhpTypeEquivalentToSqlType(gettype($data), $col->getType())){
					$variables[$this->name.".".$column] = $data;
					unset($variables[$column]);					
				}else{

					unset($variables[$column]);
					try {
						throw new CustomSqlException("Column ($column) removed beacuse of data type Missmatch: ".gettype($data)."!=".$col->getType()." TABLE".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
					} catch (CustomSqlException $e) {
						    // Handle the exception
						echo "\nWarning: " . $e->getMessage();
						echo "\nCode: " . $e->getCode();
						echo "\nFile: " . $e->getFile();
						echo "\nLine: " . $e->getLine();
						echo "\nTrace: " . $e->getTraceAsString();
					}

				}

			}else{
				unset($variables[$column]);
				try {
					throw new CustomSqlException("tyring to Access an unidentified column on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
				} catch (CustomSqlException $e) {
				    // Handle the exception
					echo "\nWarning: " . $e->getMessage();
					echo "\nCode: " . $e->getCode();
					echo "\nFile: " . $e->getFile();
					echo "\nLine: " . $e->getLine();
					echo "\nTrace: " . $e->getTraceAsString();
				}

			}
		}

		$selectData["variables"] = $variables;

		if(empty($selectData["variables"])&&(!$canBeempty)){
			try {
				throw new CustomSqlException("SELECT aborted, varibles are empty on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
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
		//$this->resetSelectstack();
		$this->stack_select[] = $selectData;
	}

	public function INNERJOIN($table2){

		if (!empty($table2->stack_select)&&!empty($this->stack_select)){
			$stackedTables[] = $table2->name;
			$temp =  array_merge($this->stack_select[0]["variables"], $table2->stack_select[0]["variables"]);
			$this->stack_select[0]["variables"] = $temp;
		}
		
		return $this;
	}

	public function ON($columns){

		if ($this->checkMultipleTablePresence()) {	
			$sql = (MySql::constructSelectSql($this->stack_select[0]));
			$temp = explode("WHERE", $sql);
			for ($i=0; $i < count($columns) ; $i++) { 
				if (count($columns[$i])==2) {
					$table = explode(".",$columns[$i][1])[0];
					$temp[0] .= " INNER JOIN $table ON ".$columns[$i][0]." = ".$columns[$i][1]." ";
				}else{
					try {
						throw new CustomSqlException("Trying to perfrom ON() on a single table with count != 2 column on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
					} catch (CustomSqlException $e) {
				    // Handle the exception
						echo "\nWarning: " . $e->getMessage();
						echo "\nCode: " . $e->getCode();
						echo "\nFile: " . $e->getFile();
						echo "\nLine: " . $e->getLine();
						echo "\nTrace: " . $e->getTraceAsString();
						echo "\n ---------------------------- \n";
						echo "\n input structure fed \n";
						var_dump($this->stack_select[0]);
					}
				}
			}

			$temp = implode("WHERE", $temp);
			$this->tempSqlstring =$temp;

		}else{

			try {
				throw new CustomSqlException("Trying to perfrom inner join on a single table column on TABLE: ".$this->name, ExceptionCode::SQL_INCORRECT_USAGE);
			} catch (CustomSqlException $e) {
				    // Handle the exception
				echo "\nWarning: " . $e->getMessage();
				echo "\nCode: " . $e->getCode();
				echo "\nFile: " . $e->getFile();
				echo "\nLine: " . $e->getLine();
				echo "\nTrace: " . $e->getTraceAsString();
				echo "\n ---------------------------- \n";
				echo "\n input structure fed \n";
				var_dump($this->stack_select[0]);
			}

		}
	}


	public function checkMultipleTablePresence() {
		$prefixes = array();
		$differentPrefixes = true;

		foreach ($this->stack_select[0]['variables'] as $key => $value) {
			$prefix = explode('.', $key)[0];

			if (!in_array($prefix, $prefixes)) {
				$prefixes[] = $prefix;	
			}

			
		}

		if (count($prefixes)>1) {
			return true;
		} else {
			return false;		
		}
	}


	public function resetInsertstack(){
		$this->stack_insert = array();
	}

	// public function resetSelectstack(){
	// 	$this->stack_select = array();
	// }

	public function CheckOutInsert(){
		$conn = MySql::getConn();
		if ($conn!=NULL) {
			$sql = MySql::constructInsertSql($this->stack_insert);
			$this->resetInsertstack();
			return MySql::execute($sql);
		}

		try {
			throw new CustomSqlException("Error Inserting on TABLE: ".$this->name." sql: ".$this->stack_insert, ExceptionCode::SQL_INCORRECT_USAGE);
		} catch (CustomSqlException $e) {
				    // Handle the exception
			echo "\nError: " . $e->getMessage();
			echo "\nCode: " . $e->getCode();
			echo "\nFile: " . $e->getFile();
			echo "\nLine: " . $e->getLine();
			echo "\nTrace: " . $e->getTraceAsString();
		}
		$this->resetInsertstack();
		return NULL;
	}

	public function CheckOutSelect(){
		$conn = MySql::getConn();
		if ($conn!=NULL) {
			if ($this->tempSqlstring=="") {
				$sql = MySql::constructSelectSql($this->stack_select[0]);
				$this->resetSelectstack();
			}else{
				$sql = $this->tempSqlstring;
				$this->tempSqlstring="";
			}


			return MySql::execute($sql);
		}

		try {
			throw new CustomSqlException("Error Inserting on TABLE: ".$this->name." sql: ".$this->stack_insert, ExceptionCode::SQL_INCORRECT_USAGE);
		} catch (CustomSqlException $e) {
				    // Handle the exception
			echo "\nError: " . $e->getMessage();
			echo "\nCode: " . $e->getCode();
			echo "\nFile: " . $e->getFile();
			echo "\nLine: " . $e->getLine();
			echo "\nTrace: " . $e->getTraceAsString();
		}
		$this->resetSelectstack();
		return NULL;
	}



}

?>