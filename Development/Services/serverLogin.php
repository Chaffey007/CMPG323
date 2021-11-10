<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if(!isset($_POST)) die();
require_once 'DBConnection.php';
$response = [];
$usermail = mysqli_real_escape_string($con, $_POST['username']);
$pw = mysqli_real_escape_string($con, $_POST['password']);
$passHash = md5($pw);
$query = "SELECT * FROM `users` WHERE `user_uname`='$usermail' AND `user_passw`='$passHash'";
$result = mysqli_query($con, $query);
$reading = mysqli_fetch_array($result);
if($_SESSION['dbStatus'] != ""){
    $response['status'] = "DB Problem";
}
else if(mysqli_num_rows($result) == 1) {
    //... Get user Info ...
    $response['status'] = 'loggedin';
    $response['id'] = md5(uniqid());
    $response['userid'] = $reading['user_id'];
    $response['firstname'] = $reading['user_fname'];
    $response['lastname'] = $reading['user_lname'];
    $response['email'] = $reading['user_email'];
    $response['contactnum'] = $reading['user_tel'];
    $response['user'] = $reading['user_uname'];
    $response['userpas'] = $reading['user_passw'];
    $response['userPriv'] = $reading['user_type'];
    $response['userReg'] = $reading['user_reg_date'];

    $tmpId = $response['userid'];

    //... Update User Stats ...
    $queryUpdateStats = "UPDATE `users` SET `user_online` = 1 WHERE `user_id` = '$tmpId'";
    $con->query($queryUpdateStats);


} else {
    $queryUN = "SELECT * FROM `users` WHERE `user_uname`='$usermail' ";
    $resultUN = mysqli_query($con, $queryUN);
    $readingUN = mysqli_fetch_array($resultUN);
    if(mysqli_num_rows($resultUN) == 0){
        $response['status'] = 'notFound';
        $_SESSION['status'] = 'notFound';
    }
    else{
        $response['status'] = 'error';
        $_SESSION['status'] = 'error';
    }
}
echo json_encode($response);