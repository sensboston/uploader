<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

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

$isAuthenticated = authenticate($username, $password);

if ($isAuthenticated) {
    $_SESSION['authenticated'] = true;
    echo json_encode(['authenticated' => true]);
} else {
    $_SESSION['authenticated'] = false;
    echo json_encode(['authenticated' => false]);
}
?>
