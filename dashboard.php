<?php
session_start();

// Check if logout action is triggered
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: index.php"); // Redirect to login page
    exit();
}

// Include AWS S3 initialization and other necessary code if you have it here.
require 'vendor/autoload.php';

use Aws\S3\S3Client;

// Initialize the S3 client
$s3 = new S3Client([
    'region'  => 'ap-south-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => "AKIAYKFQQYO2JFEIMJ4G",
        'secret' => "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",
    ]
]);

$uploadSuccess = false; // Flag for tracking upload status

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file_name = $_FILES['file']['name'];
    $temp_file_location = $_FILES['file']['tmp_name'];
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_name = "demo." . $file_extension;

    try {
        // Upload file to S3
        $result = $s3->putObject([
            'Bucket' => 'examresultsource',
            'Key'    => $file_name,
            'SourceFile' => $temp_file_location,
            'ContentType' => $_FILES['file']['type']
        ]);

        $uploadSuccess = true; // Set the success flag
    } catch (Exception $e) {
        echo "Error uploading file: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAT E-governance Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and Font Settings */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4fa; /* Light Blue Background */
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Container */
        .container {
            width: 85%;
            margin: 0 auto;
            padding: 30px 0;
        }

        /* Header */
        .nav-link {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: 500;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        .header {
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); /* Gradient for the Header */
            color: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .logout-btn {
            background-color: #fc5c7d;
            color: white;
            border: none;
            padding: 10px 25px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
            box-shadow: 0 8px 16px rgba(252, 92, 125, 0.2);
        }

        .logout-btn:hover {
            background-color: #ff1744; /* Darker hover color */
        }

        /* Main Content */
        .main-content {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        /* Section Card */
        .section-card {
            background: #ffffff; /* Card Background */
            border-radius: 15px;
            padding: 30px;
            flex: 1 1 300px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }

        .section-card h2 {
            color: #6a82fb; /* Header Color */
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 700;
        }

        .section-card h3 {
            margin-bottom: 20px;
            font-size: 20px;
            color: #fc5c7d; /* Secondary Header Color */
        }

        .section-card p {
            font-size: 15px;
            color: #666;
            margin-bottom: 15px;
        }

        .view-results-btn {
            display: inline-block;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); /* Gradient Button */
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .view-results-btn:hover {
            background: linear-gradient(135deg, #fc5c7d, #6a82fb); /* Hover Effect */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }


        /* File Upload Form */
        form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        form input[type="file"] {
            font-size: 16px;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 5px;
        }

        form input[type="submit"] {
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); /* Gradient Button */
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        form input[type="submit"]:hover {
            background: linear-gradient(135deg, #fc5c7d, #6a82fb); /* Hover Effect */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header h1 {
                margin-bottom: 15px;
            }

            .main-content {
                flex-direction: column;
            }

            .section-card {
                flex: 1 1 auto;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="header">
            <h1>TAT E-governance Dashboard</h1>
            <nav>
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="result.php" class="nav-link">View Results</a>
                <a class="logout-btn" href="dashboard.php?action=logout">Logout</a> <!-- Logout link triggers logout action -->
            </nav>
        </header>        

        <!-- Main Content -->
    <main class="main-content">
    <!-- Section 1: BPUT Result Upload -->
    <section class="section-card">
        <h2>BPUT Result Uplink (.CSV File)</h2>
        <p>Upload the result CSV file for processing. Make sure the file follows the specified format and contains accurate data to avoid errors during the upload process.</p>
    </section>

    <!-- Section 2: Upload Form -->
    <section class="section-card">
        <h3>Choose a CSV File to Upload</h3>
        <form action="uploadnext.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" id="file" required>
            <input type="submit" value="Upload File">
        </form>
    </section>

    <!-- Section 3: View Results Link -->
    <section class="section-card">
        <h2>View Student Results</h2>
        <p>Click the button below to view the student results table.</p>
        <a href="result.php" class="view-results-btn">View Results</a>
    </section>
    </main>
    </div>

   <!-- JavaScript for Popup Alert -->
    <script>
        // Check if the upload was successful and show an alert
        <?php if ($uploadSuccess): ?>
            alert("File successfully uploaded to S3!");
        <?php endif; ?>
    </script>

</body>
</html>
