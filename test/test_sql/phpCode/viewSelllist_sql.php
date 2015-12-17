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

	$n_array = array(1, 100, 200, 400, 600, 800, 1000);//, 200, 500, 1000);
	//, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
foreach($n_array as $n){
	echo "# of tries = ".$n."<br>";
	$time_total = 0;
	for($j = 0; $j<10;$j++){
		$time_round = 0;
		for($i = 0;$i<$n;$i++){
			$pidArray = array_rand(range(1, 1500), 10);
			$time_start = microtime(true);
			$sellSQL = "SELECT * FROM products WHERE pid IN (".join(',',$pidArray).")";
			$sellResult = mysqli_query($connection, $sellSQL);
			$time_round += microtime(true) - $time_start;
			$time_start = microtime(true);
			while($sellRow = mysqli_fetch_assoc($sellResult)){
				$product = new product();
				$product->_id = $sellRow['pid'];
				$product->product_status = $sellRow['status'];
				if($sellRow['status'] != 'deleted'){
					$buyListSQL = "SELECT * FROM transactions WHERE product_id = ".$product->_id;
					$buyListResult = mysqli_query($connection, $buyListSQL);
					while(($buyRow = mysqli_fetch_assoc($buyListResult))) {
						if(($buyRow['status'] != 'complete') && ($product->product_status == 'sold'))
							continue;
					}
					$time_round += microtime(true) - $time_start;
					mysqli_free_result($buyListResult);
					$time_start = microtime(true);
				}
			}
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($sellResult);
		}
		echo "the ".$j."th time: time taken: ".(1000*$time_round)."ns<br>";
		$time_total += $time_round;
	}
	echo "the average time taken: ".($time_total*1000/10)."ns<br>";
}
mysqli_close($connection);
}
?>