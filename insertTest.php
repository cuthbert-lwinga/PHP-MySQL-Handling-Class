<?PHP
include_once("headers.php");

function singleInputtest(){
	$sql = new MySql(Database::test, UserConnection::username, UserConnection::password); // set up your username and password for ur mysql
	$sql = MySql::Table("accounts");
	$sql->insert(array("FirstName"=>"Neema","LastName"=>"Lymo"));
	if($sql->CheckOutInsert()){
		echo "insert successful";
		return;
	}
	echo "insert not successful";
}


function multipleInputtest(){
	$sql = new MySql(Database::test, UserConnection::username, UserConnection::password); // set up your username and password for ur mysql
	$sql = MySql::Table("accounts");
	$data = TestData(100);
	$sql->insert($data);
	if($sql->CheckOutInsert()){
		echo "insert successful";
		return;
	}
	echo "insert not successful";
}

function TestData($count){
	$names = array(
    "John",
    "Jane",
    "Michael",
    "Emily",
    "William",
    "Olivia",
    "James",
    "Cuthbert",
    "Neema",
    "Stephanie",
    "Deven",
    "Danny",
    "Marion",
    "Ben",
    "Sophia",
    "Alexander",
    "Emma",
    "Jacob",
    "Ava",
    "Mia",
    "Noah",
    "Isabella",
    "Ethan",
    "Oliver",
    "Liam",
    "Charlotte",
    "Amelia",
    "Harper",
    "Elijah",
    "Lucas",
    "Matthew",
    "Abigail",
    "Emily",
    "Scarlett",
    "Victoria",
    "Daniel",
    "Logan",
    "Grace",
    "David",
    "Christopher",
    "Henry",
    "Aiden",
    "Jackson"
    // Add more names as needed
);


$people = array();

$peopleIndexed = array();

for ($i = 0; $i < $count;) {
    $firstName = $names[rand(0, count($names) - 1)];
    $lastName = $names[rand(0, count($names) - 1)];
    $index = $firstName.$lastName;
    if (!in_array($index, $peopleIndexed)) {
        $person = array(
            "FirstName" => $firstName,
            "LastName" => $lastName
        );
        
        $people[] = $person;
        $peopleIndexed[] = $index;
        $i++;
        }

}

return $people;

}

// UnComment for Testing

//singleInputtest();
//multipleInputtest();



?>