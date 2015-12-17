<?php
//create a nosql database connection
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection = $db->users;

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

for($index = 1; $index <= 200; $index++){
	//echo "index=".$index."<br>";
	$sql_user = "SELECT * ";
	$sql_user .= "FROM users ";
	$sql_user .= "WHERE uid = $index ";

	$sqluser_result = mysqli_query($connection, $sql_user); 
	if(!$sqluser_result){
		die("sql_user query: "."Database query error.");
	}

	while($user= mysqli_fetch_assoc($sqluser_result)){
		$uid = $user["uid"];

        // SellList: use uid to query pids and prices to construct the selllist 
        // query products to get pid and price
		$sql_product = "SELECT * ";
		$sql_product .= "FROM products ";
		$sql_product .= "WHERE sid = $uid ";
		$sqlproduct_result = mysqli_query($connection, $sql_product); 
		if(!$sqlproduct_result){
			die("sql_product query: "."Database query error.");
		}

		$maxsize = mysqli_num_rows($sqlproduct_result);
		$selllist = array();
		for($i = 0; $i < $maxsize; $i++){
			while($product = mysqli_fetch_assoc($sqlproduct_result)){
				$pid = (int)$product["pid"];
				array_push($selllist, $pid);
				
			}
		}

	    // BuyList: use uid to query pids and prices to construct the buylist 
		$sql_transaction = "SELECT * ";
		$sql_transaction .= "FROM transactions ";
		$sql_transaction .= "WHERE buyer_id = $uid ";
		$sqltrans_result = mysqli_query($connection, $sql_transaction); 
		if(!$sqltrans_result){
			die("Database query error.");
		}

		$maxsize2 = mysqli_num_rows($sqltrans_result);
		$buylist = array();
		for($i = 0; $i < $maxsize; $i++){
			while($transaction = mysqli_fetch_assoc($sqltrans_result)){
				$arr2 = array("product_id"=>(int)$transaction["product_id"],
					"offering_price"=>(float)$transaction["price"],
					"status"=>$transaction["status"]
					);
				array_push($buylist, $arr2);
			}
		}

		$document = array(
			"_id"=> (int)$uid,
			"password"=> $user["password"],
			"name"=> $user["name"],
			"tel"=> $user["tel"],
			"email"=> $user["email"],
			"address"=> $user["address"],
			"selllist"=> $selllist,
			"buylist"=> $buylist,
			);

		$collection->insert($document);
	}

}

//release the resource
mysqli_free_result($sqluser_result);
mysqli_free_result($sqlproduct_result);
mysqli_free_result($sqltrans_result);

//close connection
mysqli_close($connection);
?>