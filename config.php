<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mafishi2';

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// $conn = new mysqli('localhost', 'root', '', 'amatradestore');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
