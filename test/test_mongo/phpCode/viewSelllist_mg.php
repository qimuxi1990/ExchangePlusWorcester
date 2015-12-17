<?php
// test case: user want to view his/her selllist
// input: a random array of 10 pid
// output: user's selllist, including product details and buyerlist(buyer_id, offering_price, trans_status)

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection = $db->products;

//step 2. set float precision
ini_set('precision', 20);

//step 3 initialize: 
//step 3-1: initialize #tires
$triesArr = array(10, 100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000);
for($t = 0; $t < sizeof($triesArr); $t++){ 
    $tries = $triesArr[$t];
    echo "# of tries = ".$tries."<br>";
    //step 3-2. initialize $sum_time_taken = 0, 
    $sum_time_taken = 0;
    //step 3-3. initialize # of times tested, an array of size 10 (test 10 times to get the average time_taken)
    for($s = 0; $s < 10; $s++){ // test 10 times, and compute the average time_taken
        //step 3-4. each time, initialize the time_taken to be 0
        $time_taken = 0;
        for($i=0; $i<$tries; $i++){
            //step 3-5. initialize an array of pids, size = 10
            $numbers = range(1, 1500); // an array consist of 1, 2, ..., 1500
            shuffle($numbers); // rearrange these numbers in a random order
            $pids = array_slice($numbers, 0, 10); // get the first 10 numbers

            //step 4. test
            for($j=0; $j<9; $j++){
                $pid = $pids[$j];
                $time_start = microtime(true);    
                $query = array('_id'=>$pid);
                $cursor = $collection->findOne($query);
                $time_end = microtime(true);   	
                $time_taken += ($time_end - $time_start)*1000;                 
            }
        }
        echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
        $sum_time_taken += $time_taken;
    }
    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>