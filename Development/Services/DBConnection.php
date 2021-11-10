<?php
session_start();
//*********************************DB Login Credentials*************************************
$DBusername = "root";
$DBpassword = "35264";
$DBhostname = "localhost";
$dbname = "cmpg323";
$_SESSION['dbStatus'] = "";

//..........connection to the database..........
function dbConnect() {
    global $DBhostname, $DBpassword, $DBusername, $con;
    $con = $dbhandle = mysqli_connect($DBhostname, $DBusername, $DBpassword);
    if (!$dbhandle) {
        throw new Exception('<font color="white">Connection Failed!!</font>');
    }
}
try{
    dbConnect();
    //echo '<font color="white">Connection Successful!!</font></br>';
}
catch (Exception $e)
{
    //echo '<font color="white">ERROR...</font> ', $e->getMessage();
    //echo "</br>";
    $_SESSION['dbStatus'] = $e;
}

//..........select a database to work with..........
function dbSelect() {
    global $con, $dbname;
    $selected = mysqli_select_db($con, $dbname);
    if(!$selected) {
        throw new Exception('<font color="white">Could not select '.$dbname.'</font>');
    }
}
try{
    dbSelect();
    //echo '<font color="white">'.$dbname.' Selected Successfully</font></br>';
}
catch(Exception $e1){
    //echo '<font color="white">ERROR...</font> ', $e1->getMessage();
}
?>