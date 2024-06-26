<?php
require_once 'config.php';

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

function getDirectorySize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    return $size;
}

$response = ['uploadAllowed' => false, 'message' => ''];
$fileSize = isset($_POST['fileSize']) ? (int)$_POST['fileSize'] : 1000000000000;

$totalUploadLimit = convertToBytes(TOTAL_UPLOAD_SIZE);
$currentDirSize = getDirectorySize(UPLOAD_DIR);
$newSize = $currentDirSize + $fileSize;

if ($newSize > $totalUploadLimit) {
    $response['message'] = 'Upload denied. Total upload limit exceeded.';
    $_SESSION['uploadAllowed'] = false;
} else {
    $response['message'] = 'Upload allowed. Starting upload...';
    $_SESSION['uploadAllowed'] = true;
}

echo json_encode($response);
?>
