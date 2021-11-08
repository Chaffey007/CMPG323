<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if(!isset($_POST)) die();
require_once 'DBConnection.php';
$response = [];
$usermail = mysqli_real_escape_string($con, $_POST['username']);
$pw = mysqli_real_escape_string($con, $_POST['password']);
$passHash = md5($pw);
$query = "SELECT * FROM `users` WHERE `user_username`='$usermail' AND `user_password`='$pw'";
$result = mysqli_query($con, $query);
$reading = mysqli_fetch_array($result);
if($_SESSION['dbStatus'] != ""){
    $response['status'] = "DB Problem";
}
else if(mysqli_num_rows($result) == 1) {
    //... Get user Info ...
    $response['status'] = 'loggedin';
    $response['id'] = md5(uniqid());
    $response['userid'] = $reading['db_id'];
    $response['userCode'] = $reading['user_emp_code'];
    $response['firstname'] = $reading['user_name'];
    $response['lastname'] = $reading['user_surname'];
    $response['email'] = $reading['user_email'];
    $response['contactnum'] = $reading['user_number'];
    $response['user'] = $reading['user_username'];
    $response['userpas'] = $reading['user_password'];
    $response['userPriv'] = $reading['user_priv'];
    $response['profPic'] = $reading['user_prof_pic'];

    $tmpId = $response['userCode'];
    //... Get User Devices ...
    /*$queryTwo = "SELECT * FROM `user_devices` WHERE `user_emp_code`='$tmpId'";
    $resultTwo = mysqli_query($con, $queryTwo);
    $readingTwo = mysqli_fetch_array($resultTwo);
    if(mysqli_num_rows($resultTwo) == 1){
        $response['setCurrency'] = $readingTwo['set_currency'];
        $response['setCountry'] = $readingTwo['set_country'];
        $response['setMeasureUnit'] = $readingTwo['set_measure_unit'];
        $response['setPushNotif'] = $readingTwo['set_notifications'];
        $response['setSmsNotif'] = $readingTwo['set_smsNotif'];
        $response['setSmsNo'] = $readingTwo['set_smsNo'];
    }else{
        $response['setCurrency'] = 'Default';
        $response['setCountry'] = 'Default';
        $response['setMeasureUnit'] = 'Default';
        $response['setPushNotif'] = 'Default';
        $response['setSmsNotif'] = 'Default';
        $response['setSmsNo'] = 'Default';
    }*/

    //... Get User Stats ...
    $newCount = 0;
    $curLogStat = 'Online';
    $nowDate = date("Y-m-d H:i:s");
    $queryGetStats = "SELECT * FROM `user_stats` WHERE `user_emp_code`='$tmpId'";
    $resultGetStats = mysqli_query($con, $queryGetStats);
    $readingGetStats = mysqli_fetch_array($resultGetStats);
    if(mysqli_num_rows($resultGetStats) == 1){
        if($readingGetStats['login_count'] !== NULL){
            $newCount = (int)$readingGetStats['login_count'];
        }
        $newCount += 1;
        //... Update User Stats ...
        $queryUpdateStats = "UPDATE `user_stats` SET `login_count` = '$newCount', `last_login` = '$nowDate', `current_status` = '$curLogStat' WHERE `user_emp_code` = '$tmpId'";
        $con->query($queryUpdateStats);
    }


} else {
    $queryUN = "SELECT * FROM `users` WHERE `user_username`='$usermail' ";
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
/*
print_r($_POST);*/