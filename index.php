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
$sql = "SELECT image_path, response ,prompt FROM image_responses WHERE user_id = ?";
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
    <title>Smart Image Analyzer</title>
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
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
</head>
<body class="bg-gray-100">
   <!-- Navigation Bar -->
   <nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="#" class="text-white text-xl font-bold"><img style='height: 60px;'src='./img/li.png'></a>

        <!-- Hamburger Icon for Mobile -->
        <button id="menu-toggle" class="block lg:hidden text-white focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Navigation Links -->
        <div class="hidden lg:flex lg:items-center lg:space-x-6">
            <a href="https://lapansiindustries.com/liacademy/index.html" class="text-white hover:text-gray-300">Home</a>
            <a href="https://lapansiindustries.com/liacademy/index.html" class="text-white hover:text-gray-300">About</a>
            <a href="https://www.lapansiindustries.com/Library/LibraryCBU.html" class="text-white hover:text-gray-300">Library</a>
            <a href="https://lapansiindustries.com/liacademy/cbugpt.php" class="text-white hover:text-gray-300">CBUGPT</a>
            <a href="tel:+2603229809" class="text-white hover:text-gray-300">Contact</a>
            <a href="./auth/signout.php" class="text-white hover:text-gray-300">LogOut</a>
        </div>
    </div>
</nav>

<!-- Drawer (Mobile Menu) -->
<div id="mobile-menu" class="lg:hidden fixed inset-0 bg-gray-800 bg-opacity-75 z-50 hidden">
    <div class="flex justify-end p-4">
        <button id="close-menu" class="text-white focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div class="flex flex-col items-center justify-center h-full">
        <a href="https://lapansiindustries.com/liacademy/index.html" class="text-white text-2xl font-bold mb-6">Li-Academy</a>
        <a href="https://lapansiindustries.com/liacademy/index.html" class="text-white hover:text-gray-300">Home</a>
        <a href="https://lapansiindustries.com/liacademy/index.html" class="text-white hover:text-gray-300">About</a>
        <a href="https://www.lapansiindustries.com/Library/LibraryCBU.html" class="text-white hover:text-gray-300">Library</a>
        <a href="https://lapansiindustries.com/liacademy/cbugpt.php" class="text-white hover:text-gray-300">CBUGPT</a>
        <a href="tel:+2603229809" class="text-white hover:text-gray-300">Contact</a>
        <a href="./auth/signout.php" class="text-white hover:text-gray-300">LogOut</a>
    </div>
</div>
<!-- JavaScript to toggle the drawer -->
<script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
    document.getElementById('close-menu').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.add('hidden');
    });
</script>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Handle image upload and analysis
if (isset($_POST["submit"])) {
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
    if ($_FILES["fileToUpload"]["size"] > 500000) {
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

            // echo "<script>$('.loading').show();</script>"; // Show loading spinner
            // Show loading spinner
            // echo "<script>document.getElementById('loadingSpinner').style.display = 'block';</script>";
             // Show loading spinner before making the request
             
// Rest of your PHP code...

// Show loading spinner before making the request
echo "<script>";
echo "if (document.getElementById('loadingSpinner')) {";
echo "  document.getElementById('loadingSpinner').style.display = 'flex';";
echo "}";
echo "console.log('spinner');"; // Log message to console for debugging
echo "</script>";

// Rest of your PHP code...


             echo "<script>document.getElementById('loadingSpinner').style.display = 'flex'; console.log('spinner')</script>";

             // Send data to Flask API

            // Send data to Flask API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5000/upload');
            // curl_setopt($ch, CURLOPT_URL, 'https://mafishi-latest.onrender.com/upload');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec($ch);
            // curl_close($ch);

        //    // Hide loading spinner after response received
        //     echo "<script>document.getElementById('loadingSpinner').style.display = 'none';</script>";

            // Show loading spinner before making the request
            // echo "<script>$('.loading').show();</script>";

            $response = curl_exec($ch);
            curl_close($ch);

            
            echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
              var loadingElements = document.querySelectorAll('.loading');
              loadingElements.forEach(function(element) {
              element.style.display = 'none';
              });
                });
            </script>";



            // echo "<script>$('.loading').hide();</script>"; // Hide loading spinner after response received

            // Save the response and image path to the database
            $imagePath = basename($_FILES["fileToUpload"]["name"]);
            $prompt = $_POST["prompt"];
            $sql = "INSERT INTO image_responses (user_id, image_path, response, prompt) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $userId, $imagePath, $response, $prompt);
            $stmt->execute();
            $stmt->close();

            // Decode JSON response and remove curly braces
            $response = json_decode($response, true)['result'];
            echo "<br><br>";
                echo "<div class='container mx-auto px-4 py-8'>";
                echo "<img src='uploads/" . basename($_FILES["fileToUpload"]["name"]) . "' class='max-w-full h-auto rounded-md shadow-md'>";
                echo "<p class='mt-4'>Analysis Result:</p>";
                echo "<div id='analysisResult' class='bg-white rounded-md p-4 mt-2'>";
                     
                     // Convert response into an array
                $points = explode("\n", $response);
                echo "<ul class='list-disc pl-5'>";
                foreach ($points as $point) {
                         echo "<li class='text-base'>$point</li>";
                     }
                echo "</ul>";
 
                echo "</div>";
                echo "</div>"; // Close the container
                

                // Display analysis result
                echo "<script>document.getElementById('analysisResult').innerHTML = '$response';</script>";
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
            
        }
    }
}
?>

        
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-4">Upload an Image and Analyze</h2>
        <form action="" method="post" enctype="multipart/form-data" class="mb-8" id="form">
            <div class="mb-4">
                <label for="fileToUpload" class="block text-sm font-medium text-gray-700">Select image to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700">Enter a prompt:</label>
                <input type="text" name="prompt" id="prompt" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md  p-4">
            </div>
            <button type="submit" id="submitButton" name="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload Image and Analyze</button>

        </form>

        
        <!-- Analysis Result Section -->
        <div id="analysisResult" class="text-lg"></div>

        <div class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
<?php
// Display retrieved images and data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_path'];
        $response = $row['response'];
        $userprompt = $row['prompt'];

        // Decode JSON response and remove curly braces and 'result' key
        $responseArray = json_decode($response, true);
        // Check if JSON decoding was successful and 'result' key exists
        if ($responseArray && isset($responseArray['result'])) {
            $responseText = $responseArray['result'];

            // Convert response into an array of points
            $points = explode("\n\n", $responseText);
            // Output the points or use them as needed
           
            }
        // $responseText = $responseArray['result'];

        // // Convert response into an array of points
        // $points = explode("\n\n", $responseText);
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
                <ul class='list-disc pl-5'>
                    <?php foreach ($points as $point) : ?>
                        <li class='text-base'><?php echo $point ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
              <!-- Delete button -->
    <form action="delete.php" method="post" class="mt-4">
        <input type="hidden" name="image_id" value="<?php echo $imageId ?>">
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md">Delete</button>
    </form>
        </div>
<?php
    }
} else {
    // If no images found
    echo "<div class='text-gray-600'>No images found for the logged-in user.</div>";
}
?>

</div>

</div>
<!-- Add the loading spinner directly in your HTML file -->
<div id="loadingSpinner" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-200 bg-opacity-50 z-50" style="display: none;">
    <span class="loading loading-spinner loading-lg"></span>
</div>

    <script src="./spinner.js"></script>
</body>
</html>
