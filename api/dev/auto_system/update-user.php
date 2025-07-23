<?php require_once '../config/connection.php' ?>


<?php
///// check for API security
if ($apiKey != $expectedApiKey) {
    $response = [
        'response' => 98,
        'success' => false,
        'message' => 'SECURITY ACCESS DENIED! You are not allowed to execute this command due to a security breach.'
    ];
    goto end;
}



// variable declearation
$userId = $_GET['userId'];
$fullName = strtoupper(trim($_POST['fullName']));
$emailAddress = trim($_POST['emailAddress']);
$phoneNumber = trim($_POST['phoneNumber']);
$passport = $_FILES['passport']['name'];



// security for name
if ($fullName === "") {
    $response = [
        'response' => 100,
        'success' => false,
        'message' => " 'Full Name' REQUIRED! Enter Full Name 'Full Name' and try again.",
    ];
    goto end;
}

if (!preg_match("/^[A-Za-z\s]+$/", $fullName)) {
    $response = [
        'response' => 101,
        'success' => false,
        'message' => "Full Name is required! Please enter a valid full Name and try again.",
    ];
    goto end;
}

// SECURITY FOR emailAddress
if ($emailAddress === "") {
    $response = [
        'response' => 102,
        'success' => false,
        'message' => "'Email Address' REQUIRED! Provide a valid email and try again.",
    ];
    goto end;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 111,
        'success' => false,
        'message' => "INVALID 'Email Address' format! Please enter a valid email.",
    ];
    goto end;
}


// SECURITY FOR phonenumber
if ($phoneNumber === "") {
    $response = [
        'response' => 103,
        'success' => false,
        'message' => " 'Phone Number' REQUIRED! Provide the value for 'Phone Number' and try again.",
    ];
    goto end;
}

if (!is_numeric($phoneNumber)) {
    $response = [
        'response' => 104,
        'success' => false,
        'message' => "INVALID 'phoneNumber'! ENTER ONLY NUMBER."
    ];
    goto end;
}

if (strlen($phoneNumber) != 11) {
    $response = [
        'response' => 105,
        'success' => false,
        'message' => "INVALID 'Phone Number'! Phone number must be exactly 11 digits.",
    ];
    goto end;
}

//Security for passport

if (!$passport) {
    $response = [
        'response' => 107,
        'success' => false,
        'message' => "Passport Required. Please try again.",
    ];
    goto end;
}

//////////////check if email address already exist//////////////////////////
$query = mysqli_query($conn, "SELECT emailAddress FROM user_tab WHERE emailAddress = '$emailAddress' AND userId !='$userId'") or die(mysqli_error($conn));
$checkEmailExists = mysqli_num_rows($query);

if ($checkEmailExists > 0) {
    $response = [
        'response' => 110,
        'success' => false,
        'message' => "This email ('$emailAddress') is already in use. Please try another Email Address."
    ];
    goto end;
}

/// Handle passport upload ///
$allowedExts = ["jpg", "jpeg", "JPEG", "JPG", "gif", "png", "PNG", "GIF", "webp", "WEBP"];
$uploadPath = null;

if (isset($_FILES['passport']) && $_FILES['passport']['error'] != UPLOAD_ERR_NO_FILE) {
    $extension = pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION);

    if (!in_array($extension, $allowedExts)) {
        $response = [
            'response' => 108,
            'success' => false,
            'message' => 'INVALID PICTURE FORMAT! Check the picture format and try again.'
        ];
        goto end;
    }

    $userArray = $callclass->_getUserDetails($conn, $userId);
    $fetchArray = json_decode($userArray, true);
    $dbPassport = $fetchArray[0]['passport'];

    unlink($userProfilePixPath . $dbPassport);

    $datetime = date("Ymdhi");
    $passport = $userId . $datetime . $_FILES['passport']['name'];
    $uploadPath = $userProfilePixPath . $passport;

    if (!move_uploaded_file($_FILES["passport"]["tmp_name"], $uploadPath)) {
        $response = [
            'response' => 109,
            'success' => false,
            'message' => 'PICTURE UPLOAD ERROR! Contact your Engineer For Help.'
        ];
        goto end;
    }

    mysqli_query($conn, "UPDATE user_tab SET passport='$passport' WHERE userId='$userId'") or die(mysqli_error($conn));
}

$select = "SELECT * FROM user_tab WHERE userId = '$userId'";
$query = mysqli_query($conn, $select) or die(mysqli_error($conn));
$allRecordCount = mysqli_num_rows($query);

if ($allRecordCount == 0) {
    $response = [
        'response' => 111,
        'success' => false,
        'message' => "User with ID '$userId' does not exist!"
    ];
    goto end;
}


$update =
    "UPDATE user_tab SET 
    fullName = '$fullName',
    emailAddress = '$emailAddress',
    phoneNumber = '$phoneNumber',
    updatedTime = NOW()
    WHERE userId = '$userId'";


mysqli_query($conn, $update) or die(mysqli_error($conn));


$response = [
    'code' => 200,
    'success' => true,
    'message' => 'User updated successfully',
    'data' => [
        'userId' => $userId,
        'fullName' => $fullName,
        'emailAddress' => $emailAddress,
        'phoneNumber' => $phoneNumber,
        'passport' => $passport
    ],
];


end:
echo json_encode($response);
?>