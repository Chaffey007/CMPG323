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

//................................................................. Get banking data .................................................................
if($action == 'get'){
    $getDate = mysqli_real_escape_string($con, $_POST['date']);
    $length = 0;

    //... Day or Month ...
    $sepSubUnits = null;
    if((strpos($getDate, " ") !== false)){
        $sepSubUnits = explode(' ', $getDate);
        $length = count($sepSubUnits);
        if($length < 3){
            $queryGet = "SELECT * FROM `banking` WHERE `entry_date` LIKE '%$getDate'";
        }else{
            $queryGet = "SELECT * FROM `banking` WHERE `entry_date` = '$getDate'";
        }
    }else{
        $queryGet = "SELECT * FROM `banking` WHERE `entry_date` = '$getDate'";
    }
    //....... Get entries by date .........
    $resultGet = $con->query($queryGet);
    if(mysqli_num_rows($resultGet) > 0){
        while($reading = mysqli_fetch_assoc($resultGet)){
            if($reading['db_id'] != null){
                $response[] = [
                    'status' => 'Yes - Date',
                    'dbId' => $reading['db_id'],
                    'entryDate' => $reading['entry_date'],
                    'type' => $reading['entry_type'],
                    'stat' => $reading['entry_status'],
                    'descript' => $reading['entry_descript'],
                    'val' => $reading['entry_value'],
                    'user' => $reading['entry_user'],
                    'note' => $reading['entry_note'],
                    'editDate' => $reading['edit_date']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No - Empty Banking List'
        ];
    }
    array_push($list,$response);

    //........ Get Descriptions ...........
    $response = [];
    $queryGetA = "SELECT * FROM `banking`";
    $resultGetA = $con->query($queryGetA);
    if(mysqli_num_rows($resultGetA) > 0){
        while($readingA = mysqli_fetch_assoc($resultGetA)){
            if($readingA['db_id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'descript' => $readingA['entry_descript']
                ];
            }
        }
        //asort($response['descript']);
    }else{
        $response[] = [
            'status' => 'No - Empty Description List'
        ];
    }
    array_push($list,$response);

    //........ Get Balances ...........
    $response = [];
    $balType = 'Balance';
    $monthList = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $tmpMonth = null;
    $tmpMonthNo = 0;
    $tmpYear = 0;
    $tmpDay = 0;
    $tmpDate = null;
    $tmpLastMonthBal = 'none';
    //... If Single Day ...
    if($length == 3){
        //... If first day of the month ...
        if($sepSubUnits[0] == '1'){
            $tmpMonth = $sepSubUnits[1];
            //... Determine previous month last balance ...
            for($p = 0; $p < 12; $p++){
                if($tmpMonth == $monthList[$p]){
                    $tmpMonthNo = $p;
                    //... First month of the year ...
                    if($tmpMonthNo == 0){
                        $tmpMonthNo = 11;
                    }
                    //... Not first month of the year ...
                    else{
                        $tmpMonthNo--;
                    }
                }
            }

            //... Get last date ...
            $tmpMonth = $monthList[$tmpMonthNo];

            //... If First month of year ...
            if($tmpMonth == 'December'){
                $tmpYear = (int)$sepSubUnits[2];
                $tmpYear--;
            }else{
                $tmpYear = (int)$sepSubUnits[2];
            }

            $tmpDate = $tmpMonth . " " . $tmpYear;
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
            $resultGetA = $con->query($queryGetA);
            if(mysqli_num_rows($resultGetA) > 0){
                while($readingA = mysqli_fetch_assoc($resultGetA)){
                    $tmpDate = $readingA['entry_date'];
                }
            }else{
                $tmpDate = $tmpMonth . " " . $sepSubUnits[2];
            }
            //... Next Step ...
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
        }
        //... If not first day of the month ...
        else{
            $tmpDay = (int)$sepSubUnits[0];
            $tmpDay--;

            /********************** *****************************/
            /*
             * Get all dates for the month...
             * Determine last entry before current day...
             * Get balance for that day...
             */
            //... Get list of all dates in month ...
            $responseQ = [];
            $tmpDate = $sepSubUnits[1]." ".$sepSubUnits[2];
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate'";
            $resultGetA = $con->query($queryGetA);
            if(mysqli_num_rows($resultGetA) > 0){
                while($readingA = mysqli_fetch_assoc($resultGetA)){
                    if($readingA['db_id'] != null){
                        $responseQ[] = $readingA['entry_date'];
                    }
                }
            }
            //... Split dates and determine last entry ...
            $tmpSml = 0;
            $tmpLast = 999999;
            $tmpDays = [];
            $lengthA = count($responseQ);
            for($aa = 0; $aa < $lengthA; $aa++){
                if((strpos($responseQ[$aa], " ") !== false)){
                    $sepSubUnitsA = explode(' ', $responseQ[$aa]);
                    $tmpDays[] = $sepSubUnitsA[0];
                }
            }
            $lengthA = count($tmpDays);
            for($bb = 0; $bb < $lengthA; $bb++){
                if(((int)$tmpDays[$bb] > $tmpSml) && ((int)$tmpDays[$bb] <= $tmpLast) && ((int)$tmpDays[$bb] <= (int)$sepSubUnits[0])){
                    $tmpLast = (int)$tmpDays[$bb];
                }
            }
            //... If entry found or not ...
            if($tmpLast == 999999){

            }else{
                $tmpDate = $tmpLast. " " .$tmpMonth . " " . $tmpYear;
            }



















            /********************** *****************************/



            $tmpDate = $tmpDay." ".$sepSubUnits[1]." ".$sepSubUnits[2];
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
        }
    }
    //... If Full Month ...
    else if($length == 2){
        //... If First month of year ...
        if($sepSubUnits[0] == 'January'){
            $tmpMonth = 'December';
            $tmpYear = (int)$sepSubUnits[1];
            $tmpYear--;
            $tmpDate = $tmpMonth . " " . $tmpYear;
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
        }
        //... If not first month of the year ...
        else{
            $tmpMonth = $sepSubUnits[0];
            for($p = 0; $p < 12; $p++){
                if($tmpMonth == $monthList[$p]){
                    $tmpMonthNo = $p;
                    $tmpMonthNo--;
                }
            }
            $tmpMonth = $monthList[$tmpMonthNo];
            $tmpDate = $tmpMonth . " " . $sepSubUnits[1];
            $queryGetA = "SELECT * FROM `banking` WHERE `entry_type` = '$balType' AND `entry_date` LIKE '%$tmpDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
        }
    }

    $resultGetA = $con->query($queryGetA);
    if(mysqli_num_rows($resultGetA) > 0){
        while($readingA = mysqli_fetch_assoc($resultGetA)){
            if($readingA['db_id'] != null){
                $response[] = [
                    'status' => 'Yes - Previous Balance Detected',
                    'dbID' => $readingA['db_id'],
                    'entryDate' => $readingA['entry_date'],
                    'val' => $readingA['entry_value']
                ];
            }
        }
        //asort($response['descript']);
    }else{
        $response[] = [
            'status' => 'No - Empty Balance List ---- ' .$queryGetA
        ];
    }
    array_push($list,$response);

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
                    'profPic' => $readingA['user_prof_pic']
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

//................................................................. Add new Entry .................................................................
if($action == 'add'){
    $descript = mysqli_real_escape_string($con, $_POST['descript']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $entryDate = mysqli_real_escape_string($con, $_POST['date']);
    $val = mysqli_real_escape_string($con, $_POST['val']);
    $balance = mysqli_real_escape_string($con, $_POST['bal']);

    $user = $_SESSION['User_Id'];
    $priv = $_SESSION['userPriv'];
    $stat = 'Valid';
    $balDesc = 'Balance';
    $balUser = 'System';

    if((int)$priv >= 5){
        //...... New Book Entry ......
        $queryA = "INSERT INTO `banking` (entry_date, entry_descript, entry_type, entry_value, entry_user, entry_status, edit_date) ";
        $queryB = "VALUES ('$entryDate', '$descript', '$type', '$val', '$user', '$stat', '$date')";
        $query = $queryA.$queryB;
        if ($con->query($query) === TRUE) {
            //...... Balance Entry ......
            //... Get all ...
            $queryLocate = "SELECT * FROM `banking` WHERE `entry_type` = '$balDesc' AND `entry_date` = '$entryDate'";
            $resultLocate = $con->query($queryLocate);
            if(mysqli_num_rows($resultLocate) > 0){
                //... Update if existing ...
                $queryUpdateBalance = "UPDATE `banking` SET `entry_value` = '$balance' WHERE `entry_date` = '$entryDate' AND `entry_type` = '$balDesc'";
                if($con->query($queryUpdateBalance) === TRUE){
                    $list[] = [
                        'status' => 'Yes - Balance Updated for '.$entryDate,
                    ];
                }
                else{
                    $list[] = [
                        'status' => 'No - Balance Updated for '.$entryDate.' failed',
                    ];
                }
            }else{
                //... Insert if non existing ...
                $queryC = "INSERT INTO `banking` (entry_date, entry_descript, entry_type, entry_value, entry_user, entry_status, edit_date) ";
                $queryD = "VALUES ('$entryDate', '$balDesc', '$balDesc', '$balance', '$balUser', '$stat', '$date')";
                $queryE = $queryC.$queryD;

                if($con->query($queryE) === TRUE){
                    $list[] = [
                        'status' => 'Yes - Balance Insert Success for ' .$entryDate,
                    ];
                }else{
                    $list[] = [
                        'status' => 'No - Balance Insert Failed for ' .$entryDate,
                    ];
                }
            }
        }else{
            $list[] = [
                'status' => 'No - Book Entry Insert Error for' .$entryDate,
            ];
        }
    }else{
        $list[] = [
            'status' => 'No - No permission (1)',
        ];
    }
}
//................................................................. Update Balance .................................................................
if($action == 'balUpdate'){
    $updDate = mysqli_real_escape_string($con, $_POST['date']);
    $newVal = mysqli_real_escape_string($con, $_POST['val']);
    $balDesc = 'Balance';
    $newDate = null;

    //... Day or Month ...
    $sepSubUnits = null;
    if((strpos($updDate, " ") !== false)){
        $sepSubUnits = explode(' ', $updDate);
        $length = count($sepSubUnits);
        if($length < 3){
            $queryGet = "SELECT * FROM `banking` WHERE `entry_type` = '$balDesc' AND `entry_date` LIKE '%$updDate' ORDER BY LENGTH(entry_date) DESC, entry_date DESC LIMIT 1";
        }else{
            $queryGet = "SELECT * FROM `banking` WHERE `entry_type` = '$balDesc' AND `entry_date` = '$updDate' LIMIT 1";
        }
    }else{
        $queryGet = "SELECT * FROM `banking` WHERE `entry_date` = '0'";
    }
    //... Get exact date ...
    $resultGetA = $con->query($queryGet);
    if(mysqli_num_rows($resultGetA) == 1){
        while($readingA = mysqli_fetch_assoc($resultGetA)){
            $newDate = $readingA['db_id'];
        }
    }else{
        $newDate = '0';
    }

    //... Update ...
    $queryUpdateBalance = "UPDATE `banking` SET `entry_value` = '$newVal' WHERE `db_id` = '$newDate'";
    if($con->query($queryUpdateBalance) === TRUE){
        $list[] = [
            'status' => 'Yes - Balance Updated for '.$updDate,
        ];
    }
    else{
        $list[] = [
            'status' => 'No - Balance Updated for '.$updDate.' failed',
        ];
    }
}
//................................................................. Invalidate Entry .................................................................
if($action == 'invalidate'){
    $newId = mysqli_real_escape_string($con, $_POST['id']);
    $newStat = 'Invalid';
    $newUser = $_SESSION['user_emp_code'];

    //... Update ...
    $queryUpdateBalance = "UPDATE `banking` SET `entry_user` = '$newUser', `entry_status` = '$newStat' WHERE `db_id` = '$newId'";
    if($con->query($queryUpdateBalance) === TRUE){
        $list[] = [
            'status' => 'Yes - ' .$newId. ' Invalidated successfully.',
        ];
    }
    else{
        $list[] = [
            'status' => 'No - '.$newId.' failed to invalidate',
        ];
    }
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