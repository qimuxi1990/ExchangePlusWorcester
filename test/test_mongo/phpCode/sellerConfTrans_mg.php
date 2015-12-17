<?php
// test case: seller agree on one transaction, and confirm to sell the product 
// (Before agree, seller already have an array of selllist, including product details and buyerlist(buyer_id, offering_price, trans_status))
// update products.product_status to 'sold' 
// update products.buyerlist[].status to be 'complete'(where buyer_id) or 'closed'
// update the buyer.buylist[].status to 'complete', other candidate buyers.buylist[].status to 'closed'
// input: the seller whose sid = 86, agree on the transaction where product_id = 8, buyer_id =1 

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection_products = $db->products;
$collection_users = $db->users;

//step 2. set float precision
$_p = ini_get('precision');
ini_set('precision', 20);

//step 3 initialize: 
//step 3-1. initialize the transaction (pid and buyer_id) which need to be confirmed
$pid = 8;
$buyer_id = 1;

//step 3-2: initialize #tires
//$triesArr = array(10, 100, 200, 500, 1000);//, 2000, 4000, 6000, 8000, 10000);
//$triesArr = array(10, 2000, 4000);//, 6000, 8000, 10000);
//$triesArr = array(10, 6000);//, 6000, 8000, 10000);
$triesArr = array(10, 10000);
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
            //step 4. update products
            //step 4-1. update products.product_status to 'sold', 
            //          update products.buyerlist[].status to be 'complete'(where buyer_id) or 'closed'
            $query_product = array('_id'=>$pid);
            $cursor_product = $collection_products->findOne($query_product);
            // echo "before update: "."<br>";
            // var_dump($cursor_product);

            $oldBuyerlist = $cursor_product['buyerlist'];
            $buyerlist_size = sizeof($oldBuyerlist);
            $buyerid_list = array_fill(0, $buyerlist_size, null); //used to query users
            $newBuyerlist = array_fill(0, $buyerlist_size, null); 
            for($j=0; $j<$buyerlist_size; $j++){
                $buyer = $oldBuyerlist[$j];
                $buyerid_list[$j] = $buyer['buyer_id'];
                if($buyer['buyer_id'] == $buyer_id)
                    $buyer['status'] = "complete";
                else
                    $buyer['status'] = "closed";
                $newBuyerlist[$j] = $buyer;
            }
            $newDocument_product = array(
                "product_status"=> "sold",
                "buyerlist"=> $newBuyerlist
                );

            $updateProduct_timeStart = microtime(true); 
            $ret = $collection_products->update(
	            array('_id'=>$pid), // query: find the one which is going to be updated
	            array('$set'=>$newDocument_product) // new data
               );
            $updateProduct_timeEnd = microtime(true); 
            //echo "i=".$i.": "."updateProduct_timeTaken:".($updateProduct_timeEnd - $updateProduct_timeStart)."<br>";
            $updateProduct_timeTaken += ($updateProduct_timeEnd - $updateProduct_timeStart)*1000; 

            // $newQuery_product = array('_id'=>$pid);
            // $newCursor_product = $collection_products->findOne($newQuery_product);
            // echo "after update"."<br>";
            // var_dump($newCursor_product);

            //step 4_2. recover the collection_products "products" to original state
            $recoverDocument_product = array(
             "product_status"=> "forSale",
             "buyerlist"=> $oldBuyerlist
             );
            $ret = $collection_products->update(
	            array('_id'=>$pid), // query: find the one which is going to be updated
	            array('$set'=>$recoverDocument_product) // new data
             );
            // $recoverQuery_product = array('_id'=>$pid);
            // $recoverCursor_product = $collection_products->findOne($recoverQuery_product);
            // echo "after recover"."<br>";
            // var_dump($recoverCursor_product);

            // echo "-------------------------------Update User----------------------------"."<br>";

            //step 5. update users
            //step 5-1. update the buyer.buylist[].status to 'complete', other candidate buyers.buylist[].status to 'closed'
            foreach($buyerid_list as $candidateBuyer_id){
                $query_user = array('_id'=>$candidateBuyer_id);
                $cursor_user = $collection_users->findOne($query_user);
                // echo "user before update"."<br>";
                // var_dump($cursor_user);

                $oldBuylist = $cursor_user['buylist'];
                $buylist_size = sizeof($oldBuylist);
                $newBuylist = array_fill(0, $buylist_size, null);
                for($j=0; $j<$buylist_size; $j++){
                    $buy = $oldBuylist[$j];
                    if($candidateBuyer_id == $buyer_id && $buy['product_id'] == $pid)
                        $buy['status'] = "complete";
                    elseif($candidateBuyer_id !== $buyer_id && $buy['product_id'] == $pid)
                        $buy['status'] = "closed";
                    $newBuylist[$j] = $buy;
                }
                $newDocument_user = array(
                    "buylist"=> $newBuylist
                    );
                $updateUser_timeStart = microtime(true); 
                $ret = $collection_users->update(
	                array('_id'=>$candidateBuyer_id), // query: find the one which is going to be updated
	                array('$set'=>$newDocument_user) // new data
                    );
                $updateUser_timeEnd = microtime(true); 
                //echo "i=".$i.": "."updateUser_timeTaken:".($updateUser_timeEnd - $updateUser_timeStart)."<br>";
                $updateUser_timeTaken += ($updateUser_timeEnd - $updateUser_timeStart)*1000; 

                // $newQuery_user = array('_id'=>$candidateBuyer_id);
                // $newCursor_user = $collection_users->findOne($newQuery_user);
                // echo "user after update"."<br>";
                // var_dump($newCursor_user);

	            //step 5_2. recover the collection "users" to original state
                $recoverDocument_user = array(
                    "buylist"=> $oldBuylist
                    ); 
                $ret = $collection_users->update(
	              array('_id'=>$candidateBuyer_id), // query: find the one which is going to be updated
	              array('$set'=>$recoverDocument_user) // new data
	              );
                // $recoverQuery_user = array('_id'=>$candidateBuyer_id);
                // $recoverCursor_user = $collection_users->findOne($recoverQuery_user);
                // echo "user after recover"."<br>";
                // var_dump($recoverCursor_user);
            }//end foreach($buyerid_list as $candidateBuyer_id)
        }
        $time_taken = $updateProduct_timeTaken + $updateUser_timeTaken;
        echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
        $sum_time_taken += $time_taken;
    }

    $av_time_taken = $sum_time_taken/10;    
    echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>