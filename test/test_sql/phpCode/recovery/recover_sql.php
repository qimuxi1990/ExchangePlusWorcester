<?php
function recover(){
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
	else{
		$emptySQL1 = "DELETE FROM `products` WHERE 1;";
		$emptySQL2 = "DELETE FROM `users` WHERE 1;";
		$emptySQL3 = "DELETE FROM `transactions` WHERE 1;";
		mysqli_free_result(mysqli_query($connection, $emptySQL1));
		mysqli_free_result(mysqli_query($connection, $emptySQL2));
		mysqli_free_result(mysqli_query($connection, $emptySQL3));
		require("database_sql.php");
		$sql = '';
		foreach($users as $user){
			$columns = implode(", ",array_keys($user));
			$values  = implode("', '", $user);
			if($sql != '')
				$sql .= ", ('$values')";
			else
				$sql = "INSERT INTO `users`($columns) VALUES ('$values')";
		}
		mysqli_free_result(mysqli_query($connection, $sql));
		$sql = '';
		foreach($products as $product){
			$pid = $product['pid'];
			$name = $product['name']; 
			$name = str_replace("'", "''", $name);
			// TODO error in Apple - iPhoneA
			$category = $product['category'];
			$category= str_replace("'", "''", $category);
			$price = $product['price']; 
			$image = $product['image']; 
			$sid = $product['sid']; 
			$status = $product['status']; 
			if ($sql != '')
				$sql .= ", ('$pid', '$name', '$category', '$price', '$image', '$sid', '$status')";
			else
				$sql = "INSERT INTO products(pid, name, category, price, image, sid, status) VALUES('$pid', '$name', '$category', '$price', '$image', '$sid', '$status')";
		}
		mysqli_free_result(mysqli_query($connection, $sql));
		$sql = '';
		foreach($transactions as $transaction){
			$columns = implode(", ",array_keys($transaction));
			$values  = implode("', '", $transaction);
			if($sql != '')
				$sql .= ", ('$values')";
			else
				$sql = "INSERT INTO `transactions`($columns) VALUES ('$values')";
		}
		mysqli_free_result(mysqli_query($connection, $sql));
		echo "transactions!<br>";
		mysqli_close($connection);
	}
}
?>