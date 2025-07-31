<?php require_once '../config/connection.php' ?>
<?php if (!$checkBasicSecurity) {goto end;}?>



<?php

$fullName = ($_POST["fullName"]);
$emailAddress = ($_POST["emailAddress"]);
$phoneNumber = ($_POST["phoneNumber"]);
$passport = $_FILES["passport"]['name'];


validateEmptyField($fullName, 'FULL NAME');
validateEmptyField($emailAddress, 'EMAIL');
validateEmptyField($phoneNumber, 'PHONE NUMBER');


if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'response' => 104,
        'success' => false,
        'message' => "INVALID INPUT! Email address is not valid."
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

$allowedExts = array("jpg", "jpeg", "JPEG", "JPG", "gif", "png", "PNG", "GIF", "webp", "WEBP");
$extension = pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION);

if (!in_array(($extension), $allowedExts)) {
    $response = [
        'response' => 111,
        'success' => false,
        'message' => 'ERROR! Input passport to continue'
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
$passport = $userId . $passport;
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
    ];

    ///// get user
    $query = mysqli_query($conn, "SELECT userId, fullName, emailAddress, phoneNumber, passport, createdTime FROM user_tab WHERE userId = '$userId'");
    $response['data']=array();
    $fetchQuery = mysqli_fetch_assoc($query);
    $fetchQuery['documentStoragePath'] = "$documentStoragePath/user-pics";
    $response['data']=$fetchQuery;
    

end:
echo json_encode($response);
?>