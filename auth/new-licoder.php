<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: sign-in.php");
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Li-Coder AI</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
        }

        /* Navigation bar styles */
        .navbar {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 20px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }

        .navbar a:hover {
            background-color: #555;
        }

        /* Main content styles */
        .main-content {
            flex: 1;
            padding: 20px;
        }

        .conversation {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 20px;
            cursor: pointer;
        }

        .conversation img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .conversation h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .conversation p {
            margin: 0 0 10px 0;
        }

        /* Loading spinner styles */
        #loadingSpinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #loadingSpinner span {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #loadingMessage {
            color: #fff;
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navigation bar -->
    <div class="navbar">
        <h2>Conversations</h2>
        <?php
        // Fetch conversations for the logged-in user from the database
        include('config.php');
        $userId = $_SESSION['user_id'];
        $sql = "SELECT DISTINCT conversation_id FROM code_responses WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<a href='#' onclick='fetchConversation(\"" . $row['conversation_id'] . "\")'>Conversation " . $row['conversation_id'] . "</a>";
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1>Upload an Image and Analyze</h1>
        <form id="uploadForm" enctype="multipart/form-data">
            <div>
                <label for="fileToUpload">Select image to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
            </div>
            <div>
                <label for="prompt">Enter the instructions e.g. Solve All Questions:</label>
                <input type="text" name="prompt" id="prompt" placeholder="e.g. Solve all the Questions in the paper">
            </div>
            <button type="submit">Upload Image and Analyze</button>
        </form>

        <!-- Analysis Result Section -->
        <div id="analysisResult"></div>
    </div>
</div>

<!-- Loading spinner -->
<div id="loadingSpinner">
    <span></span>
    <span id="loadingMessage"></span>
</div>

<script>
    // Function to display loading spinner
    function showLoadingSpinner() {
        document.getElementById('loadingSpinner').style.display = 'flex';
    }

    // Function to hide loading spinner
    function hideLoadingSpinner() {
        document.getElementById('loadingSpinner').style.display = 'none';
    }

    // Function to fetch and display conversation details
    function fetchConversation(conversationId) {
        showLoadingSpinner(); // Display loading spinner

        // Make AJAX request to fetch conversation details
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_conversation.php?conversation_id=' + conversationId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                hideLoadingSpinner(); // Hide loading spinner
                if (xhr.status === 200) {
                    // Handle successful response
                    document.getElementById('analysisResult').innerHTML = xhr.responseText;
                } else {
                    // Handle error
                    console.error('Error:', xhr.status);
                }
            }
        };
        xhr.send();
    }

    // Function to handle form submission
    document.getElementById('uploadForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission
        showLoadingSpinner(); // Display loading spinner

        // Create FormData object to send form data asynchronously
        var formData = new FormData(this);

        // Make AJAX request to handle form submission
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'process_upload.php', true); // Specify the PHP file to handle form submission
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                hideLoadingSpinner(); // Hide loading spinner
                if (xhr.status === 200) {
                    // Handle successful response
                    var response = xhr.responseText;
                    document.getElementById('analysisResult').innerHTML = response;
                } else {
                    // Handle error
                    console.error('Error:', xhr.status);
                }
            }
        };
        xhr.send(formData);
    });
</script>

</body>
</html>
