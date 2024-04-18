<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mafishi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$programme = $_POST['programme'];
$password = $_POST['password'];

// SQL to insert data into users table
$sql = "INSERT INTO users (first_name, last_name, username, email, phone, programme, password)
VALUES ('$first_name', '$last_name', '$username', '$email', '$phone', '$programme', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
