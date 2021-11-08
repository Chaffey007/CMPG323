<?php
date_default_timezone_set('UTC');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
if(!isset($_POST)) die();
require_once 'DBConnection.php';
include 'autoUpdateLogistics.php';
$response = [];
$responseOne = [];
$list = [];
$SESSION_array = array();
//... If Logged in or not ...
if(!isset($_SESSION['User_Id'])){
    $uID = 0;
}else{
    $uID = $_SESSION['User_Id'];
}
$compId = $compProf = '';
$date = date("Y-m-d H:i:s");
/** If user has company profile **/
if(!isset($_SESSION['compProfile'])){
    $_SESSION['compId'] = '';
}
else if($_SESSION['compProfile'] == 'Yes'){
    $compId = $_SESSION['compId'];
    $compProf = 'Yes';
}
//$action = mysqli_real_escape_string($con, $_POST['compName']);
$action = $_POST['act'];

//... Current Selected Profile ...
$curProf = '';
if(!isset($_SESSION['selectedProf'])){
    $curProf = '';
}else{
    $curProf = $_SESSION['selectedProf'];
}
$testBlank = '';


//... Get Logistics ...
if($action == 'get'){
    if(($compProf == 'Yes') && ($curProf == 'Business')){
        $queryLocate = "SELECT * FROM `logistics` WHERE Company_Created = '$compId'";
    }else if($curProf == 'Private'){
        $queryLocate = "SELECT * FROM `logistics` WHERE User_Created = '$uID' AND (Company_Created is null OR Company_Created = '$testBlank')";
    }else if($curProf == 'Manager'){
        $queryLocate = "SELECT * FROM `logistics` WHERE Acc_Manager = '$uID'";
    } else if($curProf == 'Admin'){
        $queryLocate = "SELECT * FROM `logistics` ";
    }
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['Logistic_Id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['DB_id'],
                    'id' => $reading['Logistic_Id'],
                    'listType' => $reading['List_Type'],
                    'typeStat' => $reading['Type_Status'],
                    'descript' => $reading['Logistic_Description'],
                    'company' => $reading['Company_Created'],
                    'user' => $reading['User_Created'],
                    'dateCreate' => $reading['Date_Created'],
                    'dateReady' => $reading['Date_Ready'],
                    'dateBooked' => $reading['Date_Booked'],
                    'validTill' => $reading['Valid_Until'],
                    'servType' => $reading['Service_Type'],
                    'inco' => $reading['Inco_Term'],
                    'direction' => $reading['Logistic_Direction'],
                    'addrFrom' => $reading['From_Address'],
                    'addrTo' => $reading['To_Address'],
                    'vol' => $reading['Total_Volume'],
                    'weight' => $reading['Total_Weight'],
                    'unit' => $reading['Mesure_Unit'],
                    'countryFrom' => $reading['From_Country'],
                    'countryTo' => $reading['To_Country'],
                    'datePickup' => $reading['Pickup_Date'],
                    'dateDeliv' => $reading['Deliver_Date'],
                    'progres' => $reading['Progress_Status'],
                    'itemCnt' => $reading['Item_Count'],
                    'amount' => $reading['Quote_Amount'],
                    'curr' => $reading['Quote_Cur'],
                    'chosen' => $reading['Type_Chosen'],
                    'fromLong' => $reading['From_Long'],
                    'fromLat' => $reading['From_Lat'],
                    'toLong' => $reading['To_Long'],
                    'toLat' => $reading['To_Lat'],
                    'docReq' => $reading['Doc_Req'],
                    'docProv' => $reading['Doc_Prov'],
                    'reqType' => $reading['Request_Type'],
                    'accMan' => $reading['Acc_Manager'],
                    'qCar' => $reading['Q_Car'],
                    'qQuoteNo' => $reading['Q_Quote_No'],
                    'qTransTime' => $reading['Q_TransTime'],
                    'qServ' => $reading['Q_Serv'],
                    'qOrChrg' => $reading['Q_OrChrg'],
                    'qMainChrg' => $reading['Q_MainChrg'],
                    'qDesChrg' => $reading['Q_DesChrg'],
                    'qCustChrg' => $reading['Q_CustChrg'],
                    'qICD' => $reading['Q_ICD'],
                    'qCVD' => $reading['Q_CVD'],
                    'qVAT' => $reading['Q_VAT'],
                    'qExVat' => $reading['Q_ExVat'],
                    'qInVat' => $reading['Q_InVat'],
                    'qCurrency' => $reading['Q_Currency'],
                    'setTransTime' => $reading['Set_TransTime']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }
    array_push($list,$response);
    //...................... Get Support Docs .............................
    $response = [];
    if(($compProf == 'Yes') && ($curProf == 'Business')){
        $queryLocate = "SELECT * FROM `support_docs` WHERE shipment_user = '$compId'";
    }else if ($curProf == 'Private'){
        $queryLocate = "SELECT * FROM `support_docs` WHERE shipment_user = '$uID'";
    }else if($curProf == 'Admin'){
        $queryLocate = "SELECT * FROM `support_docs` ";
    }
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['doc_id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['doc_id'],
                    'id' => $reading['shipment_id'],
                    'name' => $reading['doc_name'],
                    'dateUploaded' => $reading['date_uploaded'],
                    'validity' => $reading['valid_status'],
                    'version' => $reading['version_number'],
                    'user' => $reading['shipment_user'],
                    'title' => $reading['doc_title']
                ];
            }
            else{
                $response[] = [
                    'status' => 'No'
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }

    array_push($list,$response);
    //...................... Get All Packing Lists .............................
    $response = [];
    if(($compProf == 'Yes') && ($curProf == 'Business')){
        $queryLocate = "SELECT * FROM `packing_list` WHERE user_id = '$compId'";
    }else if ($curProf == 'Private'){
        $queryLocate = "SELECT * FROM `packing_list` WHERE user_id = '$uID'";
    }else if($curProf == 'Admin'){
        $queryLocate = "SELECT * FROM `packing_list` ";
    }
    //$query = "SELECT * FROM `packing_list` WHERE user_id = '$uID'";
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['db_ID'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbID' => $reading['db_ID'],
                    'shipID' => $reading['ship_id'],
                    'itemID' => $reading['item_id'],
                    'descript' => $reading['item_descript'],
                    'qty' => $reading['item_qty'],
                    'length' => $reading['item_length'],
                    'width' => $reading['item_width'],
                    'height' => $reading['item_height'],
                    'volume' => $reading['item_volume'],
                    'weight' => $reading['item_weight'],
                    'value' => $reading['item_value'],
                    'cur' => $reading['item_cur'],
                    'user' => $reading['user_id']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }

    array_push($list,$response);
    //...................... Get Carrier Lists .............................
    $response = [];
    $queryLocate = "SELECT * FROM `carriers` ";
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['db_id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['db_id'],
                    'name' => $reading['carrier_name']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }

    array_push($list,$response);
    //...................... Get Required Docs Lists .............................
    $response = [];
    $queryLocate = "SELECT * FROM `req_docs` ";
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['db_id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['db_id'],
                    'name' => $reading['doc_name']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }

    array_push($list,$response);
    //...................... Get Tracking Notes .............................
    $response = [];
    $queryLocate = "SELECT * FROM `tracking_notes` ";
    $resultLocate = $con->query($queryLocate);
    if(mysqli_num_rows($resultLocate) > 0){
        while($reading = mysqli_fetch_assoc($resultLocate)){
            if($reading['db_id'] != null){
                $response[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['db_id'],
                    'shipID' => $reading['Shipment_Id'],
                    'descript' => $reading['Track_descript'],
                    'date' => $reading['Track_Date'],
                    'title' => $reading['Track_Title']
                ];
            }
        }
    }else{
        $response[] = [
            'status' => 'No'
        ];
    }
    array_push($list,$response);
}

//... Get Logistics for Profile ...
if($action == 'getProf'){
    $active = 0;
    $done = 0;
    $pend = 0;
    $ready = 0;
    $loc = 0;
    $prod = 0;
    if(($compProf == 'Yes') && ($curProf == 'Business')){
        $queryLogist = "SELECT * FROM `logistics` WHERE Company_Created = '$compId'";
        $queryLoc= "SELECT * FROM `locations` WHERE Loc_User_ID = '$compId'";
        $queryProd= "SELECT * FROM `units` WHERE Unit_Client = '$compId'";
    }else if($curProf == 'Private'){
        $queryLogist = "SELECT * FROM `logistics` WHERE User_Created = '$uID' AND (Company_Created is null OR Company_Created = '$testBlank')";
        $queryLoc= "SELECT * FROM `locations` WHERE Loc_User_ID = '$uID'";
        $queryProd= "SELECT * FROM `units` WHERE Unit_Client = '$uID'";
    }else if($curProf == 'Manager'){
        $queryLogist = "SELECT * FROM `logistics` WHERE Acc_Manager = '$uID'";
        $queryLoc= "SELECT * FROM `locations` WHERE Acc_Manager = '$uID'";
        $queryProd= "SELECT * FROM `units` WHERE Unit_Client = '$uID'";
    } else if($curProf == 'Admin'){
        $queryLogist = "SELECT * FROM `logistics` ";
        $queryLoc= "SELECT * FROM `locations` ";
        $queryProd= "SELECT * FROM `units` ";
    }
    $resultLogist = $con->query($queryLogist);
    while($reading = mysqli_fetch_assoc($resultLogist)){
        if($reading['Logistic_Id'] != null){
            if(($reading['List_Type'] == 'Ship') && (($reading['Type_Status'] == 'SFP') || ($reading['Type_Status'] == 'InTrans'))){
                $active++;
            }
            if(($reading['List_Type'] == 'Ship') && ($reading['Type_Status'] == 'Deliv')){
                $done++;
            }
            if(($reading['List_Type'] == 'Quote') && ($reading['Type_Status'] == 'Pending')){
                $pend++;
            }
            if(($reading['List_Type'] == 'Quote') && ($reading['Type_Status'] == 'Ready')){
                $ready++;
            }
        }
    }
    $resultLoc = $con->query($queryLoc);
    while($reading = mysqli_fetch_assoc($resultLoc)){
        if($reading['Loc_Id'] != null){
            $loc++;
        }
    }
    $resultProd = $con->query($queryProd);
    while($reading = mysqli_fetch_assoc($resultProd)){
        if($reading['Unit_Id'] != null){
            $prod++;
        }
    }
    $list[] = [
        'status' => 'Yes',
        'active' => $active,
        'done' => $done,
        'pq' => $pend,
        'rq' => $ready,
        'loc' => $loc,
        'prod' => $prod
    ];
}

//... Add Carrier Data To Pending Quote ...
if($action == 'addQuote'){
    $list = [];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $e = $_POST['e'];
    $f = $_POST['f'];
    $g = $_POST['g'];
    $h = $_POST['h'];
    $i = $_POST['i'];
    $j = $_POST['j'];
    $k = $_POST['k'];
    $l = $_POST['l'];
    $m = $_POST['m'];
    $n = $_POST['n'];
    $o = $_POST['o'];
    $p = $_POST['p'];
    $q = $_POST['q'];
    $r = $_POST['r'];

    $aa = "UPDATE `logistics` SET `Doc_Req` = '$a', `Doc_Prov` = '$b', `Q_Car` = '$d', ";
    $bb = "`Q_Quote_No` = '$e', `Q_TransTime` = '$f', `Q_Serv` = '$g', `Q_Currency` = '$h', ";
    $cc = "`Q_OrChrg` = '$i', `Q_MainChrg` = '$j', `Q_DesChrg` = '$k', `Q_CustChrg` = '$l', ";
    $dd = "`Q_ICD` = '$m', `Q_CVD` = '$n', `Q_VAT` = '$o', `Q_ExVat` = '$p', ";
    $ee = "`Q_InVat` = '$q', `Valid_Until` = '$r' ";
    $ff = "WHERE `Logistic_Id` = '$c'";
    $queryUpdateQuote = $aa.$bb.$cc.$dd.$ee.$ff;
    if($con->query($queryUpdateQuote) === TRUE){
        $list[] = [
            'status' => 'Yes',
        ];
    }
    else{
        $list[] = [
            'status' => 'No',
        ];
    }
}

//... Send Pending Quote To Ready Quotes ...
if($action == 'approveQuote'){
    $shipId = mysqli_real_escape_string($con, $_POST['i']);
    $stat = 'Ready';

    $queryUpdateQuote = "UPDATE `logistics` SET `Type_Status` = '$stat', `Date_Ready` = '$date' WHERE `Logistic_Id` = '$shipId'";
    if($con->query($queryUpdateQuote) === TRUE){
        $list[] = [
            'status' => 'Yes',
        ];
    }
    else{
        $list[] = [
            'status' => 'No',
        ];
    }

    //... Get Current quote's User and send notification ...
    $tmpUserId = null;
    $queryNewOne = "SELECT * FROM `logistics` WHERE Logistic_Id = '$shipId' LIMIT 1";
    $resultNewOne = $con->query($queryNewOne);
    while($readingNewOne = mysqli_fetch_assoc($resultNewOne)){
        $tmpUserId = $readingNewOne['User_Created'];
    }
    $tmpSmsSet = '';
    $tmpSmsNo = '';
    $queryNewTwo = "SELECT * FROM `user_settings` WHERE User_Id = '$tmpUserId' LIMIT 1";
    $resultNewTwo = $con->query($queryNewTwo);
    while($readingNewTwo = mysqli_fetch_assoc($resultNewTwo)){
        $tmpSmsSet = $readingNewTwo['set_smsNotif'];
        $tmpSmsNo = $readingNewTwo['set_smsNo'];
    }
    if($tmpSmsSet == 'yes'){
        /************************************ SMS *****************************************/
        //... New Quote Ready SMS to client ...
        require_once "../vendor/autoload.php";
        $basic  = new \Nexmo\Client\Credentials\Basic('e3f10198', 'AKsP9W932s05aKJg');
        $client = new \Nexmo\Client($basic);

        try {
            $message = $client->message()->send([
                'to' => $tmpSmsNo,
                'from' => 'Intellicargo',
                'text' => 'Quote #' .$shipId. ' is ready to book. Visit www.intellicargoi.com to view all the options.'
            ]);
        } catch (Exception $e) {
            $message = $e->getEntity();
            //$response['status'] = "SMS failed:\r\n" . $message['error_text'];
        }
        /************************************ EMAIL *****************************************/
        $tmpUserMail = null;
        $queryNewOne = "SELECT * FROM `user` WHERE User_Id = '$tmpUserId' LIMIT 1";
        $resultNewOne = $con->query($queryNewOne);
        while($readingNewOne = mysqli_fetch_assoc($resultNewOne)){
            $tmpUserMail = $readingNewOne['User_Email'];
        }
        $to = $tmpUserMail. ", backup@intellicargogroup.co.za";
        $subject = 'Quote Update';

        $message = '<html><head>';
        $message .= '<title>Update for Quote #'.$shipId.'</title>';
        $message .= '<style>
                body{
                background: rgba(0,0,0,0);
                color: rgba(100,100,100,1);
                }
                </style>';
        $message .= '</head><body>';
        $message .= '<img src="https://www.intellicargoi.com/Images/Intellicargo Logo small.png"/>';
        $message .= '<p style="font-size: 18px;">Quote #' .$shipId. ' is ready to book.</p><br>';
        $message .= '<p style="font-size: 15px">Visit <a href="www.intellicargoi.com" style="color: rgba(0,128,255,1);">www.intellicargoi.com</a> to view all the options.</p><br><br>';
        $message .= '<p style="font-size: 18px;">Regards</p>';
        $message .= '<p style="font-size: 18px;">Intellicargo International</p>';
        $message .= "</body></html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
        $headers .= 'From: <noreply@intellicargoi.com>' . "\r\n";

        $mail_sent = @mail( $to, $subject, $message, $headers );
        $result = $mail_sent ? "Mail sent" : "Mail failed";
    }
}

//... Book Quote (Send to Shipments) ...
if($action == 'bookQuote'){
    $shipId = $_POST['a'];
    $serv = $_POST['b'];
    $inVat = $_POST['o'];
    $curr = $_POST['p'];
    $newTpe = 'Ship';
    $newSt = 'SFP';

    $queryBookShip = "UPDATE `logistics` SET `Quote_Cur` = '$curr', `Quote_Amount` = '$inVat', `List_Type` = '$newTpe', `Type_Status` = '$newSt', `Date_Booked` = '$date', `Type_Chosen` = '$serv' WHERE `Logistic_Id` = '$shipId'";
    if($con->query($queryBookShip) === TRUE){
        /************************************ EMAIL *****************************************/
        $to = "backup@intellicargogroup.co.za";
        $subject = 'Quote Booking';

        $message = '<html><head>';
        $message .= '<title>Booking Details for Quote #'.$shipId.'</title>';
        $message .= '<style>
                body{
                background: rgba(0,0,0,0);
                color: rgba(100,100,100,1);
                }
                </style>';
        $message .= '</head><body>';
        $message .= '<img src="https://www.intellicargoi.com/Images/Intellicargo Logo small.png"/>';
        $message .= '<p style="font-size: 18px;">Booking Details for Quote #' .$shipId. '.</p><br>';
        $message .= '<p style="font-size: 18px;">Service: ' .$serv. '.</p>';
        $message .= '<p style="font-size: 18px;">Total Including Vat: ' .$inVat. '&nbsp;<font color="rgba(0,128,255,1)">'.$curr.'</font>.</p>';
        $message .= '<p style="font-size: 18px;">Date Booked: ' .$date. '.</p><br><br>';
        $message .= '<p style="font-size: 18px;">Regards</p>';
        $message .= '<p style="font-size: 18px;">Intellicargo International</p>';
        $message .= "</body></html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
        $headers .= 'From: <noreply@intellicargoi.com>' . "\r\n";

        $mail_sent = @mail( $to, $subject, $message, $headers );
        $result = $mail_sent ? "Mail sent" : "Mail failed";

        $list[] = [
            'status' => 'Yes',
        ];
    }
    else{
        $list[] = [
            'status' => 'No',
        ];
    }
}

//... New Quote ...
if($action == 'newQuote'){
    $servType = mysqli_real_escape_string($con, $_POST['a']);
    $incoterm = mysqli_real_escape_string($con, $_POST['b']);
    $orAddr = mysqli_real_escape_string($con, $_POST['e']);
    $desAddr = mysqli_real_escape_string($con, $_POST['f']);
    $orCount = mysqli_real_escape_string($con, $_POST['g']);
    $desCount = mysqli_real_escape_string($con, $_POST['h']);
    $unitSys = mysqli_real_escape_string($con, $_POST['i']);
    $combUnits = mysqli_real_escape_string($con, $_POST['j']);
    $shipName = mysqli_real_escape_string($con, $_POST['k']);
    $cur = mysqli_real_escape_string($con, $_POST['l']);
    //$cur = 'USD';
    $lastEntry = 0;
    $logistID = '';
    $listType = 'Quote';
    $typeStat = 'Pending';
    $comp = null;
    $dir = 'Inbound';
    $blank = null;
    $mesUnit = null;
    $tmpStat = 'OnTime';
    $tst = '';
    $profIns = '';
    $reqFrom = 'Priv';

    //... Get Mesure Unit ...
    if($unitSys == true){
        $mesUnit = 'Metric';
    }else{
        $mesUnit = 'Imperial';
    }
    //... Seperate Units ...
    $sepUnits = null;
    $sepSubUnits = null;
    if((strpos($combUnits, ";") !== false)){
        $sepUnits = explode(';', $combUnits);
        $length = count($sepUnits);
        //... Seperate unit data ...
        for($q = 0; $q < $length; $q++){
            if((strpos($sepUnits[$q], ",") !== false)){
                $sepSubUnits[$q] = explode(',', $sepUnits[$q]);
            }
        }
        $listLength = count($sepUnits);
    }
    else{
        $sepUnits = $combUnits;
        $listLength = 1;
        $sepSubUnits = explode(',', $sepUnits);
    }

    //... Calculate individual Volumes & Total Volume & Total Weight & Total Item Count & Total Value ...
    $sepVol = $totVol = $totWeight = $totItem = $totVal = $singVol = null;
    if($listLength > 1){
        for($t = 0; $t < $listLength; $t++){
            $sepVol[$t] = ((double)$sepSubUnits[$t][2] * (double)$sepSubUnits[$t][3] * (double)$sepSubUnits[$t][4] * (double)$sepSubUnits[$t][5]);
            $singVol[$t] = ((double)$sepSubUnits[$t][2] * (double)$sepSubUnits[$t][3] * (double)$sepSubUnits[$t][4]);
            $totVol += (double)$sepVol[$t];
            $totWeight += ((double)$sepSubUnits[$t][1] * (double)$sepSubUnits[$t][5]);
            $totItem += (double)$sepSubUnits[$t][5];
            $totVal += (double)$sepSubUnits[$t][6];
        }
    }else{
        $sepVol = ((double)$sepSubUnits[2] * (double)$sepSubUnits[3] * (double)$sepSubUnits[4] * (double)$sepSubUnits[5]);
        $singVol = ((double)$sepSubUnits[2] * (double)$sepSubUnits[3] * (double)$sepSubUnits[4]);
        $totVol .= (double)$sepVol;
        $totWeight .= (double)$sepSubUnits[1];
        $totItem .= (double)$sepSubUnits[5];
        $totVal .= (double)$sepSubUnits[6];
    }

    //... Get Last DBID By current user ...
    if($compProf == 'Yes'){
        $queryLocate = "SELECT * FROM `logistics` WHERE Company_Created = '$compId' ORDER BY DB_id DESC LIMIT 1";
        $profIns = $logistID = $comp = $compId;
    }else{
        $queryLocate = "SELECT * FROM `logistics` WHERE User_Created = '$uID' ORDER BY DB_id DESC LIMIT 1";
        $profIns = $logistID = $uID;
    }
    $resultLocate = $con->query($queryLocate);
    while($reading = mysqli_fetch_assoc($resultLocate)){
        if($reading['DB_id'] != null){
            $lastEntry = (int)$reading['DB_id'] + 1;
        }
    }

    //... Generate The new Logistic ID ...
    $tmpDate = date("Ymd");
    $logistID .= $tmpDate . $lastEntry;

    //... Set the insertQuery ...
    $queryA = "INSERT INTO `logistics` (Logistic_Id, List_Type, Type_Status, Logistic_Description, Company_Created, User_Created, Date_Created, Date_ready, Date_Booked, Valid_Until, Service_Type, Inco_Term, Logistic_Direction, From_Address, To_Address, From_Long, From_Lat, To_Long, To_Lat, Total_Volume, Total_Weight, Mesure_Unit, From_Country, To_Country, Deliver_Date, Progress_Status, Item_Count, Quote_Amount, Quote_Cur, Request_Type) ";
    $queryB = "VALUES ('$logistID', '$listType', '$typeStat', '$shipName', '$comp', '$uID', '$date', '$typeStat', '$typeStat', '$typeStat', '$servType', '$incoterm', '$dir', '$orAddr', '$desAddr', '$blank', '$blank', '$blank', '$blank', '$totVol', '$totWeight', '$mesUnit', '$orCount', '$desCount', '$typeStat', '$tmpStat', '$totItem', '$totVal', '$cur', '$reqFrom')";
    $query = $queryA.$queryB;
    if ($con->query($query) === TRUE) {
        $z = 0;
        //... If Single item Or Multiple Items ...
        if($listLength > 1){
            for($t = 0; $t < $listLength; $t++){
                //... Set the insertQuery ...
                $queryC = "INSERT INTO `packing_list` (item_weight, ship_id, item_id, item_descript, item_qty, item_length, item_width, item_height, item_volume, item_value, item_cur, user_id) ";
                $queryD = "VALUES ('{$sepSubUnits[$t][1]}', '{$logistID}', '{$blank}', '{$sepSubUnits[$t][0]}', '{$sepSubUnits[$t][5]}', '{$sepSubUnits[$t][2]}', '{$sepSubUnits[$t][4]}', '{$sepSubUnits[$t][3]}', '{$singVol[$t]}', '{$sepSubUnits[$t][6]}', '{$cur}', '{$profIns}')";
                $queryE = $queryC.$queryD;
                if ($con->query($queryE) === TRUE) {
                    $z++;
                }
            }
        }
        else{
            //... Set the insertQuery ...
            $queryC = "INSERT INTO `packing_list` (item_weight, ship_id, item_id, item_descript, item_qty, item_length, item_width, item_height, item_volume, item_value, item_cur, user_id) ";
            $queryD = "VALUES ('$sepSubUnits[1]', '$logistID', '$blank', '$sepSubUnits[0]', '$sepSubUnits[5]', '$sepSubUnits[2]', '$sepSubUnits[4]', '$sepSubUnits[3]', '$singVol', '$sepSubUnits[6]', '$cur', '$profIns')";
            $queryE = $queryC.$queryD;
            if ($con->query($queryE) === TRUE) {
                $z++;
            }
        }
        if($z == $listLength){
            $list[] = [
                'status' => 'Yes',
            ];
        }else{
            $list[] = [
                'status' => 'Some Items Was not saved successfully...',
            ];
        }
        /************************************ EMAIL *****************************************/
        $m = mysqli_real_escape_string($con, $_POST['m']);
        $w = mysqli_real_escape_string($con, $_POST['n']);

        $to = $_SESSION['User_Email'].", backup@intellicargogroup.co.za, requestquote@intellicargogroup.co.za";
        $subject = $shipName;

        $message = '<html><head>';
        $message .= '<title>New Interface Quote: </title>';
        $message .= '<style>
                body{
                background: rgba(0,0,0,0);
                color: rgba(100,100,100,1);
                }
                </style>';
        $message .= '</head><body>';
        $message .= '<img src="https://www.intellicargoi.com/Images/Intellicargo Logo small.png"/>';
        $message .= '<br><p style="font-size: 18px;">Thank you '. $_SESSION['User_Name'] .'. We have recieved your request for a quote.</p><br>';
        $message .= '<p style="font-size: 15px">Ref No: '.$logistID.'</p>';
        $message .= '<p style="font-size: 15px;">Name: '.$_SESSION['User_Name'].'</p>';
        $message .= '<p style="font-size: 15px">Email: '.$_SESSION['User_Email'].'</p>';
        $message .= '<p style="font-size: 15px">Tel: '.$_SESSION['User_Contact'].'</p>';
        $message .= '<p style="font-size: 15px">From: '.$orAddr.'</p>';
        $message .= '<p style="font-size: 15px">To: '.$desAddr.'</p>';
        $message .= '<p style="font-size: 15px">Service Type: '.$servType.'</p>';
        $message .= '<p style="font-size: 15px">Incoterm: '.$incoterm.'</p>';
        $message .= '<p style="font-size: 15px">Item Quantity: '.$totItem.'</p>';
        $message .= '<p style="font-size: 15px">Total Volume: '.$totVol.' '.$m.'3</p>';
        $message .= '<p style="font-size: 15px">Total Weight: '.$totWeight.' '.$w.'</p>';
        $message .= '<p style="font-size: 15px">Total Value: '.$totVal.' '.$cur.'</p>';
        $message .= '<p style="font-size: 15px">Date Requested: '.$date.'</p><br><br>';
        $message .= '<p style="font-size: 15px;">Please note that some quotes may take up to 48 hours.</p><br><br>';
        $message .= '<p style="font-size: 18px;">Regards</p>';
        $message .= '<p style="font-size: 18px;">Intellicargo International</p>';
        $message .= "</body></html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
        $headers .= 'From: <noreply@intellicargoi.com>' . "\r\n";

        $mail_sent = @mail( $to, $subject, $message, $headers );
        $result = $mail_sent ? "Mail sent" : "Mail failed";

        $list[] = [
            'status' => 'Yes',
            //'mail' => $result
        ];
        /************************************ SMS *****************************************/
        //............................... New Quote SMS to client ................................
        if($_SESSION['setSmsNotif'] == 'yes'){
            require_once "../vendor/autoload.php";
            $basic  = new \Nexmo\Client\Credentials\Basic('e3f10198', 'AKsP9W932s05aKJg');
            $client = new \Nexmo\Client($basic);

            try {
                $message = $client->message()->send([
                    'to' => $_SESSION['setSmsNo'],
                    'from' => 'Intellicargo',
                    'text' => 'Your request has been recieved. You will be notified with any updates.'
                ]);
            } catch (Exception $e) {
                $message = $e->getEntity();
                //$response['status'] = "SMS failed:\r\n" . $message['error_text'];
            }
        }
        //.................................... New Quote SMS to Admin ................................
        require_once "../vendor/autoload.php";
        $basic  = new \Nexmo\Client\Credentials\Basic('e3f10198', 'AKsP9W932s05aKJg');
        $client = new \Nexmo\Client($basic);

        try {
            $message = $client->message()->send([
                'to' => '27603937729',
                'from' => 'Intellicargo',
                'text' => 'New Interface Quote Request Recieved.'
            ]);
        } catch (Exception $e) {
            $message = $e->getEntity();
            //$response['status'] = "SMS failed:\r\n" . $message['error_text'];
        }
    } else {
        $list[] = [
            'status' => mysqli_error($con),
        ];
    }
}

//... New Website Quote Request ...
if($action == 'webQuote'){
    $servTyp = mysqli_real_escape_string($con, $_POST['a']);
    $cur = mysqli_real_escape_string($con, $_POST['b']);
    $unit = mysqli_real_escape_string($con, $_POST['c']);
    $mail = mysqli_real_escape_string($con, $_POST['d']);
    $tel = mysqli_real_escape_string($con, $_POST['e']);
    $orA = mysqli_real_escape_string($con, $_POST['f']);
    $deA = mysqli_real_escape_string($con, $_POST['g']);
    $len = mysqli_real_escape_string($con, $_POST['h']);
    $wid = mysqli_real_escape_string($con, $_POST['i']);
    $hei = mysqli_real_escape_string($con, $_POST['j']);
    $wei = mysqli_real_escape_string($con, $_POST['k']);
    $qty = mysqli_real_escape_string($con, $_POST['l']);
    $val = mysqli_real_escape_string($con, $_POST['m']);
    $name = mysqli_real_escape_string($con, $_POST['n']);
    $m = '';
    $w = '';
    $tmpID = 0;
    $lastEntry = 0;
    $logistID = '0';
    $listType = 'Quote';
    $typeStat = 'Pending';
    $mesUnit = null;
    $length = 0;
    $reqFrom = 'Site';

    //... Calculate Total Volume & Weight & Value ...
    $tVol = ((double)$len * (double)$wid * (double)$hei * (double)$qty);
    $tWei = ((double)$wei * (double)$qty);
    $tVal = ((double)$val * (double)$qty);

    //... Get Last DBID By current user ...
    $queryLocate = "SELECT * FROM `logistics` WHERE User_Created = '$tmpID' ORDER BY DB_id";
    $resultLocate = $con->query($queryLocate);
    while($reading = mysqli_fetch_assoc($resultLocate)){
        if($reading['DB_id'] != null){
            $lastEntry = (int)$reading['DB_id'] + 1;
            $length++;
        }
    }

    //... Generate The new Logistic ID ...
    $tmpDate = date("Ymd");
    $logistID .= $tmpDate . ($length + 1);

    //... Description ...
    $shipName = 'WebQuote ' .($length + 1);

    //... Determine UOM ...
    $unit == 1 ? ($m = 'm' && $w = 'kg' && $mesUnit = 'Metric') : ($m = 'inch' && $w = 'lbs' && $mesUnit = 'Imperial');

    $queryA = "INSERT INTO `logistics` (Logistic_Id, List_Type, Type_Status, Logistic_Description, User_Created, Date_Created, Service_Type, From_Address, To_Address, Total_Volume, Total_Weight, Mesure_Unit, Item_Count, Quote_Amount, Quote_Cur, Request_Type) ";
    $queryB = "VALUES ('$logistID', '$listType', '$typeStat', '$shipName', '$tmpID', '$date', '$servTyp', '$orA', '$deA', '$tVol', '$tWei', '$mesUnit', '$qty', '$tVal', '$cur', '$reqFrom')";
    $query = $queryA.$queryB;

    if($con->query($query) === TRUE){

        $to = $mail.", backup@intellicargogroup.co.za, requestquote@intellicargogroup.co.za";
        $subject = $shipName;

        $message = '<html><head>';
        $message .= '<title>New Web Quote: </title>';
        $message .= '<style>
                body{
                background: rgba(0,0,0,0);
                color: rgba(100,100,100,1);
                }
                </style>';
        $message .= '</head><body>';
        $message .= '<img src="https://www.intellicargoi.com/Images/Intellicargo Logo small.png"/>';
        $message .= '<br><p style="font-size: 18px;">Thank you '. $name .'. We have recieved your request for a quote.</p><br>';
        $message .= '<p style="font-size: 15px">Ref No: '.$logistID.'</p>';
        $message .= '<p style="font-size: 15px;">Name: '.$name.'</p>';
        $message .= '<p style="font-size: 15px">Email: '.$mail.'</p>';
        $message .= '<p style="font-size: 15px">Tel: '.$tel.'</p>';
        $message .= '<p style="font-size: 15px">From: '.$orA.'</p>';
        $message .= '<p style="font-size: 15px">To: '.$deA.'</p>';
        $message .= '<p style="font-size: 15px">Service Type: '.$servTyp.'</p>';
        $message .= '<p style="font-size: 15px">Item Quantity: '.$qty.'</p>';
        $message .= '<p style="font-size: 15px">Total Volume: '.$tVol.' '.$m.'3</p>';
        $message .= '<p style="font-size: 15px">Total Weight: '.$tWei.' '.$w.'</p>';
        $message .= '<p style="font-size: 15px">Total Value: '.$val.' '.$cur.'</p>';
        $message .= '<p style="font-size: 15px">Date Requested: '.$date.'</p><br><br>';
        $message .= '<p style="font-size: 15px;">Please note that some quotes may take up to 48 hours.</p><br><br>';
        $message .= '<p style="font-size: 18px;">Regards</p>';
        $message .= '<p style="font-size: 18px;">Intellicargo International</p>';
        $message .= "</body></html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
        $headers .= 'From: <noreply@intellicargoi.com>' . "\r\n";

        $mail_sent = @mail( $to, $subject, $message, $headers );
        $result = $mail_sent ? "Mail sent" : "Mail failed";

        $list[] = [
            'status' => 'Yes',
            //'mail' => $result
        ];

        /************************************ SMS *****************************************/
        //... New Quote SMS to Admin ...
        require_once "../vendor/autoload.php";
        $basic  = new \Nexmo\Client\Credentials\Basic('e3f10198', 'AKsP9W932s05aKJg');
        $client = new \Nexmo\Client($basic);

        try {
            $message = $client->message()->send([
                'to' => '27603937729',
                'from' => 'Intellicargo',
                'text' => 'New Web Quote Request Recieved. ID: ' .$logistID
            ]);
        } catch (Exception $e) {
            $message = $e->getEntity();
            //$response['status'] = "SMS failed:\r\n" . $message['error_text'];
        }

    }
    else{
        $list[] = [
            'status' => mysqli_error($con),
        ];
    }

}

if($list == null){
    $list[] = [
        'status' => 'No',
    ];
}
if($_SESSION['dbStatus'] != ""){
    $list[] = [
        'status' => 'DB Problem',
    ];
}

echo json_encode($list);
