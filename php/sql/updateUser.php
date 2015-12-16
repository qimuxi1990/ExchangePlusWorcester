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

	$updateSQL = "UPDATE users SET "
	."password = '$data->password', "
	."name = '$data->name', "
	."tel = '$data->tel', "
	."email = '$data->email', "
	."address = '$data->address'"
	."WHERE uid = '$data->_id'";
	if(!mysqli_query($connection, $updateSQL)){
		die('Error: '.mysqli_error($connection));
	}
	else
		$user = $data;

	// close connection
	mysqli_close($connection);
}

// encode data into json
$json_out = json_encode($user);
//output json
echo $json_out;
?>