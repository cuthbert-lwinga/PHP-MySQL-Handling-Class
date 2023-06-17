<?PHP
include_once("headers.php");

function innerJointest(){
		$sql = new MySql(Database::test, UserConnection::username, UserConnection::password); // set up your username and password for ur mysql
		
		$sql = MySql::Table("accounts");
		$sql->select(array("id"=>"","FirstName"=>"","LastName"=>"Lwinga"));
		
		$sql2 = MySql::Table("AccessRecords");
		$sql2->select(array("userId"=>"","accessLevel"=>""),true);

		$sql->INNERJOIN($sql2);
		$sql->ON(([[$sql->getTablecol("id"),$sql2->getTablecol("userId")]]));
		$output = $sql->CheckOutSelect();

		if($output!=NULL){
			if($output->num_rows>0){
				while($row = $output->fetch_assoc()){
					var_dump($row);
					echo " \n";
				}

			}
		}
	}
function selectTest(){
			$sql = new MySql(Database::test, UserConnection::username, UserConnection::password); // set up your username and password for ur mysql
		
		$sql = MySql::Table("accounts");
	$sql->select(array("id"=>"","FirstName"=>"","LastName"=>"Lwinga"));
	$output = $sql->CheckOutSelect();

	if($output!=NULL){
		if($output->num_rows>0){
			while($row = $output->fetch_assoc()){
				echo "id: ".$row["id"]." FirstName: ".$row["FirstName"]." \n";
			}

		}
	}

	}


	selectTest();
?>