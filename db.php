<?php
$host="localhost";
$username="root";
$password="";
$database="mira_artcraft";

$conn=new mysqli($host,$username,$password,$database);

if ($conn->connect_error){
    die("Database connection faild: ". $conn->connect_error);

}


?>