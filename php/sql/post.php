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

$pidSQL = "SELECT pid FROM products ORDER BY pid ASC";
$pidResult = mysqli_query($connection, $pidSQL);
if(!$pidResult){
	die('Error'.mysqli_error($connection));
}
else{
	// generate id
	$pid = 1;
	while($pidRow = mysqli_fetch_assoc($pidResult)){
		if($pidRow["pid"]==$pid)
			$pid += 1;
		else
			break;
	}
	// insert into TABLE users
	if($data->demanding_price == null)
		$data->demanding_price = "null";
	$postSQL = "INSERT INTO products(pid, name, category, price, image, sid, status)
	VALUES('$pid', '$data->name', '$data->category', ".$data->demanding_price.", '$data->image', '$data->seller_id', 'forSale')";
	if(!mysqli_query($connection, $postSQL)){
		die('Error'.mysqli_error($connection));
	}
	else{
		$product->_id = $pid;
	}
}


// free resources
mysqli_free_result($pidResult);
// close connection
mysqli_close($connection);

// encode data into json
$json_out = json_encode($product);
//output json
echo $json_out;
?>