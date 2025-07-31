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

 ?? '';

if (empty($userId)) {
    $response = [
        'success' => false,
        'message' => 'No userId provided'
    ];
    goto end;
}

$query = mysqli_query($conn, "SELECT userId, fullName, emailAddress, phoneNumber, passport FROM user_tab ");

if (mysqli_num_rows($query) == 0) {
    $response = [
        'success' => false,
        'message' => 'User not found'
    ];
    goto end;
}

$response = [
    'success' => true,
];

    $fetchQuery = mysqli_fetch_assoc($query);
    $fetchQuery['documentStoragePath'] = "$documentStoragePath/user-pics";
    $response['data'] = $fetchQuery;
end:
echo json_encode($response);
?>
