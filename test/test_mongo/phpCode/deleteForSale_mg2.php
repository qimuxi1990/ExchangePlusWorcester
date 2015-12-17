<?php
// test case: seller delete a product which is "forSale" (cannot delete a product which has been sold)
// (Before delete, seller already have an array of sell product id)
// update products.product_status to 'deleted' 
// update products.buyerlist[].status to be 'closed'
// update the all buyers's buylist[].status to 'closed'
// input: the seller whose sid = 86, want to delete the product where product_id = 8

//step 1. connect to database
$dbhost = 'localhost';
$dbname = 'exchangeplus_worcester_mongo';
$m = new MongoClient();
$db = $m->$dbname;
$collection_products = $db->products;
$collection_users = $db->users;

//step 2. set float precision
ini_set('precision', 20);

//step 3 initialize: 
//step 3-1. initialize the pid which need to be deleted
$pid = 8;

//step 3-2: initialize #tires
//$triesArr = array(10, 100, 200, 500, 1000, 2000);//, 4000, 6000, 8000, 10000);
$triesArr = array(1);
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
        //step 4-1. update products.product_status to 'deleted' 
        //          update products.buyerlist[].status to be 'closed', 
        $query_product = array('_id'=>$pid);
        $cursor_product = $collection_products->findOne($query_product);
        // echo "products before update: "."<br>";
        // var_dump($cursor_product);
        $oldBuyerlist = $cursor_product['buyerlist'];
        $buyerlist_size = sizeof($oldBuyerlist);

        $newBuyerlist = array_fill(0, $buyerlist_size, null);
        $buyerid_list = array_fill(0, $buyerlist_size, null); //used to query_product users
        for($j=0; $j<$buyerlist_size; $j++){
          $buyer = $oldBuyerlist[$j];
          $buyer['status'] = "closed";
          $newBuyerlist[$j] = $buyer;
          $buyerid_list[$j] = $buyer['buyer_id'];
        }
        $newDocument_product = array(
         "product_status"=> "deleted",
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
        // echo "products after update"."<br>";
        // var_dump($newCursor_product);
        

        //echo "-------------------------------Update User----------------------------"."<br>";

        //step 5. update users
        //step 5-1. update the all buyers's buylist[].status to 'closed'
        foreach($buyerid_list as $buyer_ID){
          $query_user = array('_id'=>$buyer_ID);
          $cursor_user = $collection_users->findOne($query_user);
          // echo "user before update"."<br>";
          // var_dump($cursor_user);

          $oldBuylist = $cursor_user['buylist'];
          $buylist_size = sizeof($oldBuylist);
          $newBuylist = array_fill(0, $buylist_size, null);
          for($j=0; $j<$buylist_size; $j++){
            $buy = $oldBuylist[$j];
            if($buy['product_id'] == $pid)
              $buy['status'] = "closed";
            $newBuylist[$j] = $buy;
          }
          $newDocument_user = array(
            "buylist"=> $newBuylist
            );
          $updateUser_timeStart = microtime(true); 
          $ret = $collection_users->update(
	          array('_id'=>$buyer_ID), // query: find the one which is going to be updated
	          array('$set'=>$newDocument_user) // new data
           );
          $updateUser_timeEnd = microtime(true); 
          //echo "i=".$i.": "."updateUser_timeTaken:".($updateUser_timeEnd - $updateUser_timeStart)."<br>";
          $updateUser_timeTaken += ($updateUser_timeEnd - $updateUser_timeStart)*1000; 

          // $newQuery_user = array('_id'=>$buyer_ID);
          // $newCursor_user = $collection_users->findOne($newQuery_user);
          // echo "user after update"."<br>";
          // var_dump($newCursor_user);

      }//end foreach($buyerid_list as $buyer_ID){
    }// end for($i=0; $i<$tries; $i++)
    $time_taken = $updateProduct_timeTaken + $updateUser_timeTaken;

    echo "the ".$s."th "."time".": "."time taken: ".$time_taken." ns"."<br>";
    $sum_time_taken += $time_taken;

    //step 6. recover database
    $collection_products->drop();
    $collection_users->drop();
    include 'mongoGetProducts.php';
    include 'mongoGetUsers.php';
  }

  $av_time_taken = $sum_time_taken/10;    
  echo "the average time taken: ".$av_time_taken." ns"."<br>";
}
?>