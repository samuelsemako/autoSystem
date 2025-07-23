<?php require_once '../config/connection.php' ?>

<?php
///// check for API security
if ($apiKey != $expectedApiKey) {
    $response = [
        'response' => 401,
        'success' => false,
        'message' => 'SECURITY ACCESS DENIED! You are not allowed to execute this command due to a security breach.'
    ];
    goto end;
}

$fullName = ($_POST["fullName"]);
$emailAddress = ($_POST["emailAddress"]);
$phoneNumber = ($_POST["phoneNumber"]);
$passport = $_FILES["passport"]['name'];



if (empty($fullName)) {
    $response = [
        'response' => 400,
        'success' => false,
        'message' => 'Enter full name to continue registration'
    ];
    goto end;
}

if (!preg_match("/^[a-zA-Z ]+$/", $fullName)) {
    $response = [
        'response' => 102,
        'success' => false,
        'message' => "FULL NAME MUST CONTAIN ONLY ALPHABET. INPUT A VALID VALUE TO CONTINUE",
    ];
    goto end;
}

if (empty($emailAddress)) {
    $response = [
        'response' => 400,
        'success' => false,
        'message' => 'Enter Email Address to continue registration'
    ];
    goto end;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 104,
        'success' => false,
        'message' => "INVALID INPUT! Email address is not valid Sam."
    ];
    goto end;
}

if ($phoneNumber == "") {
    $response = [
        'response' => 400,
        'success' => false,
        'message' => 'Enter phone number to continue registration'
    ];
    goto end;
}

if (empty($phoneNumber)) {
    $response = [
        'response' => 103,
        'success' => false,
        'message' => "PHONE NUMBER REQUIRED! Provide PHONE NUMBER and try again.",
    ];
    goto end;
}

if (!is_numeric($phoneNumber)) {
    $response = [
        'response' => 103,
        'success' => false,
        'message' => "INVALID PHONE NUMBER! ENTER ONLY DIGITS."
    ];
    goto end;
}

if (strlen($phoneNumber) != 11) {
    $response = [
        'response' => 104,
        'success' => false,
        'message' => "INVALID PHONE NUMBER! NUMBER MUST BE EXACTLY 11 DIGITS."
    ];
    goto end;
}



if (!isset($_FILES['passport']) || $_FILES['passport']['error'] !== 0) {
    $response = [
        'response' => 103,
        'success' => false,
        'message' => "PASSPORT REQUIRED! Provide PASSPORT and try again."
    ];
    goto end;
}

//////////////check if email address already exist//////////////////////////
$query = mysqli_query($conn, "SELECT emailAddress FROM user_tab WHERE emailAddress = '$emailAddress'") or die(mysqli_error($conn));
$checkEmailExists = mysqli_num_rows($query);

if ($checkEmailExists > 0) {
    $response = [
        'response' => 110,
        'success' => false,
        'message' => "This email ('$emailAddress') is already in use. Please try another Email Address."
    ];
    goto end;
}

//////////////geting sequence//////////////////////////
$sequence = $callclass->_getSequenceCount($conn, 'USER');
$array = json_decode($sequence, true);
$no = $array[0]['no'];

/// generate loanId //////
$userId = 'USER' . $no . date("Ymdhis");

/// Handle passport upload ///
$allowedExts = array("jpg", "jpeg", "JPEG", "JPG", "gif", "png", "PNG", "GIF", "webp", "WEBP");
$extension = pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION);

if (!in_array(($extension), $allowedExts)) {
    $response = [
        'response' => 111,
        'success' => false,
        'message' => 'INVALID PICTURE FORMAT! Check the picture format and try again.'
    ];
    goto end;
}

$datetime = date("Ymdhi");
$passport = $userId . '' . $datetime . '' . $passport;
$uploadPath = $userProfilePixPath . $passport;

if (!move_uploaded_file($_FILES["passport"]["tmp_name"], $uploadPath)) {
    $response = [
        'response' => 112,
        'success' => false,
        'message' => 'PICTURE UPLOAD ERROR! Contact your Engineer For Help'
    ];
    goto end;
}

mysqli_query($conn, "INSERT INTO user_tab 
    (userId, fullName, emailAddress, phoneNumber, passport, createdTime, updatedTime) VALUES 
    ('$userId', '$fullName', '$emailAddress', '$phoneNumber', '$passport', NOW(), NOW())") or die(mysqli_error($conn));

$response = [
    'response' => 200,
    'success' => true,
    'message' => "Record created successfully",
    'data' => [
        'userId' => $userId,
        'fullName' => $fullName,
        'emailAddress' => $emailAddress,
        'phoneNumber' => $phoneNumber,
        'passport' => $passport
    ],

];
goto end;


end:
echo json_encode($response);
?>