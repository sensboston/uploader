<?php
require_once 'config.php';
require_once 'auth.php';

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

            // Move the uploaded file to the specified directory
            if (move_uploaded_file($tmpFile, $uploadFile)) {
                // Preserve the original file modification time
                touch($uploadFile, $fileDateTime);
                echo "File is valid, and was successfully uploaded.\n";
            } else {
                echo "File upload failed! ";
                print_r(error_get_last());
            }
        } else {
            echo "No file uploaded or file upload error!\n";
            echo "Error code: " . $_FILES['file']['error'];
        }
    } else {
        echo "Invalid username or password!\n";
    }
} else {
    echo "Invalid request method!\n";
}
