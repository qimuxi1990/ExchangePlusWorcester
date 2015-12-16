<?php

//1. create a database connection
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "123456";
$dbname = "exchangeplus_worcester";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if(mysqli_connect_errno()){
	die("Database connecction failed: " 
		. mysqli_connect_error() 
		. " (the error number is: " 
			. mysqli_connect_errno() 
			. ")"
	);
}


srand(0); 
for($product_id = 1; $product_id <= 1500; $product_id++){
	// step 1. randomize number of buyers "$numOfBuyer", if no one buy, go to next product
	$numOfBuyer = rand(-5, 10); 
	//echo "pid = " . $product_id . ": " . "num of buyers:" . $numOfBuyer . "<br>";
	if($numOfBuyer <=0) 
		continue;  
	else{
		// step 2. randomize an array of buyers (the array size is $numOfBuyer)
		$numbers = range(1, 200); // an array consist of 1, 2, ..., 200
		shuffle($numbers); // rearrange the array in a random order
		$buyers = array_slice($numbers, 0, $numOfBuyer); // get the first "numOfBuyer"(eg. 8) numbers

		//step 3. randomrize price each candidate-buyer offers; randomize transaction status (closed, pending, complete). 
		//step 3_1. get the demanding price of the seller; 
		//     get the product-status (deleted or forSale)
		//                   (1)if is deleted, all transaction "closed";  
		//                   (2)if forSale, randomize one transaction to be completed, then change products.status to "Sold")
		$query_price = "SELECT * ";
		$query_price .= "FROM products ";
		$query_price .= "WHERE pid = $product_id ";

		$price_result = mysqli_query($connection, $query_price); 
		if(!$price_result){
			die("Database query error.");
		}

		while($row = mysqli_fetch_assoc($price_result)){
			$demanding_price = $row["price"];  
			$product_status = $row["status"]; // get the product-status (deleted or forSale)

			if ($product_status == "deleted")
				$status = "closed";
			else{
			    // randomize one transaction to be complete, if $completeTrans < 0, all pending
			    // else if $completeTrans >= 0, then in the buyers[ ] array, the one with the index = $completeTrans is set to "complete" 
			    // otherwise, the status in products should be update to "sold"
				$completeTrans = rand(-5, $numOfBuyer-1); 
				if($completeTrans < 0)
					$status = "pending";
				else{
					// if there is one complete, then the status in products should be update to "sold"
					$update_pd_status = "UPDATE products ";
					$update_pd_status .= "SET status = 'sold' ";
					$update_pd_status .= "WHERE pid = $product_id ";
					$update_result = mysqli_query($connection, $update_pd_status); 
					if(!$update_result){
						die("Database update error.");
					}
				}
			}

			for($i = 0; $i < $numOfBuyer; $i++){
		        //step 3_2. randorize offering-price of each candidate buyer; randomrize transanction status
				$price = $demanding_price * rand(50, 100) * 0.01;  
				if($product_status !== "deleted" && $completeTrans >= 0){ 
					if($i == $completeTrans)
						$status = "complete";
					else
						$status = "closed";
				}

                // step 4. insert to MySQL database
				$buyer_id = $buyers[$i];  
				$sql = "INSERT INTO transactions(buyer_id, product_id, price, status)
				VALUES('$buyer_id', '$product_id', '$price', '$status')";
				if(!mysqli_query($connection, $sql))
				{
					die('Error : ' . mysqli_error($connection) );
				}

	        }//end for($i = 0; $i < $numOfBuyer; $i++)
	    } // end while($row = mysqli_fetch_assoc($price_result))

		//stpe 5. release the resource
	    mysqli_free_result($price_result);

    } // end inner else
}//end for($product_id = 11; $product_id <= 12; $product_id++)

//step 6. close connection
mysqli_close($connection);

?>