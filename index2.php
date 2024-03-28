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

 <!-- JavaScript code for handling form submission, file upload, and displaying analysis results -->
 <script>
        $(document).ready(function() {
            $('#form').submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting normally

                var formData = new FormData($(this)[0]);

                $.ajax({
                    url: 'http://localhost:5000/upload',
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#loadingSpinner').hide(); // Hide loading spinner
                        var analysisResultContainer = $('#analysisResult');
                        analysisResultContainer.html(''); // Clear previous analysis results

                        var imagePath = $('#fileToUpload')[0].files[0].name;
                        var prompt = $('#prompt').val();

                        // Save data to database (JavaScript cannot directly interact with the database)
                        // This is a placeholder for the database interaction
                        $.ajax({
                            url: 'save_data.php',
                            type: 'POST',
                            data: {userId: <?php echo $userId; ?>, imagePath: imagePath, response: response, prompt: prompt},
                            success: function() {
                                // Database interaction successful
                            },
                            error: function() {
                                alert('Error saving data to the database.');
                            }
                        });

                        // Display analysis results
                        var responseText = JSON.parse(response).result;
                        var points = responseText.split('\n\n');
                        var analysisResultHTML = '<ul>';
                        points.forEach(function(point) {
                            analysisResultHTML += '<li>' + point + '</li>';
                        });
                        analysisResultHTML += '</ul>';
                        analysisResultContainer.html(analysisResultHTML);
                    },
                    beforeSend: function() {
                        $('#loadingSpinner').show(); // Show loading spinner
                    },
                    error: function() {
                        alert('Error uploading file.');
                        $('#loadingSpinner').hide(); // Hide loading spinner
                    }
                });
                return false; // Prevent the form from submitting normally
            });
        });
    </script>


        
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
