<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
session_start();
$response = [];

if(!isset($_SESSION['status'])){
    $_SESSION['status'] = '';
}else if($_SESSION['status'] != 'loggedin'){
    $_SESSION['status'] = '';
}else{
    $response['status'] = $_SESSION['status'];
    $response['id'] = $_SESSION['id'];
    $response['userid'] = $_SESSION['User_Id'];
    $response['userCode'] = $_SESSION['user_emp_code'];
    $response['firstname'] = $_SESSION['User_Name'];
    $response['lastname'] = $_SESSION['User_Surname'];
    $response['email'] = $_SESSION['User_Email'];
    $response['contactnum'] = $_SESSION['User_Contact'];
    $response['user'] = $_SESSION['User_Username'];
    $response['userpas'] = $_SESSION['User_Password'];
    $response['userPriv'] = $_SESSION['userPriv'];
    $response['profPic'] = $_SESSION['profPic'];
}


echo json_encode($response);
?>