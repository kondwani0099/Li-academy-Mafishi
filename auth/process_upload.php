<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page or handle as per your authentication mechanism
    exit("User is not logged in");
}

// Set session timeout to 15 minutes
$inactive = 900; // 15 minutes in seconds

// Check if last activity timestamp is set
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    // Last activity occurred more than 15 minutes ago, destroy session
    session_unset();
    session_destroy();
    exit("Session expired due to inactivity");
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Generate or retrieve conversation ID from session
if (!isset($_SESSION['conversation_id'])) {
    // Generate a new conversation ID
    $_SESSION['conversation_id'] = uniqid();
}

// Check if form data is received
if (!isset($_FILES["fileToUpload"]) || !isset($_POST["prompt"])) {
    exit("Form data not received");
}

// Your database connection and configuration
include('config.php');

// Get user ID from session
$userId = $_SESSION['user_id'];

// Retrieve the conversation ID from session
$conversationId = $_SESSION['conversation_id'];

// Your existing code for uploading image and sending data to Flask API
// Adjust this part as per your requirement

// Prepare data to send to Flask API
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
if ($check === false) {
    exit("File is not an image.");
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 9000000) {
    exit("Sorry, your file is too large.");
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    exit("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    exit("Sorry, your file was not uploaded.");
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Prepare data to send to Flask API
        $data = array(
            'fileToUpload' => new CURLFile($target_file), // Correctly create CURLFile object
            'prompt' => $_POST['prompt']
        );

        // Send data to Flask API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://li-coder.onrender.com/upload');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
        curl_close($ch);

        // Check if API communication was successful
        if ($http_code == 200) {
            // Save the response and image path to the database
            $imagePath = basename($_FILES["fileToUpload"]["name"]);
            $prompt = $_POST["prompt"];
            $sql_insert = "INSERT INTO code_responses (user_id, conversation_id, image_path, response, prompt) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("issss", $userId, $conversationId, $imagePath, $response, $prompt);
            $stmt_insert->execute();
            $stmt_insert->close();  // Close the previous statement properly

            // Decode JSON response and remove curly braces
            $response = json_decode($response, true)['result'];

            // Display analysis result
            echo "<br><br>";
            echo "<div class='conversation'>";
            echo "<img src='uploads/" . basename($_FILES["fileToUpload"]["name"]) . "' alt='Uploaded Image'>";
            echo "<h1>Analysis Result:</h1>";
            echo "<div>";
            echo "<pre>"; // Start preformatted text section with code editor style

            // Convert response into an array
            $points = explode("\n", $response);
            foreach ($points as $point) {
                // Escape HTML entities in the code snippet
                $escaped_point = htmlspecialchars($point);
                echo "<p>$escaped_point</p>"; // Code text color as white
            }
            echo "</pre>"; // End preformatted text section
            echo "</div>";
            echo "</div>"; // Close the conversation container

            // Display analysis result
            echo "<script>document.getElementById('analysisResult').innerHTML = '$response';</script>";
        } else {
            // Failed to communicate with API
            echo "<div class='conversation'>";
            echo "<h1>Bad Network</h1>";
            echo "<p>The page you're looking for used information that you entered. Returning to this page might trigger a repetition of any action you took there. Please check your network connection and try again later.</p>";
            echo "</div>";
        }
    } else {
        // File upload error
        echo "<div class='conversation'>";
        echo "<h1>Upload Failed</h1>";
        echo "<p>Failed to upload file. Please try again.</p>";
        echo "</div>";
    }
}
?>
