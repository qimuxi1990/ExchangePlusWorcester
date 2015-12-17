<?php
// test case: buyer want to search product based on category, 
// database only return products with the status "forSale" (not those has been sold or deleted)
// input: category "Phone" (case insensitive)

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection= $db->products;

//step 2. set float precision
ini_set('precision', 20);

//step 3. initialize
//step 3-1. initialize category
$category = "Phone";
$time_taken = 0;
$tries = 10000;
//step 4. test
for($i=1; $i<=$tries; $i++){
	$time_start = microtime(true); 
	echo "time start: ".$time_start."<br>";
	$query = array('$and' => array(
		array('category'=>array('$regex' => 'Phone')), 
		array('product_status' => 'forSale')
		));
	$cursor = $collection->find($query);

	$time_end = microtime(true);
	echo "time end: ".$time_end."<br>";
	echo "time taken: ". ($time_end - $time_start)* 1000 . "<br>";
	$time_taken += ($time_end - $time_start)*1000; 
}
echo "total time taken: ".$time_taken." ns"."<br>";
?>