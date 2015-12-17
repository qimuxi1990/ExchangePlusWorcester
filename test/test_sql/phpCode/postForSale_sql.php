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

	$n_array = array(1, 200);//, 500);
	//, 500, 1000, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
foreach($n_array as $n){
	echo "# of tries = ".$n."<br>";
	$time_total = 0;
	for($j = 0; $j<10;$j++){
		$time_round = 0;
		for($i = 0;$i<$n;$i++){
			$time_start = microtime(true);
			$maxSQL = "SELECT MAX(pid) FROM products WHERE 1";
			$maxResult = mysqli_query($connection, $maxSQL);
			$product->_id = mysqli_fetch_array($maxResult)[0]+1;
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($maxResult);
			$time_start = microtime(true);
			$insertSQL = "INSERT INTO products(pid, name, category, price, image, sid, status)
			VALUES($product->_id, '$product->name', '$product->category', ".$product->demanding_price.", '$product->image', '$product->seller_id', 'forSale')";
			$insertResult = mysqli_query($connection, $insertSQL);
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($insertResult);
		}		
		$recoverSQL = "DELETE FROM products WHERE pid > 1500";
		$recoverResult = mysqli_query($connection, $recoverSQL);
		mysqli_free_result($recoverResult);
		echo "the ".$j."th time: time taken: ".(1000*$time_round)."ns<br>";
		$time_total += $time_round;
	}
	echo "the average time taken: ".($time_total*1000/10)."ns<br>";
}
mysqli_close($connection);
}
?>