<?php
   // 1. create a database connection
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "123456";
$dbname = "exchangeplus_worcester";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

   //test if connected
if(mysqli_connect_errno()){
  die("Database connecction failed: " 
   . mysqli_connect_error() 
   . " (the error number is: " 
     . mysqli_connect_errno() 
     . ")"
  );
}


$selectCategory = array(
  "pcmcat209400050001",
  "abcat0501000", //desktop & all-in-one computer
  "abcat0401000", //camera
  "pcmcat242800050021", //health, fitness, beauty
  "abcat0204000",//headphones
  "pcmcat241600050001", //home audio
  "pcmcat254000050002", //home automation & security
  "pcmcat209000050006", // iPad, Tablets & E-Readers
  "abcat0502000",     // Laptops
  "pcmcat310200050004", // Portable & Wireless Speakers
  "abcat0904000",     // Ranges, Cooktops & Ovens
  "abcat0901000",     // Refrigerators
  "abcat0912000",     // Small Kitchen Appliances
  "abcat0101000",     // TVs
  "abcat0910000"      // Washer & Dryers
  );


srand(0);
for($index = 0; $index < sizeof($selectCategory); $index++){
  for ($pageNum = 1; $pageNum < 2; $pageNum++){
    $apiKey = "p7hmguu9tpy8tqrk6ycdan9r";
    $attributes = array(
      "name",
      "categoryPath.name",
      //"description",
      "regularPrice",
      "image"
      );
  // 2. read json file
    $url = "https://api.bestbuy.com/v1/products((categoryPath.id=".$selectCategory[$index]."))?apiKey="
    .$apiKey
    ."&sort=sku.asc&show="
    . implode(",", $attributes)
    ."&pageSize=100&page="
    . $pageNum
    . "&format=json";

    $page = file_get_contents($url);

  // 3. convert json string into PHP array
    $data = json_decode($page, true);

    $size = sizeof($data['products']);

    for($i = 0; $i < $size; $i++){

    // 4. extract the array values
    $pid = $index * ($pageNum * 100) + ($pageNum - 1) * 100 + $i + 1; //echo $pid;
    $name = $data['products'][$i]['name']; 
    $name = str_replace("'", "''", $name);
    $category = $data['products'][$i]['categoryPath'][1]['name']; 
    $category= str_replace("'", "''", $category);
    $price = $data['products'][$i]['regularPrice']; 
    $image = $data['products'][$i]['image']; 

    $sid = rand(1, 200);
    

    $deleteFlag = rand(1, 10);
    if($deleteFlag == 10)
      $status = "deleted";
    // elseif($cancel <= 3)
    //   $status = "sold";
    else
      $status = "forSale";
    //echo "deleteFlag: ".$deleteFlag."<br>";

    // 5 insert JSON to MySQL database
    $sql = "INSERT INTO products(pid, name, category, price, image, sid, status)
    VALUES('$pid', '$name', '$category', '$price', '$image', '$sid', '$status')";
    if(!mysqli_query($connection, $sql))
    {
      die('Error : ' . mysqli_error($connection));
    //die('Error : ' . mysqli_error($connection).'<br>'.$pid.' '. $data['products'][$i]['categoryPath'] );
    }
  }
}
}

 //step6. close connection
mysqli_close($connection);

?>