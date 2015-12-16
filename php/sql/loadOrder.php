<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');
// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$buyer_id = $data[0];
$pidArray = $data[1];
$buyProducts = array();
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
	$orderSQL = "SELECT products.*, transactions.price as offering_price, transactions.status as transaction_status FROM products JOIN transactions ON pid = product_id "
	."WHERE pid IN (".join(',',$pidArray).") AND buyer_id = ".$buyer_id;
	$orderResult = mysqli_query($connection, $orderSQL);
	if(!$orderResult) {
		die('Error : ' . mysqli_error($connection));
	}else {
		while($OrderRow = mysqli_fetch_assoc($orderResult)){
			$product = new product();
			$product->_id = $OrderRow['pid'];
			$product->name = $OrderRow['name'];
			$product->category = $OrderRow['category'];
			$product->demanding_price = $OrderRow['price'];
			$product->image = $OrderRow['image'];
			$product->seller_id = $OrderRow['sid'];
			$product->product_status = $OrderRow['status'];
			$product->buyList = array();
			array_push($product->buyList, array('buyer_id'=>$buyer_id, 'offering_price'=>$OrderRow['offering_price'], 'transaction_status'=>$OrderRow['transaction_status']));
			array_push($buyProducts, $product);
		}
	}
	// free resources
	mysqli_free_result($orderResult);
	// close connection
	mysqli_close($connection);
}

// encode data into json
$json_out = json_encode($buyProducts);
//output json
echo $json_out;
?>