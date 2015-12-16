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

	$loginSQL = "SELECT * FROM users WHERE uid =". $data->_id;
	$loginResult = mysqli_query($connection, $loginSQL);
	if(!$loginResult) {
		die('Error : ' . mysqli_error($connection));
	}else {
		if($loginResult->num_rows != 0) {
			$userRow = mysqli_fetch_assoc($loginResult);
			if($userRow["password"]==$data->password){
				$user->_id = $userRow["uid"];
				$user->password = $userRow["password"];
				$user->name = $userRow["name"];
				$user->tel = $userRow["tel"];
				$user->email = $userRow["email"];
				$user->address = $userRow["address"];
				$sellSQL = "SELECT * FROM products WHERE sid =".$user->_id;
				$sellResult = mysqli_query($connection, $sellSQL);
				if(!$sellResult) {
					die('Error : ' . mysqli_error($connection));
				}else {
					while($sellRow = mysqli_fetch_assoc($sellResult)){
						array_push($user->sellList, $sellRow["pid"]);
					}
				}
				mysqli_free_result($sellResult);
				$buySQL = "SELECT * FROM transactions WHERE buyer_id =".$user->_id;
				$buyResult = mysqli_query($connection, $buySQL);
				if(!$buyResult) {
					die('Error : ' . mysqli_error($connection));
				}else {
					while($buyRow = mysqli_fetch_assoc($buyResult)){
						array_push($user->buyList, array("product_id"=>$buyRow['product_id'], "offering_price"=>$buyRow['price'], "transaction_status"=>$buyRow['status']));
					}
				}
				mysqli_free_result($buyResult);
			}
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