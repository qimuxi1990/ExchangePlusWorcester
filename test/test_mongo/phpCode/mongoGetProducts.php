<?php
//create a nosql database connection
//config
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
//connect to db
$m = new MongoClient();
$db = $m->$dbname;
//select a collection
$collection = $db->products;

//create sql database connection
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

for($index = 1; $index <= 1500; $index++){
	$sql_products = "SELECT * ";
	$sql_products .= "FROM products ";
	$sql_products .= "WHERE pid = $index ";

	$sqlproducts_result = mysqli_query($connection, $sql_products); 
	if(!$sqlproducts_result){
		die("Database query error.");
	}

	while($product= mysqli_fetch_assoc($sqlproducts_result)){
        // get product_status
		$product_status = $product["status"];
	    // for each product, get buyer list
		$buyerlist = array();
		$sql_transactions = "SELECT * ";
		$sql_transactions .= "FROM transactions ";
		$sql_transactions .= "WHERE product_id = $index ";
		$sqltrans_result = mysqli_query($connection, $sql_transactions); 
		if(!$sqltrans_result){
			die("Database query error.");
		}
		$maxsize = mysqli_num_rows($sqltrans_result);
		//echo "number of candidate buyers: ".$maxsize."<br>";
		for($i = 0; $i < $maxsize; $i++){
			while($transaction = mysqli_fetch_assoc($sqltrans_result)){
				$arr = array("buyer_id"=>(int)$transaction["buyer_id"],
					"offering_price"=>(float)$transaction["price"],
					"status"=>$transaction["status"]
					);
				//var_dump($arr);
				array_push($buyerlist, $arr);
			}
		}


		$document = array(
			"_id"=> $index,
			"name"=> $product["name"],
			"category"=> $product["category"],
			"demanding_price"=> (float)$product["price"],
			"image"=> $product["image"],
			"seller_id"=> (int)$product["sid"],
			"product_status"=> $product_status,
			"buyerlist"=> $buyerlist,
			);

		$collection->insert($document);
	}
}
// release the resource
mysqli_free_result($sqlproducts_result);
mysqli_free_result($sqltrans_result);

// close connection
mysqli_close($connection);
?>