<?php 
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

/**
 * Upload file to S3 bucket
 */
function uploadToS3($s3, $bucket, $file) {
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = "result." . $fileExtension;

    try {
        $result = $s3->putObject([
            'Bucket'      => $bucket,
            'Key'         => $fileName,
            'SourceFile'  => $file['tmp_name'],
            'ContentType' => $file['type'],
        ]);
        return $result['ObjectURL'];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * Handle response and redirection
 */
function respond($message, $redirectUrl) {
    echo "<script>
        alert('$message');
        window.location.href = '$redirectUrl';
    </script>";
    exit;
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $bucketName = 'examresultsource';
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadToS3($s3, $bucketName, $file);

        if (is_array($uploadResult) && isset($uploadResult['error'])) {
            respond("Upload Failed: {$uploadResult['error']}", 'dashboard.php');
        } else {
            respond('File successfully uploaded!', 'dashboard.php');
        }
    } else {
        // Map error codes to messages
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the allowed size.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file is too large.',
            UPLOAD_ERR_PARTIAL    => 'File upload was incomplete.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary folder is missing.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the file upload.',
        ];

        $error = $errorMessages[$file['error']] ?? 'Unknown upload error.';
        respond("File Upload Error: $error", 'dashboard.php');
    }
} else {
    respond('No file uploaded. Please try again.', 'dashboard.php');
}
?>
