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
	ini_set('precision', 20);

	$n_array = array(10, 200, 500, 1000, 1500, 2000, 3000, 4000);//, 500, 1000);
	//, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
foreach($n_array as $n){
	echo "# of tries = ".$n."<br>";
	$time_total = 0;
	for($j = 0; $j<10;$j++){
		$time_round = 0;
		for($i = 0;$i<$n;$i++){
			$pidArray = array_rand(range(1, 1500), 10);
			$time_start = microtime(true);
			$orderSQL = "SELECT products.*, transactions.price as offering_price, transactions.status as transaction_status FROM products JOIN transactions ON pid = product_id "
			."WHERE pid IN (".join(',',$pidArray).") AND buyer_id = ".$buyer_id;
			$orderResult = mysqli_query($connection, $orderSQL);
			while($orderRow = mysqli_fetch_assoc($orderResult)){
			}
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($orderResult);
		}
		echo "the ".$j."th time: time taken: ".(1000*$time_round)."ns<br>";
		$time_total += $time_round;
	}
	echo "the average time taken: ".($time_total*1000/10)."ns<br>";
}
mysqli_close($connection);
}
?>