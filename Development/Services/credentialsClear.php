<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require_once 'DBConnection.php';
$response = [];

//... Update user status ...
$curLogStat = 'Offline';
$tmpId = $_SESSION['User_Id'];
//... Update User Stats ...
$queryUpdateStats = "UPDATE `users` SET `user_online` = 0 WHERE `user_id` = '$tmpId'";
$con->query($queryUpdateStats);

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
$_SESSION['profReg'] = "";

session_destroy();

echo json_encode($response);
?>