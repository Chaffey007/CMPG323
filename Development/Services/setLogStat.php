<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
session_start();
$json = file_get_contents('php://input');
$response = [];
$sel = $_POST['sel'];
$response['selectedProf'] = $_SESSION['selectedProf'] = $sel;
$response['showHome'] = $_SESSION['showHome'] = '1';

echo json_encode($response);


/*
 * //... If session variable not yet initialised ...
if(!isset($_SESSION['compProfile'])){
$_SESSION['compId'] = '';
}
else if($_SESSION['compProfile'] == 'Yes'){
$compId = $_SESSION['compId'];
$compProf = 'Yes';
}
*/
?>





