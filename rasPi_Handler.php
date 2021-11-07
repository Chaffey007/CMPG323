<?php
//date_default_timezone_set('Africa/Johannesburg');

require_once '../portal/libs/Session.php';
require_once '../portal/config.php';
require_once '../portal/libs/Database.php';
require_once '../portal/libs/Email.php';
require_once '../portal/libs/Log.php';
require_once '../portal/util/User.php';
require_once '../portal/util/Common.php';
require_once '../portal/libs/Vehicle.php';
require_once '../portal/libs/RFID.php';


//require_once '../rfid-recovery/mtn_sms_class.php';

class rasPi_Handler{

    private $db;
    private $common, $user;
    private $date;
    private $params;
    private $log;
    private $key;
    public $minSoftVer, $curSoftVer, $updEcxeptFold, $updEcxeptFile, $deviceType, $alwaysAllow;


    function __construct()
    {
        $this->common = new Common();
        $this->common->setTimeZone();
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->user = new User($this->db);
        $this->date = new DateTime();
        $this->date = $this->date->format('Y-m-d H:i:s');
        $this->params = array();
        $this->log = new Log();
        $this->key = 'qJB0rGtIn8UB1xG03efyCp';
        $this->minSoftVer = "3.0.0";                                // Minimum software version required by server API.
        $this->curSoftVer = $this->minSoftVer;                      // Current software version for update. (Will differ from min ver in future implementations)
        $this->updEcxeptFold = array("Sounds");                     // Folders to exclude during update.
        $this->updEcxeptFile = array("config.txt");   // Files to exclude during update. (Always include file extension) - Always include config.txt in this list, otherwise all devices that update, will be reset to factory default settings. - If file is in a subfolder, include subfolder in array e.g. GoBrowser/Interact.py
        $this->deviceType = "COMMANDCop";
        $this->alwaysAllow = array('1','3','4','6','7','9','10','13','14','15','18');
    }

    /**
     * @param $par
     */
    function setPars($par){
        $this->params = $par;
    }


    /**
     * Auto login for Raspberry PI.
     * Using my own implimentation as the login model requires a request for un and pass...
     * Also, md5 might cause login problems with login model.
     * @param $un
     * @param $pw
     * @param $enc
     * @return mixed|string
     */
    public function xhrLogin($un, $pw, $enc) {
        $rsp = new stdClass();
        $rsp->changePass = 0;

        if (($un != '') && ($pw != '')) {
            /*if($enc == "own"){
                $pw = md5($pw);
            }*/

            $data = $this->db->select('SELECT * FROM users WHERE login = :login AND passwd = :password', array('login' => $un, 'password' => $pw));
            $count = count($data);
            if ($count > 0) {
                $main_id = $data[0]["mainuserid"];
                $main_data = $this->db->select('SELECT * FROM users WHERE user_id = :uid', array('uid' => $main_id));
                $acc_suspend = $main_data[0]["suspended_billing"] == 1 ? true : false;
                if($acc_suspend){
                    $rsp->error = 1;
                    $rsp->errorDescription = "This account has been suspended due to non payment.";
                }else{
                    //Found a user, set up the session
                    Session::init();
                    $changepass= 0;
                    Session::set('changePass', false);
                    $db_row = $data[0];

                    $user_id = $db_row["user_id"];
                    $fname = $db_row["firstname"];
                    $lname = $db_row["lastname"];
                    $type = $db_row["type"];
                    $osmtileserver = $db_row["osm_tileserver"];
                    $mainuserid = $db_row["mainuserid"];
                    $companyid = $db_row["companyid"];

                    Session::set('SESS_MEMBER_ID', $user_id);
                    Session::set('SESS_ADMIN_ID', 0);
                    Session::set('SESS_FIRST_NAME', $fname);
                    Session::set('SESS_LAST_NAME', $lname);
                    Session::set('OSM_TILE_SERVER', $osmtileserver);
                    Session::set('SESS_MEMBER_MAINUSERID', $mainuserid);
                    Session::set('TYPE', $type);
                    Session::set('companyid', $companyid);
                    Session::set('loggedIn', true);
                    Session::set('device', $this->deviceType);

                    //$bb = new BillingBalances();
                    $rsp = new stdClass();
                    $branding = $db_row['branding'];

                    //Only check for EWC branded clients
                    /*if ($branding == 0)
                        $bb->getClientBalances($db_row['billing_client_id'], $rsp);
    */
                    if ($type == 9) {
                        $_SESSION['SESS_ADMIN_ID']=$user_id;
                    }
                    $this->log->insert(1);
                    $host = $this->common->getHostName();
                    $this->log->activity("LOGN","",$host);

                    $rsp->error  = 0;
                    $rsp->changepass = $changepass;
                    $rsp->type = $type;
                }
            } else {
                $rsp->error = 1;
                $rsp->errorDescription = "Login was unsuccessful. Please validate your user credentials";
            }
        }
        else {
            $rsp->error = 1;
            $rsp->errorDescription = "Login was unsuccessful. Please validate your user credentials";
        }
        return json_encode($rsp);
    }

    /**
     * Get User Data using login credentials.
     * @param $un
     * @param $pw
     * @return mixed
     */
    public function getUserData($un, $pw, $enc) {
        $rsp = new stdClass();
        /*if($enc == 'own'){
            $pw = md5($pw);
        }*/
        $data = $this->db->select('SELECT * FROM users WHERE login = :login AND passwd = :password', array('login' => $un, 'password' => $pw));
        return $data;
    }


    /**
     * Redirect according to selected function.
     * @param $func
     * @param $aLogin (Check if the device should login automatically)
     * @return string
     */
    function setLaunchURL($func,$aLogin){
        if($aLogin == "0"){
            return '../portal/login';
        }else if($func == 'zabbix'){
            return 'http://zabbix.ewcop.com/zabbix';
        }else{
            //... If Administrator, dont login automatically. Just go to Login Page ...
            if (Session::get("TYPE") == 9) {
                if($func == 'hotspot'){
                    return '../portal/admin/action/show/hotspot';               //.. For Hotspots ...
                }else{
                    return '../portal/login';
                }
            } else if(Session::get("TYPE")==1) {
                $mainuserid = Session::get('SESS_MEMBER_MAINUSERID');
                $routenewlogin = $this->db->select("SELECT rfid_new_interface FROM users WHERE user_id=:mainuserid", array("mainuserid" => $mainuserid));//check if main user is allowed to route to new interface
                if ($routenewlogin[0]['rfid_new_interface'] == 1) {//main user is allowed to route to new interface
                   return '../portal/securityservices';//route this rfuser to new interface
                } else {
                   return '../user-rfid-dashboard.php';//route this rfdashuser to old interface
                }
            } else {
                if($func == 'alarms'){
                    return '../portal/securityservices';                      //.. For new RFID Console ...
                    //return '../user-rfid-dashboard.php?portal';                 //.. For old RFID Console ...
                }else if($func == 'obbook'){
                    return '../portal/rfobook?menu';                          //.. For new RFID Console ...
                    //return '../portal/obbook';                                  //.. For old RFID Console ...
                }else if($func == 'garmin'){
                    return '../portal';
                }else{
                    return '../portal/login';
                }
            }
        }
    }

    /**
     * Set the URL to check for alarms.
     * Main page and OBBOOK uses different functions.
     * @param $func
     * @return string
     */
    function setPollURL($func){
        if($func == 'alarms'){
            return '/RASP_PI_API/raspberrypi-alarm.php';
        }else if($func == 'obbook'){
            return '/RASP_PI_API/raspberrypi-obbook.php';
        }else if($func == 'garmin'){
            return 'None';
        }else{
            return 'None';
        }
    }


    /**
     * Generate email containing important info from RasPi.
     * @param $reason
     * @param $extra
     * @param $data
     * @return array|stdClass
     */
    function sendMail($reason, $extra, $data) {
        $email = null;
        $body = '';
        $uID = '';

        //... Device Details ...
        $dets = '<br><br>Device Details';
        $tmpU = $this->checkUsage($data['mac']);
        if(count($tmpU) > 0){
            $isSet = true;

            if(($tmpU[0]['pi_user'] != "") && ($tmpU[0]['pi_user'] != null)){
                $tUserData = $this->getUserData($tmpU[0]['pi_user'],$tmpU[0]['pi_pass'],$tmpU[0]['enc']);
                if(count($tUserData) > 0){
                    $uID = $tUserData[0]['user_id'];
                }
            }

            //Client Details
            if($tmpU[0]['pi_client'] != null){
                $usernameMain = $tmpU[0]['pi_client'];
                $dets .= '<br>Main User: '.$usernameMain;
            }
            $descr = '';
            //Device Description
            if($tmpU[0]['pi_descript'] != null){
                $descr .= $tmpU[0]['pi_descript'];
            }
            if($tmpU[0]['pi_comments'] != null){
                if($descr != ""){
                    $descr .= " - ".$tmpU[0]['pi_comments'];
                }else{
                    $descr .= $tmpU[0]['pi_comments'];
                }
            }
            $dets .= '<br>Device Description: '.$descr;
            $dets .= '<br>Function: '.strtoupper($tmpU[0]['pi_function']);  //Device Function
        }else{
            $isSet = false;
        }
        $dets .= '<br>MAC Address: '.$data['mac'];  //Device MAC Address

        //Welcome email for registration of new device. (Currently not used)
        if($reason == 'Registration'){
            if($isSet){
                $body .= '<br>New Raspberry Pi registered in server.\n';
                $body .= '<br>Registration Date: '.$this->date.'';
                $body .= 'The current status is `Static`.<br> You can manage this PI @ track2.traxerver.net/portal/admin/action/manage/raspi';
                $body .= $dets;
                $body .= $extra;

            }
        }
        //Email alert for not responding to an alarm in time.
        else if($reason == 'Alarm Response'){
            $this->logAlarmIgnored($dets,$extra,$data);
            if($isSet){
                $body .= 'User not responding to alarm!';
                $body .= '<BR>Date: '.$this->date;
                $body .= $dets;
                $body .= $extra;
                $email = $this->send_mail_alarm($uID,$body,$data,$tmpU[0]);
            }
        }
        //Email alert for device offline.
        else if($reason == 'Device Offline'){
            if($isSet){
                //$body .= 'CommandCop Offline!';
                $body .= '<BR>Date: '.$this->date;
                $body .= $dets;
                $body .= $extra;
                $email = $this->send_mail_offline($uID,$body,$data,$tmpU[0]);
            }
        }
        return $email;
    }

    /**
     * Email for when user not responds in time.
     * @param $userid
     * @param $details
     * @param $data
     * @param $piData
     * @return stdClass
     */
    function send_mail_alarm($userid,$details,$data,$piData){
        $rsp = new stdClass();
        $mailData = $this->genMailData($userid,$piData,"alarm");
        include "../portal/public/css/style/$mailData->varsfile";

        $body = "Please Note<BR><BR>";
        $tmpRespTme = (int)$piData['alarm_response_interv'] + (int)$piData['alarm_response_msg_interv'];
        $body .= "An alarm has not been responded to after ".$tmpRespTme." seconds.<BR><BR>";
        $body .= "This email was sent to $mailData->mailList" . "<BR><BR>";
        $body .= $details;

        $template = file_get_contents("../portal/views/email/raspi_template.php");
        $template = str_replace("__REPORT_NAME__", 'Raspberry Pi Alarm', $template);
        $template = str_replace("__REPORT_STATUS__", 'User not responding to an alarm', $template);
        $template = str_replace("__USER_NAME__", "Administrator", $template);
        $template = str_replace("__REPORT_DETAILS__", $body, $template);
        $template = str_replace("__LOGO_URL__", "http://".$mailData->host."/portal/public/images/logos/".$header_image_left, $template);
        $template = str_replace("__HEADER_COLOR__", $header_left_color, $template);
        $template = str_replace("__THEME_COLOR__", $main_bg_color, $template);
        $template = str_replace("__REGARDS__", $mailData->regards, $template);
        $template = str_replace("__SUPPORT_CONTACT__", $mailData->contact, $template);

        if($mailData->mailList != ""){
            $rsp = $this->doSendMail('Alarm Response Time',$template,0,$mailData,$data);
        }else{
            $rsp->error = 1;
            $rsp->errorDescription = "No email addresses listed on this device.";
        }
        $rsp->mailto = $mailData->mailList;

        return $rsp;
    }

    /**
     * Email for when device is offline.
     * @param $userid
     * @param $details
     * @param $data
     * @param $piData
     * @return stdClass
     */
    function send_mail_offline($userid,$details,$data,$piData){
        $rsp = new stdClass();
        $mailData = $this->genMailData($userid,$piData,"offline");
        include "../portal/public/css/style/$mailData->varsfile";

        $body = "Please Note<BR>";
        $body .= "CommandCop is offline.<BR>See device details below.<BR>";
        $body .= $details;
        $body .= "<BR>This email was sent to $mailData->mailList" . "<BR><BR>";

        $template = file_get_contents("../portal/views/email/raspi_template.php");
        $template = str_replace("__REPORT_NAME__", 'CommandCop Offline', $template);
        $template = str_replace("__REPORT_STATUS__", 'One of the CommandCops stopped communicating with the server.', $template);
        $template = str_replace("__USER_NAME__", "Administrator", $template);
        $template = str_replace("__REPORT_DETAILS__", $body, $template);
        $template = str_replace("__LOGO_URL__", "http://".$mailData->host."/portal/public/images/logos/".$header_image_left, $template);
        $template = str_replace("__HEADER_COLOR__", $header_left_color, $template);
        $template = str_replace("__THEME_COLOR__", $main_bg_color, $template);
        $template = str_replace("__REGARDS__", $mailData->regards, $template);
        $template = str_replace("__SUPPORT_CONTACT__", $mailData->contact, $template);

        if($mailData->mailList != ""){
            $rsp = $this->doSendMail('CommandCop Offline',$template,0,$mailData,$data);
            $mailData->mailList = "fred.chaffey@ewcop.com,chris2.viljoen@ewcop.com";
            $rsp2 = $this->doSendMail('CommandCop Offline',$template,0,$mailData,$data);
        }else{
            $rsp->error = 1;
            $rsp->errorDescription = "No email addresses listed on this device.";
        }
        $rsp->mailto = $mailData->mailList;

        return $rsp;
    }

    /**
     * Generate universal data to be used in emails.
     * @param $userid
     * @param $piData
     * @param $act
     * @return stdClass
     */
    function genMailData($userid,$piData,$act="alarm"){
        $rsp = new stdClass();
        $u = new User();

        if($userid == ""){
            $userid = "1008";
        }
        $user = $u->getByUserID($userid);
        $rsp->contact = $this->common->getBrandedEmailContact($userid);
        $rsp->regards = $this->common->getBrandedEmailRegards($userid);
        $rsp->email_src = $this->common->getBrandedEmailSource($userid);

        if($act == "alarm"){
            $rsp->mailList = $this->getDeviceMailList($piData,$act);
        }else if($act == "offline"){
            $rsp->mailList = $this->getDeviceMailList($piData,$act,$userid);
        }

        $user = $user[0];
        $username = $user['firstname'] . " " . $user['lastname'];
        $rsp->host = $this->common->getBrandedURL($userid);
        $rsp->varsfile = $rsp->host. "_vars.php";

        return $rsp;
    }

    /**
     * Execute send mail.
     * @param $title
     * @param $template
     * @param $attachments
     * @param $mailData
     * @param $data
     * @return stdClass
     */
    function doSendMail($title,$template,$attachments,$mailData,$data){
        $rsp = new stdClass();
        $email = new Email();
        $result = $email->sendEmail("$mailData->email_src@traxerver.net", $mailData->mailList, $title, $template, $attachments);
        if ($result === false) {
            $rsp->error = 1;
            $rsp->errorDescription = "Email could not be sent";
        } else {
            $tDate = new DateTime();
            $tDate = $tDate->format('Y-m-d H:i:s');
            $tMac = $data['mac'];
            $this->db->update("raspi", array("last_email" => $tDate), "pi_mac='$tMac'");
            $rsp->error = 0;
        }
        return $rsp;
    }

    /**
     * Get the email addresses listed to send to.
     * If $uID != null, email addresses wil be selected from the 'users' table. Otherwise, from raspi table.
     * The difference in email addresses is each device can have emails sent to different addresse for minor events
     * and then emails can be sent to the same address for upper level events.
     * @param $data
     * @param $act
     * @param $uID
     * @return string
     */
    function getDeviceMailList($data, $act, $uID=null){
        if($act == "offline" && $uID != null){
            // Email addresses for main user...
            $user_email = $this->user->getUserCommandcopEmail($uID);
            // Email addresses for rf- & sub users
            $subusers = $this->db->select("SELECT * FROM users WHERE type IN ('1','2') AND mainuserid=:id",array("id" => $uID));
            foreach($subusers as $sUser){
                $ccop_mails = $sUser['commandcop_email'];
                if(strpos($ccop_mails, ',') != false){
                    $tmpMails = explode(",",$ccop_mails);
                    foreach($tmpMails as $email){
                        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
                            $user_email .= ",".$email;
                        }
                    }
                }else{
                    if(filter_var($ccop_mails,FILTER_VALIDATE_EMAIL)){
                        $user_email .= ",".$ccop_mails;
                    }
                }
            }
        }else{
            if(!empty($data)){
                if(($data['alarm_mail_contacts'] != null) && ($data['alarm_mail_contacts'] != "")){
                    $user_email = $data['alarm_mail_contacts'];
                }else{
                    $user_email = '';
                }
            }else{
                $user_email = "";
            }
        }

        return $user_email;
    }

    /**
     * Log Alarm not responded in time.
     * @param $dets
     * @param $extra
     * @param $data
     * @return bool
     */
    function logAlarmIgnored($dets,$extra,$data){
        $res = $this->checkUsage($data['mac']);
        if($this->addHighEvent($res[0]['debug_mode'],"12")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $data['mac'],
                "user_id" => $res[0]['login_id'],
                "server_datetime" => $this->date,
                "device_datetime" => $data['devTim'],
                "type" => "12",
                "dat" => $dets."<br>".$extra
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Get Data if pi exists.
     * @param $mac
     * @return mixed
     */
    function checkUsage($mac){
        $res = $this->db->select("SELECT * FROM raspi WHERE pi_mac='$mac'");
        /*if(count($res) > 0){
            if($res[0]['enc'] == "own"){
                $res[0]['pi_pass'] = $this->decryptPW($res[0]['pi_pass']);
            }
        }*/
        return $res;
    }

    /**
     * Check current status af the device.
     * @param $arr
     * @return mixed
     */
    function checkActive($arr){
        $this->setPars($arr);
        $mac = $arr['mac'];
        $func = $arr['func'];
        $desc = $arr['desc'];
        $res = $this->db->select("SELECT pi_usage FROM raspi WHERE pi_mac='$mac' LIMIT 1");
        return $res;
    }

    /**
     * Check if user exists.
     * @param $un
     * @param $pw
     * @param $enc
     * @return mixed
     */
    function checkUser($un, $pw, $enc){
        /*if($enc == "own"){
            $pw = md5($pw);
        }*/
        $cnt = $this->db->select('SELECT * FROM users WHERE login = :login AND passwd = :password', array('login' => $un, 'password' => $pw));
        if(count($cnt) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Log last Healthcheck in DB.
     * @param $mac
     * @param $tRes
     * @param $tDat
     * @param $tDate
     * @param $devTim
     * @param $alCount
     * @param $data
     * @return bool
     */
    function logHealthCheck($mac, $tRes, $tDat, $tDate, $devTim, $alCount, $data){
        $cnt = 0;
        if(strpos($alCount,';') !== false){
            $arr = explode(";", $alCount);
            if($arr[0] != "-"){
                $cnt = $arr[0];
            }
        }
        if($tRes == "-" || $tDat == "-" || $tDate == "-"){
            $upd = $this->db->update("raspi", array(
                "last_healthcheck" => $this->date,
                "last_healthcheck_alarm_count" => $cnt
            ), "pi_mac='$mac'");
        }else{
            $upd = $this->db->update("raspi", array(
                "last_healthcheck" => $this->date,
                "test_date" => $tDate,
                "test_result" => $tRes,
                "test_data" => $tDat,
                "last_healthcheck_alarm_count" => $cnt
            ), "pi_mac='$mac'");
        }
        if($upd){
            //... Log on server ...
            $this->create_log($mac,$devTim,$alCount,$data);
            return true;
        }else{
            return false;
        }
    }

    /**
     * Log each healthcheck and number of alarms received since last healthcheck.
     * @param $macaddress
     * @param $deviceTime
     * @param $alarmCount
     * @param $data
     */
    function create_log($macaddress,$deviceTime,$alarmCount,$data){
        $macaddress = str_replace(':','',$macaddress);//strip the : from the mac address
        $table_name = 't_tracklogid_'.$macaddress;
        $tim = date("His",strtotime($deviceTime)).".000V";
        $date = substr($deviceTime,0 ,10);
        $fullDate = date("Y-m-d H:i:s",strtotime($deviceTime));

        //check log
        $doLog = $this->check_log($macaddress,$alarmCount);
        if($doLog){
            //create log
            try {
                $this->db->insert($table_name, array(
                    "id" => $macaddress,
                    "typ" => 802,
                    "dat" => $alarmCount,
                    "server_datetime" => $this->common->getDateTime(),
                    "date" => $date,
                    "tim" => $tim
                ));
            }catch(Exception $e){
                //because the query could not insert that probably means that the table does not exist so we create it.
                $this->db->query("create table if not exists $table_name like t_tracklog_schema");
            }
        }
    }

    /**
     * Only record the healthlog if last log was more than 1mins ago or
     * if the last log's alarm data differs from the new data.
     * @param $macaddress
     * @param $alarmCount
     * @return bool
     */
    function check_log($macaddress,$alarmCount){
        $dolog = false;
        $table_name = $this->common->getDeviceTable($macaddress);
        $res = $this->db->select("SELECT * FROM $table_name WHERE typ='802' ORDER BY server_datetime DESC LIMIT 1");
        if(count($res) > 0){
            $entry = $res[0];
            $timediff = $this->common->timeDiffSecs($entry['server_datetime'],$this->common->getDateTime());
            if($timediff >= 60){
                $dolog = true;
            }else{
                if($alarmCount != $entry['dat']){
                    $dolog = true;
                }else{
                    $dolog = false;
                }
            }
        }else{
            $dolog = true;
        }
        return $dolog;
    }

    /**
     * Change config status in DB.
     * @param $mac
     * @param $softV
     * @param $stat
     * @return bool
     */
    function logUpdate($mac,$softV,$stat){
        return $this->db->update("raspi", array(
            "config_stat" => $stat,
            "pi_softw_ver" => $softV
        ), "pi_mac='$mac'");
    }

    /**
     * Update Action Status on admin portal.
     * @param $data
     * @param $curStat
     */
    function setLogUpdateMsg($data, $curStat){
        $pingStat = $this->getPingStatus($data, $curStat);
        $msg = "None";
        $msgPost = "";
        if($data['forceUpdate'] == "Yes"){
            $msg = "Forcing Software Update";
        }else if($data['softStat'] == "Error"){
            $msg = "Updating Software";
        }else if($data['forceReboot'] == "Yes"){
            if($data['status'] == 1){
                $msg = "Forcing Device Reboot";
            }else if($data['status'] == 2){
                $msg = "Updating Config & Reboot";
            }
        }else if($data['forceLog'] == "Yes"){
            $msg = "Requesting Log";
        }else if($data['forceConfig'] == "Yes"){
            $msg = "Requesting Config";
        }else if($pingStat[0] == true ){
            $msg = $pingStat[1];
        }else if($data['status'] == 2){
            $msg = "Updating Config";
        }else if(($data['status'] == 1) && ($data['frceReboot'] != "True")){
            $msg = "Good";
        }else{
            $msg = $curStat;
        }
        $ret = $this->logUpdate($data['mac'],$data['softVer'],$msg);
    }

    /**
     * Get current status of ping message.
     * @param $data
     * @param $curStat
     * @return array
     */
    function getPingStatus($data, $curStat){
        $resp = array(false, "");
        if($data['reqResp'] == "Yes"){
            $resp[0] = true;
            $resp[1] = "Message Sent. Pending Response";
        }else if($curStat == "Message Sent. Pending Response"){
            $resp[0] = true;
            $resp[1] = "Message Sent. Pending Response";
        }else if($curStat == "Response Received"){
            $resp[0] = true;
            $resp[1] = "Response Received";
        }
        return $resp;
    }

    /**
     * Get the admin requested message to display on the device.
     * @param $mac
     * @return array
     */
    function getReqMsg($mac){
        $resp = array();
        $res = $this->db->select("SELECT * FROM raspi_high_events WHERE mac_address='$mac' AND type='9' ORDER BY dbid DESC LIMIT 1");
        if(count($res) > 0){
            $resDat = $res[0];
            $formDat = $this->formatTypeData("9",$resDat['dat']);
            $resp[0] = "Admin user (".$formDat->user.") requires you to respond to this message.";
            if($formDat->msg != ""){
                $resp[0] .= "\n\nMessage: \n".$formDat->msg;
            }
            $resp[1] = $resDat['dbid'];
        }
        return $resp;
    }

    /**
     * Log the operator's response to the request.
     * @param $msg
     * @param $data
     * @return bool
     */
    function setReqResp($msg, $data){
        $res = $this->checkUsage($data['mac']);
        if($this->addHighEvent($res[0]['debug_mode'],"10")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $data['mac'],
                "user_id" => $res[0]['login_id'],
                "server_datetime" => $this->date,
                "device_datetime" => $data['devTim'],
                "type" => "10",
                "dat" => $msg
            ));
            if($new){
                return $this->logUpdate($data['mac'],$data['softVer'],"Response Received");
            }else{
                return null;
            }
        }else{
            return $this->logUpdate($data['mac'],$data['softVer'],"Response Received");
        }
    }

    /**
     * Check software requirement.
     * @param $ver
     * @return string
     */
    function checkSoftVer($ver){
        // Bypass ability to automatically detect a new software version.
        return "1";
        /*$minVerSep = null;
        $curVerSep = null;
        if (strpos($this->minSoftVer, '.') !== false) {
            $minVerSep = explode('.',$this->minSoftVer);
        }
        if (strpos($ver, '.') !== false) {
            $curVerSep = explode('.',$ver);
        }
        if(((int)$minVerSep[0] <= (int)$curVerSep[0]) && ((int)$minVerSep[1] <= (int)$curVerSep[1]) && ((int)$minVerSep[2] <= (int)$curVerSep[2])){
            return "1";
        }else if(((int)$minVerSep[0] <= (int)$curVerSep[0]) && ((int)$minVerSep[1] <= (int)$curVerSep[1]) && ((int)$minVerSep[2] > (int)$curVerSep[2])){
            return "0";
        }else if(((int)$minVerSep[0] <= (int)$curVerSep[0]) && ((int)$minVerSep[1] > (int)$curVerSep[1])){
            return "0";
        }else{
            return "0";
        }*/
    }

    /**
     * Check if device has minimum software version installed.
     * @param $thisData
     * @return array
     */
    function checkSoftReq($thisData){
        $ver = $thisData['softVer'];
        $force = $thisData['forceUpdate'];
        if(($this->checkSoftVer($ver) == "1") && ($force != "Yes")){
            $ret = array("Good", $ver, "", "", array());
        }else{
            $ret = array("Error", $this->curSoftVer, $this->updEcxeptFold, $this->updEcxeptFile,$this->getUpdContent());
            if($force != "Yes"){
                $this->logSoftUpd($thisData);
            }
        }
        return $ret;
    }

    /**
     * Get all data required for PI's software update.
     * @return array
     */
    function getUpdContent(){
        $dir = '../RASP_PI_API/Version';
        //$dir = '../RASP_PI_API';
        $fileList = array();
        $foldList = array();
        $sFoldList = array();
        $fileCont = array();
        $fileCount = 0;
        $foldCount = 0;
        $sFoldCount = 0;
        if(is_dir($dir)){
            //... Main Directory ...
            foreach(glob($dir.'/*') as $file){
                $nme = substr(strrchr($file,'/'),1);
                if(strpos($nme,'.') !== FALSE){
                    if(!in_array($nme, $this->updEcxeptFile)){
                        $fileList[$fileCount] = $nme;
                        //$fileCont[$fileCount] = file_get_contents($file);
                        $fileCount++;
                    }
                }else{
                    if(!in_array($nme, $this->updEcxeptFold)){
                        $foldList[$foldCount] = $nme;
                        $foldCount++;
                    }
                }
            }
            //... Secondary Directories ...
            foreach($foldList as $folder){
                $sDir = $dir."/".$folder;
                foreach(glob($sDir.'/*') as $file){
                    $nmeArr = explode($dir.'/',$file);
                    $nme = $nmeArr[1];
                    if(strpos($nme,'.') !== FALSE){
                        if(!in_array($nme, $this->updEcxeptFile)){
                            $fileList[$fileCount] = $nme;
                            //$fileCont[$fileCount] = file_get_contents($file);
                            $fileCount++;
                        }
                    }else{
                        if(!in_array($nme, $this->updEcxeptFold)){
                            $sFoldList[$sFoldCount] = $nme;
                            $sFoldCount++;
                        }
                    }
                }
            }
            //... Other Subdirectories ...
            for($a = 0; $a < count($sFoldList); $a++){
                $sDir = $dir."/".$sFoldList[$a];
                foreach(glob($sDir.'/*') as $file){
                    $nmeArr = explode($dir.'/',$file);
                    $nme = $nmeArr[1];
                    if(strpos($nme,'.') !== FALSE){
                        if(!in_array($nme, $this->updEcxeptFile)){
                            $fileList[$fileCount] = $nme;
                            //$fileCont[$fileCount] = file_get_contents($file);
                            $fileCount++;
                        }
                    }else{
                        if(!in_array($nme, $this->updEcxeptFold)){
                            $sFoldList[$sFoldCount] = $nme;
                            $sFoldCount++;
                        }
                    }
                }
            }
        }
        $retArr = array($fileList,$foldList,$sFoldList,$fileCont);
        return $retArr;
    }

    /**
     * Log Crash date & time and increment crash counter.
     * @param $mac
     * @param $func
     * @param $data
     * @param $tim
     * @param $user
     * @param $tpe
     * @return bool
     */
    function logCrash($mac, $func, $data, $tim, $user, $tpe="dev"){
        if($tpe == "thr"){
            $tpe = "15";
        }else {
            $tpe = "3";
        }
        $count = $this->db->select("SELECT crash_count FROM raspi WHERE pi_mac='$mac'");
        $new = (int)$count[0]['crash_count'];
        $new++;
        if($tpe == "3"){
            $update = $this->db->update("raspi", array("crash_count" => $new, "last_crash" => $this->date), "pi_mac='$mac'");
        }else{
            $update = true;
        }
        if($update){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => $tpe,
                "dat" => $data
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Log data requested by admin
     * @param $mac
     * @param $func
     * @param $data
     * @param $tim
     * @param $user
     * @return bool
     */
    function logAdminReq($mac, $func, $data, $tim, $user){
        $res = $this->checkUsage($mac);
        if($this->addHighEvent($res[0]['debug_mode'],"8")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => "8",
                "dat" => $data
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Log data requested by admin
     * @param $mac
     * @param $func
     * @param $data
     * @param $tim
     * @param $user
     * @return bool
     */
    function logConfig($mac, $func, $data, $tim, $user){
        $res = $this->checkUsage($mac);
        if($this->addHighEvent($res[0]['debug_mode'],"18")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => "18",
                "dat" => $data
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Log Test in DB.
     * @param $mac
     * @param $data
     * @param $tim
     * @param $user
     * @return bool
     */
    function logTest($mac, $user, $tim, $data){
        $res = $this->checkUsage($mac);
        if($this->addHighEvent($res[0]['debug_mode'],"1")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => "1",
                "dat" => $data
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Log Connection in DB.
     * @param $mac
     * @param $data
     * @param $tim
     * @param $user
     * @return bool
     */
    function logConn($mac, $user, $tim, $data){
        $res = $this->checkUsage($mac);
        if($this->addHighEvent($res[0]['debug_mode'],"13")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => "13",
                "dat" => $data.";".$tim
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Log Alarm Responses in DB.
     * @param $mac
     * @param $data
     * @param $tim
     * @param $user
     * @return bool
     */
    function logAlResp($mac, $user, $tim, $data){
        $res = $this->checkUsage($mac);
        if($this->addHighEvent($res[0]['debug_mode'],"14")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $mac,
                "user_id" => $user,
                "server_datetime" => $this->date,
                "device_datetime" => $tim,
                "type" => "14",
                "dat" => $data
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Log software update in DB.
     * @param $fullData
     * @return bool
     */
    function logSoftUpd($fullData){
        $res = $this->checkUsage($fullData['mac']);
        $fullData['devTim'] == "-" ? $tmpDevTim = null : $tmpDevTim = $fullData['devTim'];
        if($this->addHighEvent($res[0]['debug_mode'],"5")){
            $new = $this->db->insert("raspi_high_events", array(
                "mac_address" => $fullData['mac'],
                "user_id" => $res[0]['login_id'],
                "server_datetime" => $this->date,
                "device_datetime" => $tmpDevTim,
                "type" => "5",
                "dat" => $this->curSoftVer
            ));
            if($new){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Add a new PI on first connection and set default values.
     * @param $arr
     * @return array
     */
    function newRasPi($arr){
        $this->setPars($arr);
        $resp = array();
        $mac = $arr['mac'];
        $func = $arr['func'];
        $count = $this->db->select("SELECT * FROM raspi WHERE pi_mac='$mac'");

        if (count($count) > 0) {
            $resp['status'] = 0;
            $resp['message'] = "Duplicate Entry Found. Pi not added to DB.";
        } else {
            $create = $this->db->insert("raspi", array(
                "pi_mac" => $arr['mac'],
                "pi_function" => $arr['func'],
                "server_url" => $arr['launchURL'],
                "healthcheck_interv" => $arr['healthInter'],
                "sms_interv" => $arr['smsInter'],
                "alarm_poll_interv" => $arr['alarmPollInter'],
                "alarm_response_interv" => $arr['alarmRespInter'],
                "alarm_on" => $arr['alarmOn'],
                "alarm_off" => $arr['alarmOff'],
                "voice_char" => $arr['voiceChar'],
                "alarm_response_msg_interv" => $arr['alarmRespMsgInter'],
                "mail_limit" => '1',
                "pi_volume" => $arr['volume'],
                "pi_siren" => $arr['siren'],
                "crash_count" => '0',
                "date_created" => $this->date,
                "config_ver" => "1",
                "pi_descript" => $arr['desc'],
                "pi_usage" => 'Static',
                "pi_softw_ver" => $arr['softVer'],
                "last_healthcheck" => $this->date
            ));

            if($create){
                //$tmpMail = $this->sendMail('Registration', '', 'Raspberry-PI Registration', $this->params);
                $resp['status'] = 1;
                $resp['message'] = "Pi successfully added to DB.";
                //$resp['message'] .= " - ".$tmpMail['message'];
                $resp['uID'] = Session::get('SESS_MEMBER_ID');
            }else{
                $resp['status'] = 0;
                $resp['message'] = "Unexpected insert error. Pi not added to DB.";
            }
        }

        return $resp;
    }

    /**
     * Activate the device From the devise login request. (Not for use from admin page)
     * @param $un
     * @param $pw
     * @param $arr
     * @return array
     */
    function actRasPi($un, $pw, $arr){
        $this->setPars($arr);
        $resp = array();
        $mac = $arr['mac'];
        $func = $arr['func'];
        $count = $this->db->select("SELECT * FROM raspi WHERE pi_mac='$mac'");

        if (count($count) > 0) {
            if($this->checkUser($un, md5($pw), "own")){
                $tMac = $arr['mac'];
                $tFunc = $arr['func'];
                $upd = $this->db->update("raspi", array(
                    "pi_descript" => $arr['desc'],
                    "pi_user" => $un,
                    //"pi_pass" => $this->encryptPW($pw),
                    "pi_pass" => md5($pw),
                    "enc" => "own",
                    "test_result" => $arr['tRes'],
                    "test_data" => $arr['tDat'],
                    "test_date" => $arr['tDate'],
                    "pi_usage" => "Active"
                ), "pi_mac='$tMac'");
                if($upd){
                    $logis = $this->xhrLogin($un, md5($pw), "own");
                    if($logis->error == 1){
                        $resp['status'] = 0;
                        $resp['message'] = $logis->errorDescription;
                    }
                    $clUpd = $this->updclientDets($tMac,$tFunc);
                    if($clUpd['status'] == 1){
                        //$tmpMail = $this->sendMail('Registration', '', 'Raspberry-PI Registration', $this->params);
                        //$resp['status'] = 2;
                        $resp['status'] = 1;
                        $resp['message'] = "This MAC Address and function has been linked in the DB";
                        //$resp['message'] .= " - ".$tmpMail['message'];
                        $resp['uID'] = Session::get('SESS_MEMBER_ID');
                    }else{
                        $resp['status'] = 0;
                        $resp['message'] = $clUpd['message'];
                    }
                }else{
                    $resp['status'] = 0;
                    $resp['message'] = "Error updating PI in DB.";
                }
            }else{
                $resp['status'] = 0;
                $resp['message'] = "Incorrect User Details. Try again after reboot.";
            }
        } else {
            $resp['status'] = 0;
            $resp['message'] = "Device not found in DB.";
        }

        return $resp;
    }

    /**
     * Update client details.
     * @param $tMac
     * @param $tFunc
     * @return mixed
     */
    function updclientDets($tMac,$tFunc){
        $user = new User();
        $mainUserID = Session::get('SESS_MEMBER_MAINUSERID');
        $userID = Session::get('SESS_MEMBER_ID');
        $mainUser = $user->getByUserID($mainUserID);
        $logUser = $user->getByUserID($userID);

        $mainUserName = $mainUser[0]['firstname']. " ".$mainUser[0]['lastname'];
        $logUserName = $logUser[0]['firstname']. " ".$logUser[0]['lastname'];

        $upd = $this->db->update("raspi", array("pi_client" => $mainUserName, "pi_login_client" => $logUserName, "login_id" => $userID), "pi_mac='$tMac'");
        if($upd){
            $resp['status'] = 1;
            $resp['message'] = "Client details set in DB";
        }else{
            $resp['status'] = 0;
            $resp['message'] = "Error setting client details";
        }
        return $resp;
    }

    /**
     * Encrypt Password.
     * @param $pass
     * @return string
     */
    function encryptPW($pass){
        //$qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->key), $pass,MCRYPT_MODE_CBC, md5(md5($this->key))));
        $qEncoded = openssl_encrypt($pass, "AES-128-CTR", md5($this->key), 0, md5(md5($this->key)));
        return($qEncoded);
    }

    /**
     * Dycrypt Password
     * @param $pass
     * @return string
     */
    function decryptPW($pass){
        //$qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($pass),MCRYPT_MODE_CBC, md5(md5($this->key))),"\0");
        $qDecoded = openssl_decrypt ($pass, "AES-128-CTR", md5($this->key), 0, md5(md5($this->key)));
        return($qDecoded);
    }


    function getAlHist($mac,$user,$date){
        $rfid = new RFID();
        $list = array();
        $macaddress = str_replace(':','',$mac);//strip the : from the mac address
        $table_name = $this->common->getDeviceTable($macaddress);

        $date .= " 00:00:00";
        $alertObj = $rfid->loadAlerts($user,0,$date);
        $alertList = $alertObj->alerts;

        $res = $this->db->select("SELECT * FROM $table_name WHERE typ='802' AND date='$date' ORDER BY server_datetime DESC");
        $len = count($res);
        for($a = 0; $a < $len; $a++){
            $tim = strtok($res[$a]['tim'], ".");
            $logTime = substr($res[$a]['tim'], 0, 2) . ':' . substr($res[$a]['tim'], 2, 2) . ':' . substr($res[$a]['tim'], 4, 2);
            $curPos = $len - 1;
            if($a == $curPos){
                $nextTim = "000000";
            }else{
                $nextTim = strtok($res[$a + 1]['tim'], ".");
            }

            $logDat = $res[$a]['dat'];
            foreach($alertList as $alert){
                $alertDets = $alert[2];
                $testTim = str_replace(':','',$alertDets['tim']);
                if($testTim <= $tim && $testTim > $nextTim){
                    $list[] = array($logTime,$alertDets);
                }
            }
        }
        return $list;
    }

    /**
     * Allow addition of data to high_events table based on debug mode and event type.
     * @param $debug
     * @param $event
     * @return bool
     */
    function addHighEvent($debug,$event){
        if($debug == 0){
            if(in_array($event,$this->alwaysAllow)){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Get a description of the high event type code.
     * @param $type
     * @return string
     */
    function getTypeDesript($type){
        $descript = "";

        switch ($type){
            case "1":
                $descript = "Testing Device";
                break;
            case "2":
                $descript = "Health Check";
                break;
            case "3":
                $descript = "Event Log (Device Restart)";
                break;
            case "4":
                $descript = "Config Change";
                break;
            case "5":
                $descript = "Software Update";
                break;
            case "6":
                $descript = "Remote Forced Software Update";
                break;
            case "7":
                $descript = "Remote Forced Reboot";
                break;
            case "8":
                $descript = "Events Log (Admin Request)";
                break;
            case "9":
                $descript = "Ping Device";
                break;
            case "10":
                $descript = "Ping Response";
                break;
            case "11":
                $descript = "Event Log (Connection Restored)";
                break;
            case "12":
                $descript = "Alarm ignored";
                break;
            case "13":
                $descript = "Connection Restored";
                break;
            case "14":
                $descript = "Alarm Responses";
                break;
            case "15":
                $descript = "Event Log (Thread Restart)";
                break;
            case "16":
                $descript = "Admin Requested Log";
                break;
            case "17":
                $descript = "Admin Requested Config";
                break;
            case "18":
                $descript = "Config Log";
                break;
            default:
                $descript = "";
                break;
        }

        return $descript;
    }

    /**
     * Format data field in raspi_high_events to usable structure.
     * @param $type
     * @param $data
     * @return StdClass
     */
    function formatTypeData($type,$data){
        $rsp = new StdClass();
        $u = new User();
        //$logArr = array('3','8','11','15');
        $logArr = array('3','8','11','15');

        $rsp->title = $this->getTypeDesript($type);
        if($type == "1"){
            $tmpData = explode(";", $data);
            $rsp->user = $tmpData[0];
            $rsp->date = $tmpData[1];
            $rsp->result = $tmpData[2];
            $rsp->keyb = $tmpData[3];
            $rsp->mouse = $tmpData[4];
            $rsp->relay = $tmpData[5];
            $rsp->soundL = $tmpData[6];
            $rsp->soundH = $tmpData[7];
            $rsp->wd = $tmpData[8];
            $rsp->commOut = $tmpData[9];
            $rsp->commIn = $tmpData[10];
            $rsp->info = $tmpData[11];
            $rsp->data = $tmpData[3].";".$tmpData[4].";".$tmpData[5].";".$tmpData[6].";".$tmpData[7].";".$tmpData[8].";".$tmpData[9].";".$tmpData[10].";".$tmpData[11];
        }else if(in_array($type,$logArr)){
            $lineArr = array();
            $tmpData = explode("\n", $data);
            foreach($tmpData as $line){
                if(strlen($line) > 0){
                    if((strpos($line, "last_health") !== false) && (strpos($line, "last_alarm") !== false)){
                        $tmpData2 = explode("~", $line);
                        foreach($tmpData2 as $statInfo){
                            $lineArr[] = array('',$statInfo);
                        }
                    }else{
                        $pos1 = strpos($line, ":");
                        $stat = substr($line,0,$pos1);
                        $pos2 = strpos($line, ":", $pos1+2);
                        $lineArr[] = array($stat,substr( $line, $pos2+1));
                    }
                }
            }
            $rsp->data = $lineArr;
        }else if($type == "4"){
            $tmpData = explode(";", $data);
            $newDat = array();
            foreach($tmpData as $newData){
                $pos1 = strpos($newData, "'");
                $newDat[] = substr( $newData, $pos1+1, -1);
            }
            $userDat = $u->getByUserID($tmpData[0]);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
            foreach($tmpData as $row){
                $t = str_replace("'","",$row);
                $tSplit = explode("=",$t);
                $key = $tSplit[0];
                $rsp->$key = $tSplit[1];
            }
        }else if($type == "5"){
            $rsp->data = $data;
        }else if($type == "6"){
            $tmpData = explode(";", $data);
            $userDat = $u->getByUserID($tmpData[0]);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
            $rsp->ver = $tmpData[1];
        }else if($type == "7"){
            $userDat = $u->getByUserID($data);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
        }else if($type == "9"){
            $tmpData = explode(";", $data);
            $userDat = $u->getByUserID($tmpData[0]);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
            if($tmpData[1] == ""){
                $tmpData[1] = "No Message, only pinged for response.";
            }
            $rsp->msg = $tmpData[1];
        }else if($type == "10"){
            $tmpData = explode(";", $data);
            $rsp->respMsg = $tmpData[1];
            $rsp->reqMsgTim = strtok($tmpData[2],".");
            $rsp->respMsgTim = strtok($tmpData[3],".");
            $rsp->reqMsgId = $tmpData[4];
        }else if($type == "13"){
            $tmpData = explode(";", $data);
            $timeDiff = $this->common->timeDiff($tmpData[0],$tmpData[1]);
            $rsp->from = $tmpData[0];
            $rsp->to = $tmpData[1];
            $rsp->diff = $timeDiff;
        }else if($type == "16"){
            $userDat = $u->getByUserID($data);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
        }else if($type == "17"){
            $userDat = $u->getByUserID($data);
            $rsp->uID = $userDat[0]['user_id'];
            $rsp->user = $userDat[0]['firstname']." ".$userDat[0]['lastname'];
        }else if($type == "18"){
            $lineArr = array();
            $tmpData = explode("\n", $data);
            foreach($tmpData as $line){
                if(strlen($line) > 0){
                    $lineArr[] = $line;
                }
            }
            $rsp->data = $lineArr;
        }

        return $rsp;
    }



}