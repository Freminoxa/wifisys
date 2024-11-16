<?php
$servername = "127.0.0.1";
$db_username = "francis";
$db_password = "1234";
$dbname = "wifisystem";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}