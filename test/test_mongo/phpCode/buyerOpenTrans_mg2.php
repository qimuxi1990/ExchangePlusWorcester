<?php
// test case: buyer want to buy a product 
// (Before open a transaction, buyer already have a list of products which is forSale, no need to test if the product is sold or deleted)
// add product_id, offering_price($500), status('pending') to users.buylist
// add buyer_id, offering_price($500), status('pending') to products.buyerlist
// input: create 10 new user (uid = 201~210, othe information is the same as the info whose uid=1), let these new user buy product 1~1500, at most buy 1500*10 times

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection_users = $db->users;
$collection_products = $db->products;

//step 2. set float precision
ini_set('precision', 20);

//step 3. initialize
//step 3-1. create new users with uid = 201 ~ 210, other info the same with uid = 1
$query = array('_id'=>1);
$cursor = $collection_users->findOne($query);
$password = $cursor['password']; 
$name = $cursor['name'];
$tel = $cursor['tel'];
$email = $cursor['email'];
$address = $cursor['address'];
$selllist = array();
$buylist = array();
for($num = 0; $num < 10; $num++){
	$document = array(
		"_id"=> 201+$num,
		"password"=> $password,
		"name"=> $name,
		"tel"=> $tel,
		"email"=> $email,
		"address"=> $address,
		"selllist"=>$selllist,
		"buylist"=>$buylist
		);
	$collection_users->insert($document);
}
//step 3-2. initialize #tries, number of new users used in this test, and how many products each user buy
// $triesArr = array(10, 100, 200, 500, 1000, 2000, 4000, 6000, 8000, 10000);
$triesArr = array(1, 3000);
//$triesArr = array(10);
for($t = 0; $t < sizeof($triesArr); $t++){ 
	$tries = $triesArr[$t];
	echo "# of tries = ".$tries."<br>";
	$modular = $tries % 1500;
	if($modular == 0)
        $numOfNewUsers = (int)($tries/1500); // number of new users used in this test
      else
       $numOfNewUsers = (int)($tries/1500) + 1;
    $maxUid = 200 + $numOfNewUsers; // max uid used in this test
    $maxUidBuy = $tries - ($numOfNewUsers-1)*1500; // number of product which the user with "$maxUid" buy

    //step 3-3. initialize $sum_time_taken = 0, 
    $sum_time_taken = 0;

    //step 3-4. initialize # of times tested, an array of size 10 (test 10 times to get the average time_taken)
    for($s = 0; $s < 10; $s++){ // test 10 times, and compute the average time_taken
        //step 3-5. each time, initialize the time_taken to be 0
    	$updateProduct_timeTaken = 0;
    	$updateUser_timeTaken = 0;

        //step 4. update 
      for($uid = $maxUid; $uid > 200; $uid--){
        if($uid == $maxUid)
         $numOfProductBought = $maxUidBuy;
       else
         $numOfProductBought = 1500;
       for($pid = 1; $pid <= $numOfProductBought; $pid++){
	            //step 4-1. update users: add product_id, offering_price, status('pending') to users.buylist
         $newOrder1 = array(
          "product_id" => $pid,
          "offering_price" => (float)500,
          "status" => "pending"
          );

         $query_user = array('_id'=>$uid);
         $cursor_user = $collection_users->findOne($query_user);
         // echo "users before update: "."<br>";
         // var_dump($cursor_user);

         $oldBuylist = $cursor_user['buylist'];
         $newBuylist = $oldBuylist;
         array_push($newBuylist, $newOrder1);

         $updateUser_timeStart = microtime(true); 
         $ret = $collection_users->update(
	                   array('_id'=>$uid), // query: find the one which is going to be updated
	                   array('$set'=>array("buylist"=>$newBuylist)) // new data
	                   );
         $updateUser_timeEnd = microtime(true);
                //echo "pid=".$pid.": "."updateUser_timeTaken:".($updateUser_timeEnd - $updateUser_timeStart)*1000."<br>";
         $updateUser_timeTaken += ($updateUser_timeEnd - $updateUser_timeStart)*1000; 

         // $newQuery_user = array('_id'=>$uid);
         // $newCursor_user = $collection_users->findOne($newQuery_user);
         // echo "users after update: "."<br>";
         // var_dump($newCursor_user);

         // echo "-------------------------------Update Products----------------------------"."<br>";

         //step 4-2. update products: add buyer_id, offering_price($500), status('pending') to products.buyerlist
         $newOrder2 = array(
          "buyer_id" => $uid,
          "offering_price" => (float)500,
          "status" => "pending"
          );

         $query_product = array('_id'=>$pid);
         $cursor_product = $collection_products->findOne($query_product);
         // echo "products before update: "."<br>";
         // var_dump($cursor_product);

         $oldBuyerlist = $cursor_product['buyerlist'];
         $newBuyerlist = $oldBuyerlist;
         array_push($newBuyerlist, $newOrder2);

         $updateProduct_timeStart = microtime(true); 
         $ret = $collection_products->update(
	                    array('_id'=>$pid), // query: find the one which is going to be updated
	                    array('$set'=>array("buyerlist"=>$newBuyerlist)) // new data
	                    );
         $updateProduct_timeEnd = microtime(true);
                //echo "pid=".$pid.": "."updateProduct_timeTaken:".($updateProduct_timeEnd - $updateProduct_timeStart)*1000."<br>";
         $updateProduct_timeTaken += ($updateProduct_timeEnd - $updateProduct_timeStart)*1000; 

         // $newQuery_product = array('_id'=>$pid);
         // $newCursor_product = $collection_products->findOne($newQuery_product);
         // echo "product after update: "."<br>";
         // var_dump($newCursor_product);

    		}//end for($pid = 1; $pid <= $numOfProductBought; $pid++)
    	}//end for($uid = $maxUid; $uid > 200; $uid--)

    	$time_taken = $updateProduct_timeTaken + $updateUser_timeTaken;
    	echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
    	$sum_time_taken += $time_taken;

      //step 6. recover database
      //step 6-1. delete buylist of user where uid > 200
      $empty_buylist = array();
      $ret = $collection_users->findAndModify(
        array('_id'=> array('$gt'=>200)),
            array('$set'=>array("buylist"=>$empty_buylist)) // new data
            );
      //step 6-2. delete products.buyerlist {buyer_id, offering_price($500), status('pending')} where buyer_id > 200 
      for($rec_pid = 1; $rec_pid <= 1500; $rec_pid++){
        $recoverQuery_product = array('_id'=>$rec_pid);
        $recoverCursor_product = $collection_products->findOne($recoverQuery_product);
        $buyerlist = $recoverCursor_product['buyerlist'];
        for($num = 0; $num < sizeof($buyerlist); $num++){
          $buyer = $buyerlist[num];
          if($buyer['buyer_id'] > 200)
            unset($buyerlist[num]);
        }
      }

    }//end for($s = 0; $s < 10; $s++)

    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";   

}//end for($t = 0; $t < sizeof($triesArr); $t++)

//step 6. delete users whose uid > 200
$collection_users->remove( array('_id'=> array('$gt'=>200 ))); 

?>