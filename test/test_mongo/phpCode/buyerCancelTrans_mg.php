<?php
// test case: buyer close a pending transaction / buyer don't want to buy the product anymore
// (Before delete, buyer already have an array buylist[{product_id, offering_price, status}])
// update buyer.buylist.status (whose product_id is the one which buyer want to closed), from "pending" to 'closed' 
// update products.buyerlist.status (whose buyer_id is the buyer who want to close the order) to be 'closed'
// input: the buyer with uid = 1, want to close his/her order of the product with pid = 8

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection_products = $db->products;
$collection_users = $db->users;

//step 2. set float precision
ini_set('precision', 20);

//step 3. initialize: 
//step 3-1. initialize the transaction (pid and buyer_id) which need to be cancel
$uid = 1;
$pid = 8;

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
    	$updateProduct_timeTaken = 0;
    	$updateUser_timeTaken = 0;
    	for($i=0; $i<$tries; $i++){

            //step 4. update users
            //step 4-1. update buyer.buylist.status (whose product_id is the one which buyer want to closed), from "pending" to 'closed'   
    		$query_user = array('_id'=>$uid);
    		$cursor_user = $collection_users->findOne($query_user);
	        // echo "before update: "."<br>";
	        // var_dump($cursor_user);
    		$oldBuylist = $cursor_user['buylist'];
    		$buylist_size = sizeof($oldBuylist);
    		$newBuylist = array_fill(0, $buylist_size, null);
    		for($j=0; $j<$buylist_size; $j++){
    			$buy = $oldBuylist[$j];
    			if ($buy['product_id'] == $pid)
    				$buy['status'] = "closed";
    			$newBuylist[$j] = $buy;
    		}
    		$newDocument_user = array(
    			"buylist"=>$newBuylist
    			);

    		$updateUser_timeStart = microtime(true); 
    		$ret = $collection_users->update(
	               array('_id'=>$uid), // query: find the one which is going to be updated
	               array('$set'=>$newDocument_user) // new data
	               );
    		$updateUser_timeEnd = microtime(true);
            //echo "i=".$i.": "."updateUser_timeTaken:".($updateUser_timeEnd - $updateUser_timeStart)*1000."<br>";
    		$updateUser_timeTaken += ($updateUser_timeEnd - $updateUser_timeStart)*1000; 

    		// $newQuery_user = array('_id'=>$uid);
    		// $newCursor_user = $collection_users->findOne($newQuery_user);
    		// echo "after update: "."<br>";
    		// var_dump($newCursor_user);

            //step 4-2. recover users
    		$oldDocument_user = array(
    			"buylist"=>$oldBuylist
    			);
    		$ret = $collection_users->update(
    			array('_id'=>$uid),
    			array('$set'=>$oldDocument_user)
    			);
    		// $recoverQuery_user = array('_id'=>$uid);
    		// $recoverCursor_user = $collection_users->findOne($recoverQuery_user);
    		// echo "after recover"."<br>";
    		// var_dump($recoverCursor_user);

	        //echo "-------------------------------Update Products----------------------------"."<br>";

            //step 5. update products
            //step 5-2. update products.buyerlist.status (whose buyer_id is the buyer who want to close the order) to be 'closed'
    		$query_product = array('_id'=>$pid);
    		$cursor_product = $collection_products->findOne($query_product);
	        // echo "before update: "."<br>";
	        // var_dump($cursor_product);
    		$oldBuyerlist = $cursor_product['buyerlist'];
    		$buyerlist_size = sizeof($oldBuyerlist);
    		$newBuyerlist = array_fill(0, $buyerlist_size, null);
    		for($k=0; $k<$buyerlist_size; $k++){
    			$buyer = $oldBuyerlist[$k];
    			if($buyer['buyer_id'] == $uid)
    				$buyer['status'] = "closed";
    			$newBuyerlist[$k] = $buyer;
    		}
    		$newDocument_product = array(
    			"buyerlist"=> $newBuyerlist
    			);

    		$updateProduct_timeStart = microtime(true); 
    		$ret = $collection_products->update(
	               array('_id'=>$pid), // query: find the one which is going to be updated
	               array('$set'=>$newDocument_product) // new data
	               );
    		$updateProduct_timeEnd = microtime(true);
            //echo "i=".$i.": "."updateProduct_timeTaken:".($updateProduct_timeEnd - $updateProduct_timeStart)*1000."<br>";
    		$updateProduct_timeTaken += ($updateProduct_timeEnd - $updateProduct_timeStart)*1000; 

    		// $newQuery_product = array('_id'=>$pid);
    		// $newCursor_product = $collection_products->findOne($newQuery_product);
    		// echo "after update: "."<br>";
    		// var_dump($newCursor_product);

            //step 5-3. recover products
    		$oldDocument_product = array(
    			"buyerlist"=>$oldBuyerlist
    			);
    		$ret = $collection_products->update(
    			array('_id'=>$pid),
    			array('$set'=>$oldDocument_product)
    			);
    		// $recoverQuery_product = array('_id'=>$pid);
    		// $recoverCursor_product = $collection_products->findOne($recoverQuery_product);
    		// echo "after recover"."<br>";
    		// var_dump($recoverCursor_product);
    	}
    	$time_taken = $updateProduct_timeTaken + $updateUser_timeTaken;
    	echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
    	$sum_time_taken += $time_taken;
    }

    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>