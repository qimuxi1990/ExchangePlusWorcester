<?php
// test case: user sign up
// input: detail user information where uid = 1 (query max _id before insert)

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection = $db->users;

//step 2. set float precision
ini_set('precision', 20);

$triesArr = array(100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000, 15000, 20000, 30000, 40000, 60000);
//$triesArr = array(10, 10000);//, 15000, 20000, 30000, 40000);
for($t = 0; $t < sizeof($triesArr); $t++){ 
	//step 3. initialize: 
	//step 3-1. initialize # of tires, $sum_time_taken = 0, 
	//          and initialize # of times tested, an array of size 10 (test 10 times to get the average time_taken)
	$tries = $triesArr[$t];
	echo "# of tries = ".$tries."<br>";
	$sum_time_taken = 0;
	for($s = 0; $s < 10; $s++){ // test 10 times, and compute the average time_taken
        //step 3-2: each time, initialize time_taken to be 0
		$findMax_time_taken = 0;
		$insert_time_taken = 0;
        //step 3-2: initialize the user information (query from users where uid = 1) for insert into users
		$query = array('_id'=>1);
		$cursor = $collection->findOne($query);
		$password = $cursor['password']; 
		$name = $cursor['name'];
		$tel = $cursor['tel'];
		$email = $cursor['email'];
		$address = $cursor['address'];
		$selllist = array();
		$buylist = array();

		for($i=0; $i<$tries; $i++){
            //step 3-3:initialize _id: 
            // get the max uid (sort the array according to _id in descending order, then get the first one), 
            // then set the id of the inserted user to be max + 1
			$findMax_time_start = microtime(true); 
			$max_cursor = $collection->find(
				array(),
				array('_id' => 1)
				) -> sort(array('_id' => -1)) -> limit(1); 
			foreach($max_cursor as $max_doc){
				$max = $max_doc['_id'];
			}
			$findMax_time_end = microtime(true); 
	        //echo "i=".$i.": "."findMax_time_taken:".($findMax_time_end - $findMax_time_start)."<br>";
			$findMax_time_taken += ($findMax_time_end - $findMax_time_start)*1000; 

            //step 3-4:initialize docoment for insert: 
			$document = array(
				"_id"=> ++$max,
				"password"=> $password,
				"name"=> $name,
				"tel"=> $tel,
				"email"=> $email,
				"address"=> $address,
				"selllist"=>$selllist,
				"buylist"=>$buylist
				);

            //step 4: test insert
			$insert_time_start = microtime(true); 
			$collection->insert($document);
			$insert_time_end = microtime(true);
	        //echo "i=".$i.": "."insert_time_taken:".($insert_time_end - $insert_time_start)."<br>";
			$insert_time_taken += ($insert_time_end - $insert_time_start)*1000; 
		}//end for($i=0; $i<$tries; $i++) 

		$time_taken = $findMax_time_taken + $insert_time_taken;		
		echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
		$sum_time_taken += $time_taken;

        //step 5: recover the database to original state: delete insertion
		$collection->remove( array('_id'=> array('$gt'=>200 ))); 

	}//end for($s = 0; $s < 10; $s++)

	$av_time_taken = $sum_time_taken/10;	
	echo "the average time taken: ".$av_time_taken."<br>";

}//end for($t = 0; $t < sizeof($triesArr); $t++) 

?>