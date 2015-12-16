<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');

// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$user = new user();
// process data
// fetch data from database
// Create a database connection
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "123456";
$dbname = "exchangeplus_worcester";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

//test if connected
if(mysqli_connect_errno()){
	die("Database connecction failed: " 
		. mysqli_connect_error() 
		. " (the error number is: " 
			. mysqli_connect_errno() 
			. ")"
	);
}
else{

	$loginSQL = "SELECT * FROM users WHERE uid =". $data;
	$loginResult = mysqli_query($connection, $loginSQL);
	if(!$loginResult) {
		die('Error : ' . mysqli_error($connection));
	}else {
		if($loginResult->num_rows != 0) {
			$userRow = mysqli_fetch_assoc($loginResult);
			$user->_id = $userRow["uid"];
			$user->name = $userRow["name"];
			$user->tel = $userRow["tel"];
			$user->email = $userRow["email"];
			$user->address = $userRow["address"];
		}
	}

	// free resources
	mysqli_free_result($loginResult);
	// close connection
	mysqli_close($connection);
}
// encode data into json
$json_out = json_encode($user);
//output json
echo $json_out;
?>