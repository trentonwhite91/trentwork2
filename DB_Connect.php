<?php
$servername = "localhost";
$username = "trentw";
$password = "whitet";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//} 
//echo "Connected successfully";

//SQL queries 
   $query1 = "SELECT Invoice_Amt, Invoice_Date FROM PROJECT1_DATABASE.Invoices;";
   
   $query1_r = mysqli_query($conn, $query1);
   
//check for successful query
   
 //  if ( !$query1_r ) {
 //  echo mysql_error();
 //  	die;
 //  } 
 //  echo "Query successful!";
   //
   $data = array();
   
   for ($x = 0; $x < mysqli_num_rows ($query1_r); $x++) {
   
   	$data[] = mysqli_fetch_assoc($query1_r);
   	
   }
   
   echo json_encode($data);

?>