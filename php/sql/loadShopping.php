<?php
require_once('../class/class_user.php');
require_once('../class/class_product.php');
// fetch data from the input steam "php://input"
$url = "php://input";
$file = file_get_contents($url); 
$json_in = utf8_encode($file); 
$data = json_decode($json_in);
$keyword_name = $data->name;
$keyword_category = $data->category;
$products = array();
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
	if($keyword_name != '' || $keyword_category != ''){
		$productsSQL = "SELECT * FROM products WHERE status = 'forSale' AND name LIKE '%"
		.$keyword_name."%' AND category LIKE '%".$keyword_category."%'";
		$productsResult = mysqli_query($connection, $productsSQL);
		if(!$productsResult) {
			die('Error : ' . mysqli_error($connection));
		}else {
			while($productsRow = mysqli_fetch_assoc($productsResult)){
				$product = new product();
				$product->_id = $productsRow['pid'];
				$product->name = $productsRow['name'];
				$product->category = $productsRow['category'];
				$product->demanding_price = $productsRow['price'];
				$product->image = $productsRow['image'];
				$product->seller_id = $productsRow['sid'];
				$product->buyList = array();
				$product->product_status = 'forSale';
				array_push($products, $product);
			}
		}
	// free resources
		mysqli_free_result($productsResult);
	}
	// close connection
	mysqli_close($connection);
}

// encode data into json
$json_out = json_encode($products);
//output json
echo $json_out;
?>