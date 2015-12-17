<?php
// test case: seller want to view buyer's contact info
// (Before this, seller already have the selllist, including product details and the buyerlist(buyer_id, offering_price, trans_status))
// query users use products.buyerlist.buyer_id
// input: the seller the seller of the product whose pid = 1, want to contact the buyer whose uid = 147

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection = $db->users;

//step 2. set float precision
ini_set('precision', 20);

//step 3 initialize: 
//step 3-1. initialize $uid =147 
$uid = 147;
//step 3-2: initialize #tires
$triesArr = array(10, 100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000);
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
    	for($i=0; $i<$tries; $i++){
    		$time_start = microtime(true); 
	        //echo "i=". $i. " start: ".$time_start."<br>";

    		$query = array('_id'=>$uid);
    		$cursor = $collection->findOne($query);

    		$time_end = microtime(true);
	        //echo "i=". $i." end: ".$time_end."<br>";

    		$time_taken += ($time_end - $time_start)*1000; 
	        //echo "i=". $i." time_taken: ".$time_taken."<br>";
    	}
    	echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
    	$sum_time_taken += $time_taken;
    }//end for($s = 0; $s < 10; $s++)
    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>