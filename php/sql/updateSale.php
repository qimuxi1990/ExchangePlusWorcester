<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');
// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$product = new product();
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
	if($data->product_status == 'deleted'){
		$updateSQL = "UPDATE transactions SET "
		."status = 'closed' "
		."WHERE product_id = '$data->product_id'; "
		."UPDATE products SET "
		."status = 'deleted' "
		."WHERE pid = '$data->product_id'";
		if(!mysqli_multi_query($connection, $updateSQL)){
			die('Error: '.mysqli_error($connection));
		}
		else{
			$product->_id = $data->product_id;
		}
	}
	if($data->product_status == 'sold'){
		$updateSQL = "UPDATE transactions SET "
		."status = 'closed' "
		."WHERE product_id = '$data->product_id' AND buyer_id != '$data->buyer_id'; "
		."UPDATE transactions SET "
		."status = 'complete' "
		."WHERE product_id = '$data->product_id' AND buyer_id = '$data->buyer_id'; "
		."UPDATE products SET "
		."status = 'sold' "
		."WHERE pid = '$data->product_id'";
		if(!mysqli_multi_query($connection, $updateSQL)){
			die('Error: '.mysqli_error($connection));
		}
		else{
			$product->_id = $data->product_id;
		}
	}
	// close connection
	mysqli_close($connection);
}
// encode data into json
$json_out = json_encode($product);
//output json
echo $json_out;
?>