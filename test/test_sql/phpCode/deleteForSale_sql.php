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
	$product = new product();
	$product->name = 'Samsung - Gear S Smartwatch 40mm Chrome AT&T - Whi';
	$product->category = 'Cell Phones';
	$product->demanding_price = 329.99;
	$product->image = 'http://img.bbystatic.com/BestBuy_US/images/products/1044/1044393_sc.jpg';
	$product->seller_id = 76;
	ini_set('precision', 20);

	$n_array = array(1, 50);
	//, 500, 1000, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
	foreach($n_array as $n){
		echo "# of tries = ".$n."<br>";
		$time_total = 0;
		for($j = 0; $j<10;$j++){
			$time_round = 0;
			for($i = 0;$i<$n;$i++){
				$time_start = microtime(true);
				$deleteSQL = "UPDATE transactions SET "
				."status = 'closed' "
				."WHERE product_id = 8; "
				."UPDATE products SET "
				."status = 'deleted' "
				."WHERE pid = 8";
				$deleteResult = mysqli_multi_query($connection, $deleteSQL);
				$time_round += microtime(true) - $time_start;
				do {
					if ($result = mysqli_store_result($connection)) {
						mysqli_free_result($result);
					}
				} while (mysqli_next_result($connection));
				$recoverSQL = "UPDATE transactions SET "
				."status = 'pending' "
				."WHERE product_id = 8; "
				."UPDATE products SET "
				."status = 'forSale' "
				."WHERE pid = 8";
				$recoverResult = mysqli_multi_query($connection, $recoverSQL);
				do {
					if ($result = mysqli_store_result($connection)) {
						mysqli_free_result($result);
					}
				} while (mysqli_next_result($connection));
			}
			echo "the ".$j."th time: time taken: ".(1000*$time_round)."ns<br>";
			$time_total += $time_round;
		}
		echo "the average time taken: ".($time_total*1000/10)."ns<br>";
	}
	mysqli_close($connection);
}
?>