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
//$collection_products = $db->products;


$empty_buylist = array();
var_dump($empty_buylist);


?>