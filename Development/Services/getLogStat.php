<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
session_start();
$json = file_get_contents('php://input');
$response = [];
if(!isset($_SESSION['showHome'])){
    $_SESSION['showHome'] = '';
}
if(!isset($_SESSION['selectedProf'])){
    $_SESSION['selectedProf'] = null;
}
$response['showHome'] = $_SESSION['showHome'];
$response['selectedProf'] = $_SESSION['selectedProf'];

echo json_encode($response);
?>