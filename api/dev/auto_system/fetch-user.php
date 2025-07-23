<?php
require_once '../config/connection.php';
///// check for API security
if ($apiKey != $expectedApiKey) {
    $response = [
        'response' => 401,
        'success' => false,
        'message' => 'SECURITY ACCESS DENIED! You are not allowed to execute this command due to a security breach.'
    ];
    goto end;
}

$userId = $_GET['userId'] ?? '';

if (empty($userId)) {
    $response = [
        'success' => false,
        'message' => 'No userId provided'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT userId, fullName, emailAddress, phoneNumber, passport FROM user_tab WHERE userId = '$userId'");

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'User not found'
    ];
    goto end;
}

$row = mysqli_fetch_assoc($query);

$response = [
    'success' => true,
    'data' => $row
];

end:
echo json_encode($response);
?>
