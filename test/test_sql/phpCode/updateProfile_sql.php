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

	//$n_array = array(10, 200, 500, 1000);
	//, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
	$n_array = array(1000);
	foreach($n_array as $n){
		echo "# of tries = ".$n."<br>";
		$time_total = 0;
		for($j = 0; $j<10;$j++){
			$time_round = 0;
			for($i = 0;$i<$n;$i++){
				$user->email = "jbutt".$i."@gmail.com";
				$time_start = microtime(true);
				$updateSQL = "UPDATE users SET email = '$user->email' WHERE uid = $user->_id";
				$updateResult = mysqli_query($connection, $updateSQL);
				$time_round += microtime(true) - $time_start;
				mysqli_free_result($updateResult);
			}		
			$recoverSQL = "UPDATE users SET email = 'jbutt@gmail.com' WHERE uid = $user->_id";
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