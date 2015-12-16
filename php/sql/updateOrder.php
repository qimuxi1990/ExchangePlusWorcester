<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');
// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$success = 0;
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
	if($data->transaction_status == 'closed'){
		$updateSQL = "UPDATE transactions SET "
		."status = 'closed' "
		."WHERE product_id = '$data->product_id' "
		."AND buyer_id = '$data->buyer_id'";
		if(!mysqli_query($connection, $updateSQL)){
			die('Error: '.mysqli_error($connection));
		}else {
			$success = 1;
		}
	}else {
		$querySQL = "SELECT * FROM transactions WHERE product_id = '$data->product_id' "
		."AND buyer_id = '$data->buyer_id'";
		$queryResult = mysqli_query($connection, $querySQL);
		if(!$queryResult){
			die('Error : ' . mysqli_error($connection));
		}else {
			if($queryResult->num_rows != 0){
				$updateSQL = "UPDATE transactions SET "
				."price = '$data->offering_price', "
				."status = '$data->transaction_status' "
				."WHERE product_id = '$data->product_id' "
				."AND buyer_id = '$data->buyer_id'";
			}
			else {
				$updateSQL = "INSERT INTO transactions VALUES "
				."('$data->buyer_id', '$data->product_id', '$data->offering_price', 'pending')";
			}
			if(!mysqli_query($connection, $updateSQL)){
				die('Error: '.mysqli_error($connection));
			}else {
				$success = 1;
			}
		}
		// free resources
		mysqli_free_result($queryResult);
	}
	// close connection
	mysqli_close($connection);
}
// encode data into json
$json_out = json_encode($success);
//output json
echo $json_out;
?>