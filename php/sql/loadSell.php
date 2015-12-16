<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');
// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$sellProducts = array();
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
	$sellSQL = "SELECT * FROM products WHERE pid IN (".join(',',$data).")";
	$sellResult = mysqli_query($connection, $sellSQL);
	if(!$sellResult) {
		die('Error : ' . mysqli_error($connection));
	}else {
		while($sellRow = mysqli_fetch_assoc($sellResult)){
			$product = new product();
			$product->_id = $sellRow['pid'];
			$product->name = $sellRow['name'];
			$product->category = $sellRow['category'];
			$product->demanding_price = $sellRow['price'];
			$product->image = $sellRow['image'];
			$product->seller_id = $sellRow['sid'];
			$product->buyList = array();
			$product->product_status = $sellRow['status'];
			if($product->product_status != 'deleted'){
				$buyListSQL = "SELECT * FROM transactions WHERE product_id = ".$product->_id;
				$buyListResult = mysqli_query($connection, $buyListSQL);
				if(!$buyListResult) {
					die('Error : ' . mysqli_error($connection));
				}else {
					while(($buyRow = mysqli_fetch_assoc($buyListResult))) {
						if(($buyRow['status'] != 'complete') && ($product->product_status == 'sold'))
							continue;
						array_push($product->buyList, array('buyer_id'=>$buyRow['buyer_id'], 'offering_price'=>$buyRow['price'], 'transaciton_status'=>$buyRow['status']));
					}
				}
				mysqli_free_result($buyListResult);
			}
			array_push($sellProducts, $product);
		}
	}
	// free resources
	mysqli_free_result($sellResult);
	// close connection
	mysqli_close($connection);
}

// encode data into json
$json_out = json_encode($sellProducts);
//output json
echo $json_out;
?>