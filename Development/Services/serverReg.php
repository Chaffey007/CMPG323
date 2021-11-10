<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if(!isset($_POST)) die();
require_once 'DBConnection.php';
$response = [];
$date = date("Y-m-d H:i:s");
$userFname = mysqli_real_escape_string($con, $_POST['firstname']);
$userLname = mysqli_real_escape_string($con, $_POST['lastname']);
$userMail = mysqli_real_escape_string($con, $_POST['email']);
$userUname = mysqli_real_escape_string($con, $_POST['username']);
$pw = mysqli_real_escape_string($con, $_POST['password']);
$passHash = md5($pw);
$query = "SELECT * FROM `users` WHERE `user_uname`='$userUname' AND `user_passw`='$passHash'";
$result = mysqli_query($con, $query);
$reading = mysqli_fetch_array($result);
if($_SESSION['dbStatus'] != ""){
    $response['status'] = "DB Problem";
}
else if(mysqli_num_rows($result) > 0) {
    $response['status'] = 'Found';
    $_SESSION['status'] = 'Found';
} else {
    $queryA = "INSERT INTO `users` (user_fname, user_lname, user_uname, user_passw, user_email, user_reg_date) ";
    $queryB = "VALUES ('$userFname', '$userLname', '$userUname', '$passHash', '$userMail', '$date')";
    $query = $queryA.$queryB;
    if($con->query($query) === TRUE){
        $response['status'] = 'Success';
        //$_SESSION['status'] = 'Success';
    }else{
        $response['status'] = 'Fail';
        //$_SESSION['status'] = 'Fail';
    }
}
echo json_encode($response);