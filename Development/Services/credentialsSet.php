<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
session_start();
$json = file_get_contents('php://input');
$obj = json_decode($json);
$response = [];
$response['status'] = $_SESSION['status'] = $obj->status;
$response['id'] = $_SESSION['id'] = $obj->id;
$response['userid'] = $_SESSION['User_Id'] = $obj->userid;
$response['userCode'] = $_SESSION['user_emp_code'] = $obj->userCode;
$response['firstname'] = $_SESSION['User_Name'] = $obj->firstname;
$response['lastname'] = $_SESSION['User_Surname'] = $obj->lastname;
$response['email'] = $_SESSION['User_Email'] = $obj->email;
$response['contactnum'] = $_SESSION['User_Contact'] = $obj->contactnum;
$response['user'] = $_SESSION['User_Username'] = $obj->user;
$response['userpas'] = $_SESSION['User_Password'] = $obj->userpas;
$response['userPriv'] = $_SESSION['userPriv'] = $obj->userPriv;
$response['profPic'] = $_SESSION['profPic'] = $obj->profPic;


echo json_encode($response);