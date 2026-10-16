<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "practical10";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database Connection Error: " . mysqli_connect_error());
}
?>