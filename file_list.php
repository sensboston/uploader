<?php
require_once 'config.php';

function authenticate($username, $password) {
    $url = 'http://localhost:7000/auth';
    $data = json_encode(array("username" => $username, "password" => $password));
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
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
    $fileSizeFormatted = ($fileSizeBytes >= 1048576) ? sprintf("%.1f MB (%s bytes)", $fileSizeBytes / 1048576, number_format($fileSizeBytes)) : sprintf("%s bytes", number_format($fileSizeBytes));
    $fileUrl = BASE_URL . urlencode($file);
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
