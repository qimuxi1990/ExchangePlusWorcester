<?php
// test case: user update his/her profile
// input: user whose uid = 1, update his/her email address to "jbutt".$i."@gmail.com"($i: 0 ~ #tries)

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection = $db->users;

//step 2. set float precision
ini_set('precision', 20);

//step 3 initialize: 
//step 3-1. initialize $uid =1 
$uid = 1;

//step 3-2: initialize #tires
$triesArr = array(100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000);
for($t = 0; $t < sizeof($triesArr); $t++){ 
	$tries = $triesArr[$t];
	echo "# of tries = ".$tries."<br>";
	//step 3-3. initialize $sum_time_taken = 0, 
	$sum_time_taken = 0;
	//step 3-4. initialize # of times tested, an array of size 10 (test 10 times to get the average time_taken)
	for($s = 0; $s < 10; $s++){ // test 10 times, and compute the average time_taken
		//step 3-5. each time, initialize the time_taken to be 0
		$time_taken = 0;
		for($i=0; $i<$tries; $i++){
			//step 3-6. initialize the updated email
			$email = "jbutt".$i."@gmail.com";

            //step 4: update database
			$time_start = microtime(true); 
			// echo "i=".$i.": "."time_start:".$time_start."<br>";
			$ret = $collection->update(
	            array("_id"=>$uid), // query: find the one which is going to be updated
	            array('$set'=>array("email"=>$email)) // new data
	            );
			$time_end = microtime(true); 
			// echo "i=".$i.": "."time_end:".$time_end."<br>";
			// echo "i=".$i.": "."time_taken:".($time_end - $time_start)*1000 ."<br>";
			$time_taken += ($time_end - $time_start)*1000;
		}
		echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
		$sum_time_taken += $time_taken;

        //step 5:recover the database to original state
		$recover_ret = $collection->update(
	    array("_id"=>$uid), // query: find the one which is going to be updated
	    array('$set'=>array("email"=>"jbutt@gmail.com")) // new data
	    );

    }//end for($s = 0; $s < 10; $s++)
    $av_time_taken = $sum_time_taken/10;	
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}//end for($t = 0; $t < sizeof($triesArr); $t++)
?>