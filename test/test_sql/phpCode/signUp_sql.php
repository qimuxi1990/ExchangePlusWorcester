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

	
	$n_array = array(100, 300, 400, 600, 700, 800, 900);//, 4000);
	//, 2000, 4000, 6000, 8000, 10000, 15000, 200000);
foreach($n_array as $n){
	echo "# of tries = ".$n."<br>";
	$time_total = 0;
	for($j = 0; $j<10;$j++){
		$time_round = 0;
		for($i = 0;$i<$n;$i++){
			$time_start = microtime(true);
			$maxSQL = "SELECT MAX(uid) FROM users WHERE 1";
			$maxResult = mysqli_query($connection, $maxSQL);
			$user->_id = mysqli_fetch_array($maxResult)[0]+1;
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($maxResult);
			$time_start = microtime(true);
			$insertSQL = "INSERT INTO users(uid, password, name, tel, email, address) "
			."VALUES ($user->_id, $user->password, '$user->name', '$user->tel', '$user->email', '$user->address')";
			$insertResult = mysqli_query($connection, $insertSQL);
			$time_round += microtime(true) - $time_start;
			mysqli_free_result($insertResult);
		}		
		$recoverSQL = "DELETE FROM users WHERE uid > 200";
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