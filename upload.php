<?php
require_once 'config.php';
require_once 'auth.php';

// Function to convert size to bytes
function convertToBytes($size) {
    $number = substr($size, 0, -1);
    switch (strtoupper(substr($size, -1))) {
        case 'G':
            return $number * 1024 * 1024 * 1024;
        case 'M':
            return $number * 1024 * 1024;
        case 'K':
            return $number * 1024;
        default:
            return $size;
    }
}

// Function to get the total size of files in the directory
function getDirectorySize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    return $size;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fileDateTime = isset($_POST['fileDateTime']) ? (int)$_POST['fileDateTime'] / 1000 : time();

    // Validate the credentials using the Flask backend
    if (authenticate($username, $password)) {
        // Check if a file was uploaded
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $uploadFile = UPLOAD_DIR . basename($_FILES['file']['name']);
            $tmpFile = $_FILES['file']['tmp_name'];

	    // Get the total upload limit from config and convert to bytes
            $totalUploadLimit = convertToBytes(TOTAL_UPLOAD_SIZE);
            // Get the current size of the upload directory
            $currentDirSize = getDirectorySize(UPLOAD_DIR);
            // Get the size of the new file
            $fileSize = $_FILES['file']['size'];

	    // Check if adding the new file exceeds the total upload limit
            if (($currentDirSize + $fileSize) > $totalUploadLimit) {
                echo "Upload denied. Total upload limit exceeded.";
            } else {
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($tmpFile, $uploadFile)) {
                    // Preserve the original file modification time
                    touch($uploadFile, $fileDateTime);
                    echo "File is valid, and was successfully uploaded.";
                } else {
                    echo "File upload failed! ";
                    print_r(error_get_last());
                }
           }
        } else {
            echo "No file uploaded or file upload error!";
            echo "Error code: " . $_FILES['file']['error'];
        }
    } else {
        echo "Invalid username or password!";
    }
} else {
    echo "Invalid request method!";
}
