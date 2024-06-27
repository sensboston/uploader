<?php
require_once 'config.php';

function authenticate($username, $password) {
    $filename = 'users.txt';
    $response = array('authenticated' => false);

    if (!file_exists($filename)) {
        return $response;
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($fileUser, $filePass) = explode(':', $line, 2);
        if ($username === $fileUser && $password === $filePass) {
            $response['authenticated'] = true;
            break;
        }
    }

    return $response['authenticated'];
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];
$deleteFile = isset($data['delete']) ? basename($data['delete']) : null;

if (!authenticate($username, $password)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($deleteFile) {
    $filePath = UPLOAD_DIR . $deleteFile;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo json_encode(['success' => 'File deleted']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'File not found']);
    }
    exit;
}

$files = array_diff(scandir(UPLOAD_DIR), array('.', '..'));

$fileList = [];
foreach ($files as $file) {
    $filePath = UPLOAD_DIR . $file;
    $fileSizeBytes = filesize($filePath);
    $fileDate = date(DATE_TIME_FORMAT, filemtime($filePath));
    $uploadDate = date(DATE_TIME_FORMAT, filectime($filePath));

    if ($fileSizeBytes >= 1073741824) {
        $fileSizeFormatted = sprintf("%.1f GB (%s bytes)", $fileSizeBytes / 1073741824, number_format($fileSizeBytes));
    } elseif ($fileSizeBytes >= 1048576) {
        $fileSizeFormatted = sprintf("%.1f MB (%s bytes)", $fileSizeBytes / 1048576, number_format($fileSizeBytes));
    } elseif ($fileSizeBytes >= 1024) {
        $fileSizeFormatted = sprintf("%.1f KB (%s bytes)", $fileSizeBytes / 1024, number_format($fileSizeBytes));
    } else {
        $fileSizeFormatted = sprintf("%s bytes", number_format($fileSizeBytes));
    }

    $fileUrl = BASE_URL . rawurlencode($file);
    $fileList[] = [
        'name' => htmlspecialchars($file, ENT_QUOTES, 'UTF-8'),
        'size' => $fileSizeFormatted,
        'sizeBytes' => $fileSizeBytes,
        'modified' => $fileDate,
        'uploaded' => $uploadDate,
        'url' => htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8')
    ];
}

usort($fileList, function($a, $b) {
    return strtotime($b['uploaded']) - strtotime($a['uploaded']);
});

header('Content-Type: application/json');
echo json_encode($fileList);
?>
