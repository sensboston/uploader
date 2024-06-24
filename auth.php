<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

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

$isAuthenticated = authenticate($username, $password);

if ($isAuthenticated) {
    $_SESSION['authenticated'] = true;
    echo json_encode(['authenticated' => true]);
} else {
    $_SESSION['authenticated'] = false;
    echo json_encode(['authenticated' => false]);
}
?>
