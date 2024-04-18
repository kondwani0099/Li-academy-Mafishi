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
$username = $_POST['username'];
$password = $_POST['password'];

// SQL to check if user exists
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start a session
    session_start();

    // Fetch user data
    $row = $result->fetch_assoc();

    // Store user data in session variables
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;

    // Redirect to new-licoder.php after successful login
    header("Location: new-licoder.php");
    exit(); // Ensure no further code execution after redirection
} else {
    echo "Invalid username or password";
}

$conn->close();
?>
