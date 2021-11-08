<?php
date_default_timezone_set('UTC');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if(!isset($_POST)) die();
require_once 'DBConnection.php';

$response = [];
$responseOne = [];
$list = [];
$SESSION_array = array();

$date = date("Y-m-d H:i:s");
$action = $_POST['act'];

//................................................................. Get User data .................................................................
if($action == 'get'){
    $getDate = mysqli_real_escape_string($con, $_POST['date']);
    $length = 0;

    //........ Get Users ...........
    $response = [];
    $queryGetA = "SELECT * FROM `users`";
    $resultGetA = $con->query($queryGetA);
    if(mysqli_num_rows($resultGetA) > 0){
        while($readingA = mysqli_fetch_assoc($resultGetA)){
            if($readingA['db_id'] != null){
                $response[] = [
                    'status' => 'Yes - Got users',
                    'dbID' => $readingA['db_id'],
                    'fName' => $readingA['user_name'],
                    'lName' => $readingA['user_surname'],
                    'uName' => $readingA['user_username'],
                    'alias' => $readingA['user_alias'],
                    'pass' => $readingA['user_password'],
                    'priv' => $readingA['user_priv'],
                    'appDate' => $readingA['user_appoint_date'],
                    'mail' => $readingA['user_email'],
                    'num' => $readingA['user_number'],
                    'code' => $readingA['user_emp_code'],
                    'idNum' => $readingA['user_id_num'],
                    'bankAcc' => $readingA['user_bank_acc'],
                    'bankBran' => $readingA['user_bank_branch'],
                    'bankNme' => $readingA['user_bank_name'],
                    'profPic' => $readingA['user_prof_pic'],
                    'created' => $readingA['create_date'],
                    'edited' => $readingA['edit_date'],
                    'editBy' => $readingA['edit_by']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No - Empty User List'
        ];
    }
    array_push($list,$response);
}



if($list == null){
    $list[] = [
        'status' => 'No - Empty List',
    ];
}
if($_SESSION['dbStatus'] != ""){
    $list[] = [
        'status' => 'DB Problem',
    ];
}

echo json_encode($list);