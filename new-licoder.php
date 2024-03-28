<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: ./auth/sign-in.php");
    exit(); // Stop further execution
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Include config file
include('config.php');

// Select images and data associated with the logged-in user
$sql = "SELECT image_path, response ,prompt FROM code_responses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Li-Coder AI</title>
    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon">
    <link href="img/favicon.png" rel="apple-touch-icon">
    <meta name="author" content="Kondwani Nyirenda">
    <meta property="og:url" content="https://www.lapansiindustries.com/mafishi-ai/index.php">
    <meta property="og:image" content="https:/www.lapansiindustries.com/mafishi-ai/img/mafishi.png" /> 
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="800">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Li-Academy" />
    <meta property="og:description" content="Li-Academy leverages cutting-edge technology MAFISHI-AI ,CBUGPT to provide innovative educational solutions tailored 
    for the Copperbelt University (CBU) community. ">
    <meta property="og:url" content="https://www.lapansiindustries.com/mafishi-ai/index.php ">

    <meta content="Li-Academy leverages cutting-edge technology ,Mafishi GPT and CBUGPT to provide innovative educational solutions tailored 
     for the Copperbelt University (CBU) community. From personalized chatbots offering curated notes and real-time data summaries
     to answering academic queries and facilitating test preparation, we empower CBU students with the tools for academic
     success. Mafishi GPT, a specialized model, further enhances learning by providing images and analyzing circuits, mechanical design,
      civil engineering, medicine, and other fields of study." name="description">

     <meta content="cbu gpt ,li-academy" name="keywords">
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
     <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RT67Y28Q1H"></script>
    
        <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    
  <!-- Favicons -->
  <link href="img/favicon.png" rel="icon">
  <link href="img/favicon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
      <!-- Libraries Stylesheet -->
    <link href="whatsapp.css" rel="stylesheet">
    <style>
    /* Style for code editor */
    .code-editor {
        background-color: black; /* Black background */
        color: white; /* Text color as white */
        padding: 10px; /* Padding for better readability */
        font-family: 'Courier New', Courier, monospace; /* Monospace font for code */
        border-radius: 5px; /* Rounded corners */
        overflow-x: auto; /* Allow horizontal scrolling */
    }
    
    /* Additional CSS for rounded inputs and button */
    input[type="file"] {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        outline: none;
        cursor: pointer;
        background: #edf2f7;
    }

    input[type="file"]::-webkit-file-upload-button {
        visibility: hidden;
    }

    input[type="file"]::before {
        content: 'Choose File';
        display: inline-block;
        background: linear-gradient(to bottom, #f9fafb, #edf2f7);
        border: 1px solid #cbd5e0;
        padding: 8px 20px;
        outline: none;
        white-space: nowrap;
        -webkit-user-select: none;
        cursor: pointer;
        text-align: center;
        font-size: 14px;
        line-height: 1.5;
        border-radius: 9999px;
    }

    input[type="file"]:hover::before {
        background: linear-gradient(to bottom, #edf2f7, #e2e8f0);
    }

    input[type="file"]:active::before {
        background: #cbd5e0;
    }
    hr{
        background-color: #fff;
    }

</style>

    
    <script>
     window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-RT67Y28Q1H');
    </script>
</head>
<body class="bg-gray-100">
<?php include('./static/navbar.php') ?>

<?php
// Variable to check if API communication failed
// $api_failed = false;
// Include config file
include('config.php');

// Select images and data associated with the logged-in user
$sql_select = "SELECT image_path, response, prompt FROM code_responses WHERE user_id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $userId);
$stmt_select->execute();
$result = $stmt_select->store_result();

// Variable to check if API communication failed
$api_failed = false;

// Handle image upload and analysis
if (isset($_POST["submit"])) {
    // Your existing code for uploading image and sending data to Flask API
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.')</script>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 9000000) {
        echo "<script>alert('Sorry, your file is too large.')</script>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.')</script>";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "<script>alert('The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.')</script>";

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
                $sql_insert = "INSERT INTO code_responses (user_id, image_path, response, prompt) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("isss", $userId, $imagePath, $response, $prompt);
                $stmt_insert->execute();
                $stmt_insert->close();  // Close the previous statement properly
                  // Decode JSON response and remove curly braces
                $response = json_decode($response, true)['result'];

                // Display analysis result
                echo "<br><br>";
                echo "<div class='container mx-auto px-4 py-8'>";
                echo "<img src='uploads/" . basename($_FILES["fileToUpload"]["name"]) . "' class='max-w-full h-auto rounded-md shadow-md'>";
                echo "<p class='mt-4'>Analysis Result:</p>";
                echo "<div id='analysisResult' class='bg-black rounded-md p-4 mt-2'>"; // Black background
echo "<pre class='code-editor'>"; // Start preformatted text section with code editor style

// Convert response into an array
$points = explode("\n", $response);
echo "<ul class='list-disc pl-5'>";
foreach ($points as $point) {
    // Escape HTML entities in the code snippet
    $escaped_point = htmlspecialchars($point);
    echo "<li class='text-white'>$escaped_point</li>"; // Code text color as white
}
echo "</ul>";
echo "</pre>"; // End preformatted text section
echo "</div>";
echo "</div>"; // Close the container

                // echo "<div id='analysisResult' class='bg-black rounded-md p-4 mt-2'>"; // Black background
                // echo "<pre class='code-editor'>"; // Start preformatted text section with code editor style
                
                // // Convert response into an array
                // $points = explode("\n", $response);
                // echo "<ul class='list-disc pl-5'>";
                // foreach ($points as $point) {
                //     echo "<li class='text-white'>$point</li>"; // Code text color as white
                // }
                // echo "</ul>";
                // echo "</pre>"; // End preformatted text section
                // echo "</div>";
                // echo "</div>"; // Close the container
                
                

                // Display analysis result
                echo "<script>document.getElementById('analysisResult').innerHTML = '$response';</script>";
            } else {
                // Failed to communicate with API
                 // API communication failed
                $api_failed = true;
                // echo "<div class='container mx-auto text-center mt-8'>";
                // echo "<h2 class='text-2xl font-semibold text-red-600'>Analysis Failed</h2>";
                // echo "<p class='text-lg text-gray-700'>Failed to communicate with the API. Please check your network connection and try again later.</p>";
                // echo "</div>";
            }
        } else {
            // File upload error
            echo "<div class='container mx-auto text-center mt-8'>";
            echo "<h2 class='text-2xl font-semibold text-red-600'>Upload Failed</h2>";
            echo "<p class='text-lg text-gray-700'>Failed to upload file. Please try again.</p>";
            echo "</div>";
        }
    }
}
// If API communication failed, display "Bad Network" message
if ($api_failed) {
    echo "<div class='container mx-auto text-center mt-8'>";
    echo "<h2 class='text-2xl font-semibold text-red-600'>Bad Network</h2>";
    echo "<p class='text-lg text-gray-700'>The page you're looking for used information that you entered. Returning to this page might trigger a repetition of any action you took there. Please check your network connection and try again later.</p>";
    echo "</div>";
}
?>

       <br> 
       <br>
       <br>
       <br>
    <div class="container mx-auto px-4 py-8 mt-100">
        <h2 class="text-2xl font-semibold mb-4">Upload an Image and Analyze</h2>
        <hr class='bg-white'>
        <br>
        <form action="" method="post" enctype="multipart/form-data" class="mb-8" id="form">
    <div class="mb-4">
        <label for="fileToUpload" class="block text-sm font-medium text-gray-700">Select image to upload:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-3">
    </div>
    <div class="mb-4">
        <label for="prompt" class="block text-sm font-medium text-gray-700">Enter the instructions e.g. Solve All Questions:</label>
        <input type="text" name="prompt" id="prompt" placeholder="e.g. Solve all the Questions in the paper" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-3">
    </div>
    <button type="submit" id="submitButton" name="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload Image and Analyze</button>
</form>
        <!-- <form action="" method="post" enctype="multipart/form-data" class="mb-8" id="form" >
            <div class="mb-4">
                <label for="fileToUpload" class="block text-sm font-medium text-gray-700">Select image to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700">Enter the instructions e.g Solve All Questions:</label>
                <input type="text" name="prompt" id="prompt" placeholder='e.g Solve all the Qestions in the paper' class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md  p-4">
            </div>
            <button type="submit" id="submitButton" name="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload Image and Analyze</button>

        </form> -->

        
        <!-- Analysis Result Section -->
        <div id="analysisResult" class="text-lg"></div>

        <div class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-2">
<?php
// Include config file
include('config.php');

// Select images and data associated with the logged-in user
// $sql = "SELECT id, image_path, response, prompt FROM image_responses WHERE user_id = ?";
// Select images and data associated with the logged-in user, ordered by ID in descending order
$sql = "SELECT id, image_path, response, prompt 
        FROM code_responses 
        WHERE user_id = ? 
        ORDER BY id DESC 
        ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($imageId, $imagePath, $response, $userprompt);

// Display retrieved images and data
if ($stmt->num_rows > 0) {
    while ($stmt->fetch()) {
        // Decode JSON response and remove curly braces and 'result' key
        $responseArray = json_decode($response, true);
        // Check if JSON decoding was successful and 'result' key exists
        if ($responseArray && isset($responseArray['result'])) {
            $responseText = $responseArray['result'];

            // Convert response into an array of points
            $points = explode("\n\n", $responseText);
            // Output the points or use them as needed
?>
            <div class="border border-gray-200 rounded-md shadow-md p-4">
                <!-- Image -->
                <img src='uploads/<?php echo $imagePath ?>' class='max-w-full h-auto rounded-md'>
                <!-- Response -->
                <div class='bg-white rounded-md p-4 mt-2'>
                    <h1 class="text-xl font-semibold mt-4">User Input:</h1>
                    <?php echo $userprompt ?>
                </div>
                <div class='bg-white rounded-md p-4 mt-2'>
                <div class='bg-black rounded-md p-4 mt-2'>
                <pre class='code-editor'>


                    <ul class='list-disc pl-5'>
                        <?php foreach ($points as $point) : ?>
                            <li class='text-base'><?php echo  htmlspecialchars($point) ?></li>
                        <?php endforeach; ?>
                    </ul>
                        </pre>
                </div>
                </div>
                <form action="delete.php" method="post" class="mt-4">
                    <input type="hidden" name="image_id" value="<?php echo $imageId ?>">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md">Delete</button>
                </form>
            </div>
<?php
        }
    }
} else {
    // If no images found
    echo "<div class='text-gray-600'>No images found for the logged-in user.</div>";
}
?>


</div>

</div>
      <!-- WhatsApp button -->
    <div id="whatsappButton" class="whatsapp-button">
        <a href="https://wa.me/+260960322980" target="_blank" title="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    
    <!-- Add the loading spinner directly in your HTML file -->
<div id="loadingSpinner" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-200 bg-opacity-50 z-50" style="display: none;">
<span class="loading loading-spinner loading-lg"></span>
</div>
<script>
        // Show loading spinner when form is submitted
        document.getElementById('form').addEventListener('submit', function () {
            document.getElementById('loadingSpinner').style.display = 'flex';
        });

        // Hide loading spinner when response is received
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('loadingSpinner').style.display = 'none';
        });
    </script>
</body>
</html>
