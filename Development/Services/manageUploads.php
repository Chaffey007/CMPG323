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

//................................................................. Get Upload Data .................................................................
if($action == "get"){
    $curUser = $_SESSION['User_Id'];
    $query = "SELECT * FROM `uploads` WHERE `user_id` = '$curUser'";
    //....... Get entries by date .........
    $resultGet = $con->query($query);
    if(mysqli_num_rows($resultGet) > 0){
        while($reading = mysqli_fetch_assoc($resultGet)){
            if($reading['upload_id'] != null){
                $list[] = [
                    'status' => 'Yes',
                    'dbId' => $reading['upload_id'],
                    'user' => $reading['user_id'],
                    'uplDate' => $reading['upload_date'],
                    'fileType' => $reading['upload_file_type'],
                    'location' => $reading['upload_geolocation'],
                    'tags' => $reading['upload_tags'],
                    'captDate' => $reading['upload_capture_date'],
                    'captBy' => $reading['upload_capture_by'],
                    'ttl' => $reading['upload_title'],
                    'descr' => $reading['uploadDescript'],
                    'shr' => $reading['upload_is_shared'],
                    'shrWith' => $reading['upload_shared_with'],
                    'fileName' => $reading['upload_filename']
                ];
            }
        }
    }else{
        $list[] = [
            'status' => 'No - Empty Uploads List'
        ];
    }
}

//................................................................. Add New Upload .................................................................
if($action == "add"){
    $shopName = mysqli_real_escape_string($con, $_POST['name']);
    $shopBranch = mysqli_real_escape_string($con, $_POST['branch']);
    $shopClass = mysqli_real_escape_string($con, $_POST['class']);
    $queryC = "INSERT INTO `uploads` (shop_name, shop_class, shop_branch) ";
    $queryD = "VALUES ('$shopName', '$shopClass', '$shopBranch')";
    $queryE = $queryC.$queryD;

    if($con->query($queryE) === TRUE){
        $list[] = [
            'status' => 'Yes - Shop Insert Success for ' .$shopName,
        ];
    }else{
        $list[] = [
            'status' => 'No - Shop Insert Failed for ' .$shopName. " => " .mysqli_error($con),
        ];
    }
}

//................................................................. Delete Upload .................................................................
if($action == "delete"){
    $uplID = mysqli_real_escape_string($con, $_POST['id']);

    $queryRemove = "DELETE FROM `uploads` WHERE `upload_id` = '$uplID' ";
    if($con->query($queryRemove) === TRUE){
        $list[] = [
            'status' => 'Yes - Delete Success for ID: ' .$uplID,
        ];
    }else{
        $list[] = [
            'status' => 'No - Delete Failed for ID: ' .$uplID. " => " .mysqli_error($con),
        ];
    }
}
//................................................................. Edit Upload Data .................................................................
if($action == 'edit'){
    $data = mysqli_real_escape_string($con, $_POST['data']);

    //... Seperate Data ...
    $sepSubUnits = null;
    if((strpos($data, ",") !== false)){
        $sepSubUnits = explode(',', $data);
        $length = count($sepSubUnits);
    }

    //... Update ...
    $queryUpdateShop = "UPDATE `shops` SET `shop_class` = '$sepSubUnits[1]', `shop_name` = '$sepSubUnits[2]', `shop_branch` = '$sepSubUnits[3]', `shop_tel` = '$sepSubUnits[4]', `shop_address` = '$sepSubUnits[5]', `shop_mail` = '$sepSubUnits[6]', `shop_www` = '$sepSubUnits[7]', `parent_comp` = '$sepSubUnits[8]', `shop_reg_no` = '$sepSubUnits[9]' WHERE `shop_id` = '$sepSubUnits[0]'";
    if($con->query($queryUpdateShop) === TRUE){
        $list[] = [
            'status' => 'Yes - Shop Updated for '.$data,
        ];
    }
    else{
        $list[] = [
            'status' => 'No - Shop Updated for '.$sepSubUnits[2].' failed => ' .mysqli_error($con),
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