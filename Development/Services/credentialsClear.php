<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require_once 'DBConnection.php';
$response = [];

//... Update user status ...
$curLogStat = 'Offline';
$tmpId = $_SESSION['user_emp_code'];
$queryGetStats = "SELECT * FROM `user_stats` WHERE `user_emp_code`='$tmpId'";
$resultGetStats = mysqli_query($con, $queryGetStats);
$readingGetStats = mysqli_fetch_array($resultGetStats);
if(mysqli_num_rows($resultGetStats) == 1){
    if($readingGetStats['login_count'] !== NULL){
        $newCount = (int)$readingGetStats['login_count'];
    }
    $newCount += 1;
    //... Update User Stats ...
    $queryUpdateStats = "UPDATE `user_stats` SET `current_status` = '$curLogStat' WHERE `user_emp_code` = '$tmpId'";
    $con->query($queryUpdateStats);
}

//... Clear vars ...
$response['status'] = $_SESSION['status'] = "";
$response['user'] = $_SESSION['username'] = "";
$response['id'] = $_SESSION['id'] = "";
$response['firstname'] = $_SESSION['firstname'] = "";
$response['lastname'] = $_SESSION['lastname'] = "";


$_SESSION['User_Id'] = "";
$_SESSION['User_Name'] = "";
$_SESSION['User_Surname'] = "";
$_SESSION['User_Email'] = "";
$_SESSION['User_Contact'] = "";
$_SESSION['User_Username'] = "";
$_SESSION['User_Password'] = "";
$_SESSION['userPriv'] = "";
$_SESSION['profPic'] = "";
$_SESSION['user_emp_code'] = "";

session_destroy();

echo json_encode($response);
?>