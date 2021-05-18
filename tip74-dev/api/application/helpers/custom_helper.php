<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function send_mail($emailData = array()) {
    require 'send/vendor/autoload.php';

    $Obj = & get_instance();
    $SandgridAccount = $Obj->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "SendGridApi" LIMIT 1');
    $apiKey = trim($SandgridAccount->row()->ConfigTypeValue);
    $request_body = json_decode('{
            "personalizations": [
            {
                "to": [
                {
                    "email": "' . $emailData['emailTo'] . '"
                }],
                "dynamic_template_data":{
                    "site_url"              :   "' . SITE_HOST . '",
                    "BASE_URL"              :   "' . BASE_URL . '",
                    "ASSET_BASE_URL"        :   "' . ASSET_BASE_URL . '",
                    "SITE_NAME"             :   "' . SITE_NAME . '",
                    "DEFAULT_CURRENCY"      :   "' . DEFAULT_CURRENCY . '",
                    "REFERRAL_SIGNUP_BONUS" :   "' . REFERRAL_SIGNUP_BONUS . '",
                    "FACEBOOK_URL"          :   "' . FACEBOOK_URL . '",
                    "TWITTER_URL"           :   "' . TWITTER_URL . '",
                    "LINKEDIN_URL"          :   "' . LINKEDIN_URL . '",
                    "INSTAGRAM_URL"         :   "' . INSTAGRAM_URL . '",
                    "Name"                  :   "' . $emailData['Name'] . '",
                    "EmailText"             :   "' . $emailData['EmailText'] . '",
                    "PhoneNumber"           :   "' . $emailData['PhoneNumber'] . '",
                    "Title"                 :   "' . $emailData['Title'] . '",
                    "Message"               :   "' . $emailData['Message'] . '",
                    "ContestName"           :   "' . $emailData['ContestName'] . '",
                    "SeriesName"            :   "' . $emailData['SeriesName'] . '",
                    "InviteCode"            :   "' . $emailData['InviteCode'] . '",
                    "MatchNo"               :   "' . $emailData['MatchNo'] . '",
                    "TeamNameLocal"         :   "' . $emailData['TeamNameLocal'] . '",
                    "TeamNameVisitor"       :   "' . $emailData['TeamNameVisitor'] . '",
                    "TotalPoints"           :   "' . $emailData['TotalPoints'] . '",
                    "UserRank"              :   "' . $emailData['UserRank'] . '",
                    "Token"                 :   "' . $emailData['Token'] . '",
                    "DeviceTypeID"          :   "' . $emailData['DeviceTypeID'] . '",
                    "Amount"                :   "' . $emailData['Amount'] . '",
                    "ReferralCode"          :   "' . $emailData['ReferralCode'] . '",
                    "ReferralURL"           :   "' . $emailData['ReferralURL'] . '",
                    "Password"              :   "' . $emailData['Password'] . '",
                    "date"                  :   "' . date('Y') . '"
                }
            }
            ],
            "from": {
                "email": "gaurav@mobiwebtech.in"
                },

                "template_id"   : "' . $emailData['template_id'] . '",
                "subject"       : "' . $emailData['Subject'] . '",
                "content"       : [
                {
                    "type": "text/html",
                    "value": "and easy to do anywhere"
                }
                ]
            }');
    // sending email 
    $sg = new \SendGrid($apiKey);

    $response = $sg->client->mail()->send()->post($request_body);
    $response->statusCode();
    $response->body();
    $response->headers();
    // print_r($response);
    return true;
}

/* ------------------------------ */
/* ------------------------------ */

function sendPushMessage($UserID, $Title, $Message, $Data = array(), $redirect = '') {
    if (!isset($Data['content_available'])) {
        $Data['content_available'] = 1;
    }
    $Obj = & get_instance();
    $Obj->db->select('DISTINCT(US.DeviceGUID),U.UserTypeID, US.DeviceTypeID, US.DeviceToken');
    $Obj->db->from('tbl_users_session US');
    $Obj->db->from('tbl_users U');
    $Obj->db->where("US.UserID", $UserID);
    $Obj->db->where("US.UserID", "U.UserID", FALSE);
    $Obj->db->where("US.DeviceToken!=", '');
    $Obj->db->where('US.DeviceToken is NOT NULL', NULL, FALSE);
    if (!MULTISESSION) {
        $this->db->limit(1);
    }
    $Query = $Obj->db->get();
    //echo $Obj->db->last_query();
    if ($Query->num_rows() > 0) {
        foreach ($Query->result_array() as $Notifications) {
            if ($Notifications['DeviceTypeID'] == 2) { /* I phone */
                pushNotificationIphone($Notifications['DeviceToken'], $Notifications['UserTypeID'], $Title, $Message, $Data, $redirect);
            } elseif ($Notifications['DeviceTypeID'] == 3) { /* android */
                pushNotificationAndroid($Notifications['DeviceToken'], $Notifications['UserTypeID'], $Title, $Message, $Data, $redirect);
            }
        }
    }
}

function pushNotificationAndroidBroadcast($title, $Message, $refrenceID = '') {
    if (ENVIRONMENT == "production") {
        $topic_name = CHANNEL_NAME;
    } else {
        $topic_name = "Alert-Dev";
    }
    /* Send Notifications */
    $post_fcm = array();
    $post_fcm['to'] = '/topics/' . $topic_name;
    $post_fcm['data'] = array(
        'badges' => 1,
        'title' => $title,
        'message' => $Message,
        'refrenceID' => $refrenceID
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($post_fcm, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => array(
            "authorization: key=" . ANDROID_SERVER_KEY,
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    // print_r($response); exit;
    if ($err) {
        return false;
    } else {
        return true;
    }
}

function pushNotificationIphoneBroadcast($title, $Message, $refrenceID = '') {
    if (ENVIRONMENT == "production") {
        /* Send Notifications */
        $topic_name = 'alert_ios';
        $post_fcm = array();
        $post_fcm['to'] = '/topics/' . $topic_name;
        $post_fcm['notification'] = array(
            'badges' => 1,
            'title' => $title,
            'message' => $Message,
            'content_available' => true,
            'refrenceID' => $refrenceID
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fcm, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => array(
                "authorization: key=" . ANDROID_SERVER_KEY,
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        // print_r($response); exit;
        if ($err) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

/* ------------------------------ */
/* ------------------------------ */

function pushNotificationAndroid($DeviceIDs, $UserTypeID, $Title, $Message, $Data = array(), $redirect = '') {
    //API URL of FCM
    $post_fcm = array();
    $post_fcm['registration_ids'] = array($DeviceIDs);
    $post_fcm['data'] = array(
        'badges' => 1,
        'title' => $Title,
        'message' => $Message,
        'refrenceID' => "",
        'redirect' => $redirect
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($post_fcm),
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Authorization: key=" . ANDROID_SERVER_KEY,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return true;
    }
}

/* ------------------------------ */
/* ------------------------------ */

function pushNotificationIphone($DeviceToken = '', $UserTypeID, $Title, $Message = '', $Data = array(), $redirect = '') {
    //API URL of FCM
    $post_fcm = array();
    $post_fcm['registration_ids'] = array($DeviceToken);
    $post_fcm['notification'] = array(
        'badges' => 1,
        'title' => $Title,
        'message' => $Message,
        "content_available" => true,
        'refrenceID' => "",
        'redirect' => $redirect
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($post_fcm),
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Authorization: key=" . ANDROID_SERVER_KEY,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        return true;
    }
}

/* ------------------------------ */
/* ------------------------------ */

function sendSMS_old($input = array()) {
    //Your authentication key
    $authKey = "6a077d91f259674b21e60cffda125dae";
    //Multiple mobiles numbers separated by comma
    $mobileNumber = $input['PhoneNumber'];
    //Sender ID,While using route4 sender id should be 6 characters long.
    $senderId = "BLKSMS";
    //Your message to send, Add URL encoding here.
    $message = $input['Text'];
    //Define route
    $route = "4";
    //Prepare you post parameters
    $postData = array('authkey' => $authKey, 'mobiles' => $mobileNumber, 'message' => $message, 'sender' => $senderId, 'route' => $route);
    if (ENVIRONMENT == 'production') {
        $url = urlencode("http://smsgateway.ca/SendSMS.aspx?CellNumber=$mobileNumber&AccountKey=zP70k0f8S70AacoogxnU3Q7WVnh460yx&MessageBody=$message");
    } else {
        $url = "http://sms.bulksmsserviceproviders.com/api/send_http.php";
    }
    $url = "http://sms.bulksmsserviceproviders.com/api/send_http.php";
    // init the resource
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
    ));
    //Ignore SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //get response
    $output = curl_exec($ch);
    //Print error if any
    if (curl_errno($ch)) {
        echo 'error:' . curl_error($ch);
    }
    curl_close($ch);
    //echo $output;
}

/* ------------------------------ */
/* ------------------------------ */

function sendMail($Input = array()) {
    $CI = & get_instance();
    $CI->load->library('email');
    $config['protocol'] = SMTP_PROTOCOL;
    $config['smtp_host'] = SMTP_HOST;
    $config['smtp_port'] = SMTP_PORT;
    $config['smtp_user'] = SMTP_USER;
    $config['smtp_pass'] = SMTP_PASS;
    $config['charset'] = "utf-8";
    $config['mailtype'] = "html";
    $config['wordwrap'] = TRUE;
    $config['smtp_crypto'] = SMTP_CRYPTO;
    $CI->email->initialize($config);
    $CI->email->set_newline("\r\n");
    $CI->email->clear();
    $CI->email->from(FROM_EMAIL, FROM_EMAIL_NAME);
    $CI->email->reply_to(NOREPLY_EMAIL, NOREPLY_NAME);
    $CI->email->to($Input['emailTo']);

    if (defined('TO_BCC') && !empty(TO_BCC)) {
        $CI->email->bcc(TO_BCC);
    }

    if (!empty($Input['emailBcc'])) {
        $CI->email->bcc($Input['emailBcc']);
    }

    $CI->email->subject($Input['emailSubject']);
    $CI->email->message($Input['emailMessage']);
    if (@$CI->email->send()) {
        return true;
    } else {
        // echo $CI->email->print_debugger();
        return false;
    }
}

/* ------------------------------ */
/* ------------------------------ */

function emailTemplate($HTML) {
    $CI = & get_instance();
    return $CI->load->view("emailer/layout", array("HTML" => $HTML), TRUE);
}

/* ------------------------------ */
/* ------------------------------ */

function checkDirExist($DirName) {
    if (!is_dir($DirName)) mkdir($DirName, 0777, true);
}

/* ------------------------------ */
/* ------------------------------ */

function validateEmail($Str) {
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $Str)) ? FALSE : TRUE;
}

/* ------------------------------ */
/* ------------------------------ */

function validateDate($Date) {
    if (strtotime($Date)) {
        return true;
    } else {
        return false;
    }
}

/* ------------------------------ */
/* ------------------------------ */

function paginationOffset($PageNo, $PageSize) {
    if (empty($PageNo)) {
        $PageNo = 1;
    }
    $offset = ($PageNo - 1) * $PageSize;
    return $offset;
}

/* ------------------------------ */
/* ------------------------------ */

function get_guid() {
    if (function_exists('com_create_guid')) {
        return strtolower(com_create_guid());
    } else {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        return strtolower($uuid);
    }
}

/* ------------------------------ */
/* ------------------------------ */

function dateDiff($FromDateTime, $ToDateTime) {
    $start = date_create($FromDateTime);
    $end = date_create($ToDateTime); // Current time and date
    return $diff = date_diff($start, $end);
    echo 'The difference is ';
    echo $diff->y . ' years, ';
    echo $diff->m . ' months, ';
    echo $diff->d . ' days, ';
    echo $diff->h . ' hours, ';
    echo $diff->i . ' minutes, ';
    echo $diff->s . ' seconds';
    // Output: The difference is 28 years, 5 months, 19 days, 20 hours, 34 minutes, 36 seconds
    echo 'The difference in days : ' . $diff->days;
    // Output: The difference in days : 10398
}

/* ------------------------------ */
/* ------------------------------ */

function diffInHours($startdate, $enddate) {
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $difference = abs($endtimestamp - $starttimestamp) / 3600;
    return $difference;
}

/* ------------------------------ */
/* ------------------------------ */

function array_keys_exist(array $needles, array $StrArray) {
    foreach ($needles as $needle) {
        if (in_array($needle, $StrArray)) return true;
    } return false;
}

/* ------------------------------ */
/* ------------------------------ */

function phparraysort($Array, $SortBy = array(), $Sort = SORT_REGULAR) {
    if (is_array($Array) && count($Array) > 0 && !empty($SortBy)) {
        $Map = array();
        foreach ($Array as $Key => $Val) {
            $Sort_key = '';
            foreach ($SortBy as $Key_key) {
                $Sort_key .= $Val[$Key_key];
            }
            $Map[$Key] = $Sort_key;
        }
        asort($Map, $Sort);
        $Sorted = array();
        foreach ($Map as $Key => $Val) {
            $Sorted[] = $Array[$Key];
        }
        return array_reverse($Sorted);
    }
    return $Array;
}

/* ------------------------------ */
/* ------------------------------ */

function dump($Response, $json = false) {
    if (!$json) {
        echo "<pre>";
        print_r($Response);
        echo "</pre>";
    } else {
        echo json_encode($Response);
    }
    exit;
}

/* ------------------------------ */
/* ------------------------------ */

function mongoDBConnection() {
    //Require MongoDB Library & Connection 
    $Obj = &get_instance();
    require_once getcwd() . '/vendor/autoload.php';
    switch (ENVIRONMENT) {
        case 'local':
            $Obj->ClientObj = new MongoDB\Client("mongodb://localhost:27017");
            break;
        case 'testing':
            $Obj->ClientObj = new MongoDB\Client("mongodb://MOBIWEBTECH2020:MOBIWEBTECH2020SORAVGARG@localhost:58017");
            break;
        case 'demo':
           $Obj->ClientObj = new MongoDB\Client("mongodb://FslDevelopment:Fsl11#Sports@localhost:27017");
            break;
        default:
            $Obj->ClientObj = new MongoDB\Client("mongodb://FslDevelopment:Fsl11#Sports@localhost:27017");
            break;
    }
    $rc = $Obj->ClientObj->getWriteConcern();
    $Obj->fantasydb = $Obj->ClientObj->tip74;
}

function QueryLogDebug($currentFunctionName, $Query) {
    $currenTime = date('Y-m-d h:i:s', time());
    //$currentFunctionName = __FUNCTION__;
    $msg = "query fails :" . $Query;
    $logfile = fopen("log/" . $currenTime . "_" . $currentFunctionName . "_log.txt", "w") or die("Unable to open file!");
    fwrite($logfile, $msg);
    fclose($logfile);
}

/**
 * [to check url is 404 or not]
 * @param  string $url
 */
if ( ! function_exists('is_404')) 
{ 
  function is_404($url) {
      $handle = curl_init($url);
      curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

      /* Get the HTML or whatever is linked in $url. */
      $response = curl_exec($handle);

      /* Check for 404 (file not found). */
      $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
      curl_close($handle);

      /* If the document has loaded successfully without any redirection or error */
      if ($httpCode >= 200 && $httpCode < 300) {
          return false;
      } else {
          return true;
      }
  }
}

/**
 * [To print array]
 * @param array $arr
*/
if ( ! function_exists('pr')) {
  function pr($arr)
  {
    echo '<pre>'; 
    print_r($arr);
    echo '</pre>';
    die;
  }
}

/**
* [To save file from another server to own server]
* @param  [string] $file
* @param  [string] $subfolder
*/
if(!function_exists('downloadImagesFromLiveServer'))
{
  function downloadImagesFromLiveServer($file,$subfolder)
  {
    if(!SAVE_LIVE_FLAGS || empty($file)){
        return FALSE;
    }
    
    $explode_file = explode(".", $file);
    if(!empty($explode_file)){
      $ext      = end($explode_file);
      $pic      = file_get_contents($file);
      $filename = time().'.'.$ext;
      $path     = 'uploads/'.$subfolder."/".$filename;
      file_put_contents($path, $pic);
      chmod($path, 0777);
      return $filename;
    }
  }
}

/**
* [To get all dates betweeb start date & end date]
* @param  [string] $strDateFrom
* @param  [string] $strDateTo
*/
if (!function_exists('get_date_range'))
{
  function get_date_range($strDateFrom,$strDateTo)
  {
      $aryRange=array();

      $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
      $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

      if ($iDateTo>=$iDateFrom)
      {
          array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
          while ($iDateFrom<$iDateTo)
          {
              $iDateFrom+=86400; // add 24 hours
              array_push($aryRange,date('Y-m-d',$iDateFrom));
          }
      }
      return $aryRange;
  }
}