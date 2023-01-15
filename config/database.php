<?php
// used to connect to the database
$host = "";
$db_name = "ecomplaint";
$username1 = "ecomplaint";
$password1 = "kaixian";

date_default_timezone_set("Asia/Kuala_Lumpur");
  
try {
    $con = new PDO("mysql:host={$host};dbname={$db_name}", $username1, $password1); //PDO make connection to database
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // show error
}  
// show error
catch(PDOException $exception){
    echo "Connection error: ".$exception->getMessage();
}
?>
