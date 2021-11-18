<?php
require_once 'DBConnection.php';

$profile = $_POST['prof'];
$userid = $_SESSION['User_Id'];
$date = date("Y-m-d H:i:s");

//... If Shop Image ...
if(strpos($profile, 'Upl') !== false){
    //... Directory ...
    define ("FILEREPOSITORY","../");
    $folder = 'Uploads';
    if (!is_dir(FILEREPOSITORY.$folder)) {
        mkdir(FILEREPOSITORY.$folder);
    }
    $tmpArray = explode('-', $profile);
    $picID = $tmpArray[1];
    $stat = 'yes';

    //... Upload File ...
    if(count($_FILES) > 0){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){

            $query = "SELECT * FROM `uploads` WHERE `user_id` = '$userid'";
            $resultGet = $con->query($query);
            $uplCount = mysqli_num_rows($resultGet);
            $uplCount++;
            $tmpFilename = $userid."_".$uplCount.".jpg";

            $result = move_uploaded_file($_FILES['file']['tmp_name'], FILEREPOSITORY.$folder."/".$tmpFilename);
            if ($result == 1){

                $queryC = "INSERT INTO `uploads` (user_id, upload_date, upload_filename, upload_file_type) ";
                $queryD = "VALUES ('$userid', '$date', '$tmpFilename', '.jpg')";
                $queryE = $queryC.$queryD;

                if($con->query($queryE) === TRUE){
                    echo 'Image Upload Successful!';
                }else{
                    echo 'Image Upload Unuccessful! => ' .mysqli_error($con);
                }
            }
            else{
                echo $_FILES['file']['error'];
                //echo "There was a problem uploading the Image.";
            }
        }
    }else{
        echo $_FILES['file']['error'];
        //echo 'Upload Fail. Please Try again.';
    }
}
/*else{
    //... Directory ...
    define ("FILEREPOSITORY","../");
    $folder = 'Prof_Pics';
    if (! is_dir(FILEREPOSITORY.$folder)) {
        mkdir(FILEREPOSITORY.$folder);
    }

//... If user has company profile ...
    $uID = $_SESSION['User_Id'];
    $compId = $compProf = '';
    $uid = $uID;
    if($profile == "Company Profile"){
        $compId = $_SESSION['compId'];
        $compProf = $_SESSION['compName'];
        $uid = $compId;
    }
//... Misc Vars ...
    $date = date("Y-m-d H:i:s");
    $stat = 'true';

//... Upload File ...
    if(count($_FILES) > 0){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            $result = move_uploaded_file($_FILES['file']['tmp_name'], FILEREPOSITORY.$folder."/"."$uid.jpg");
            if ($result == 1){
                $query = "UPDATE `user` SET `Profile_Pic` = '$stat' WHERE `User_Id` = '$uid'";
                if ($con->query($query) === TRUE) {
                    echo 'Image Upload Successful!';
                } else {
                    echo 'Image Upload Unuccessful!';
                }
            }
            else{
                echo $_FILES['file']['error'];
                //echo "There was a problem uploading the File.";
            }
        }
    }else{
        echo $_FILES['file']['error'];
        //echo 'Upload Fail. Please Try again.';
    }
}*/

