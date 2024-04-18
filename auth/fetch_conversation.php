<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: sign-in.php");
    exit(); // Stop further execution
}

// Check if the conversation ID is provided in the request
if (!isset($_GET['conversation_id'])) {
    exit("Conversation ID not provided");
}

// Include database configuration
include('config.php');

// Fetch conversation details from the database based on the conversation ID
$userId = $_SESSION['user_id'];
$conversationId = $_GET['conversation_id'];

$sql = "SELECT * FROM code_responses WHERE user_id = ? AND conversation_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userId, $conversationId);
$stmt->execute();
$result = $stmt->get_result();

// Display fetched data
while ($row = $result->fetch_assoc()) {
    echo "<div class='conversation'>";
    echo "<img src='uploads/" . $row['image_path'] . "' alt='Uploaded Image'>";
    echo "<h1>Analysis Result:</h1>";
    echo "<div>";
    echo "<pre>";
    echo "<p>Prompt: " . htmlspecialchars($row['prompt']) . "</p>";
    echo "<p>Response: " . htmlspecialchars($row['response']) . "</p>";
    echo "</pre>";
    echo "</div>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>
