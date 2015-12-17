<?php
// test case: buyer want to search product based on category, 
// query products based on category, database only return products with the status "forSale" (not those has been sold or deleted)
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

//step 3-2: initialize #tires
//$triesArr = array(10, 100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000);
$triesArr = array(10, 6000, 8000, 10000, 20000, 40000, 60000, 80000, 100000, 150000, 200000);
for($t = 0; $t < sizeof($triesArr); $t++){ 
	$tries = $triesArr[$t];
	echo "# of tries = ".$tries."<br>";
    //step 3-3. initialize $sum_time_taken = 0, 
	$sum_time_taken = 0;
    //step 3-4. initialize # of times tested, an array of size 10 (test 10 times to get the average time_taken)
    for($s = 0; $s < 10; $s++){ // test 10 times, and compute the average time_taken
        //step 3-5. each time, initialize the time_taken to be 0
    	$time_taken = 0;

        //step 4. test
    	for($i=1; $i<=$tries; $i++){
    		$time_start = microtime(true); 
    		$query = array('$and' => array(
    			array('category'=>array('$regex' => 'Phone')), 
    			array('product_status' => 'forSale')
    			));
    		$cursor = $collection->find($query);

    		$time_end = microtime(true);
    		$time_taken += ($time_end - $time_start)*1000; 
    	}
    	echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
    	$sum_time_taken += $time_taken;
    }//end for($s = 0; $s < 10; $s++)
    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>