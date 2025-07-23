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

$query = mysqli_query($conn, "SELECT * FROM user_tab");

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'No users found'
    ];
    goto end;
}

$users = [];
while ($row = mysqli_fetch_assoc($query)) {
    $users[] = $row;
}

$response = [
    'success' => true,
    'data' => $users
];

end:
echo json_encode($response);
?>
