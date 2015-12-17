<?php
error_reporting(E_ERROR);
require_once('./class/class_user.php');
require_once('./class/class_product.php');
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
	$user = new user();
	$user->_id = 1;
	$user->password = 101;
	$user->name = 'James Butt';
	$user->tel = '504-621-8927';
	$user->email = 'jbutt@gmail.com';
	$user->address = '6649 N Blue Gum St, New Orleans, LA 70116';
	ini_set('precision', 20);

	$n_array = array(1, 100, 200, 500, 1000, 1500, 2000, 3000, 4000);
	//, 500, 1000, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
	foreach($n_array as $n){
		echo "# of tries = ".$n."<br>";
		$time_total = 0;
		for($j = 0; $j<10;$j++){
			$time_round = 0;
			for($i = 0;$i<$n;$i++){
				$buyer_id = intval($i/1500) + 201;
				$product_id = $i - ($buyer_id-1)*1500 + 1;
				$time_start = microtime(true);
				$insertSQL = "INSERT INTO transactions(buyer_id, product_id, price, status) "
				."VALUES ($buyer_id, $product_id, 500, 'pending')";
				$insertResult = mysqli_query($connection, $insertSQL);
				$time_round += microtime(true) - $time_start;
				mysqli_free_result($insertResult);
				$recoverSQL = "DELETE FROM transactions WHERE buyer_id > 200";
				$recoverResult = mysqli_query($connection, $recoverSQL);
				mysqli_free_result($recoverResult);
			}
			echo "the ".$j."th time: time taken: ".(1000*$time_round)."ns<br>";
			$time_total += $time_round;
		}
		echo "the average time taken: ".($time_total*1000/10)."ns<br>";
	}
	mysqli_close($connection);
}
?>