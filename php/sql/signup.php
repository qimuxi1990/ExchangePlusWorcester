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

$uidSQL = "SELECT uid FROM users ORDER BY uid ASC";
$uidResult = mysqli_query($connection, $uidSQL);
if(!$uidResult){
	die('Error'.mysqli_error($connection));
}
else{
	// generate id
	$uid = 1;
	while($uidRow = mysqli_fetch_assoc($uidResult)){
		if($uidRow["uid"]==$uid)
			$uid += 1;
		else
			break;
	}
	// insert into TABLE users
	$signupSQL = "INSERT INTO users(uid, password, name, tel, email, address)
	VALUES('$uid', '$data->password', '$data->name', '$data->tel', '$data->email', '$data->address')";
	if(!mysqli_query($connection, $signupSQL)){
		die('Error'.mysqli_error($connection));
	}
	else{
		$user->_id = $uid;
		$user->password = $data->password;
		$user->name = $data->name;
		$user->tel = $data->tel;
		$user->email = $data->email;
		$user->address = $data->address;
	}
}


// free resources
mysqli_free_result($uidResult);
// close connection
mysqli_close($connection);

// encode data into json
$json_out = json_encode($user);
//output json
echo $json_out;
?>