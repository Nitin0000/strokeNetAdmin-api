<?php

namespace Acme\Controllers;

use Interop\Container\ContainerInterface;

class BaseController
{
    protected $ci;
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;

        $settings = $this->ci->db_settings;
        foreach ($settings as $value) {
            define($value["setting_name"], $value["setting_value"]);
        }
    }

    public function pr($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    public function formatNames($value)
    {
        $explode_txt = explode("_", $value);
        $newtext = join(" ", $explode_txt);
        return ucwords($newtext);
    }

    public function sendEmail($data)
    {
        $email = $this->ci->email;
        $email
            ->addTo($data['to'])
            ->setFrom('noreply@strokenetchandigarh.com')
            ->setFromName('StrokeNetChandigarh.com')
            ->setSubject($data['subject'])
            ->setText($data['text'])
            ->setHtml($data['html']);
        $res = $this->ci->sendgrid->send($email);
        return $res->body;
    }

    public function sendPush($data_push)
    {
        $appId = ONE_SIGNAL_APP_ID;
        $restKey = ONE_SIGNAL_REST_KEY;

        $fields = array(
            'app_id' => $appId,
            'url' => $data_push['url'],
            'contents' => array(
                "en" => $data_push['message'],
            ),
            'include_player_ids' => $data_push['devices'],
            'headings' => array(
                "en" => $data_push['title'],
            ),
            'ios_sound' => 'notification.wav',
            'android_channel_id' => '3921113c-55a9-46ac-a838-06a4eeae6104',
            'android_sound' => 'notification',
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $restKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function codeStrokeSendPush($data_push)
    {
        $appId = ONE_SIGNAL_APP_ID;
        $restKey = ONE_SIGNAL_REST_KEY;

        $fields = array(
            'app_id' => $appId,
            'url' => $data_push['url'],
            'contents' => array(
                "en" => $data_push['message'],
            ),
            'include_player_ids' => $data_push['devices'],
            'headings' => array(
                "en" => $data_push['title'],
            ),
            'ios_sound' => 'sound.wav',
            'android_channel_id' => '3d0fc596-bfd5-4981-8791-538cf031fcee',
            'android_sound' => 'sound',
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $restKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function createPushForSpecificTime($data_push)
    {
        $appId = ONE_SIGNAL_APP_ID;
        $restKey = ONE_SIGNAL_REST_KEY;
        $fields = array(
            'app_id' => $appId,
            'url' => $data_push['url'],
            'contents' => array(
                'en' => $data_push['message'],
            ),
            'include_player_ids' => $data_push['devices'],
            'headings' => array(
                'en' => $data_push['title'],
            ),
            'send_after' => $data_push['date_time'],
            'ios_sound' => 'sound.wav',
            'android_channel_id' => '3d0fc596-bfd5-4981-8791-538cf031fcee',
            'android_sound' => 'sound',
        );

        if (isset($data_push['image']) && $data_push['image']) {
            $fields['ios_attachments'] = array('id1' => $data_push['image']);
            $fields['big_picture'] = $data_push['image'];
        }

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $restKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function sendSMS($data)
    {
        $sms_settings = $this->ci->sms;
        $AUTH_ID = $sms_settings['authId'];
        $AUTH_TOKEN = $sms_settings['authToken'];
        $src = '+13648889207';

        # SMS destination number
        $dst = $data['to'];
        $text = $data['message'];
        $url = 'https://api.plivo.com/v1/Account/' . $AUTH_ID . '/Message/';
        $data = array("src" => $src, "dst" => $dst, "text" => $text);
        $data_string = json_encode($data);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }

    public function createCall($data)
    {
        $sms_settings = $this->ci->sms;
        $AUTH_ID = $sms_settings['authId'];
        $AUTH_TOKEN = $sms_settings['authToken'];
        $src = '+13648889207';

        # SMS destination number
        $dst = $data['to'];
        $url = 'https://api.plivo.com/v1/Account/' . $AUTH_ID . '/Call/';
        $data = array("from" => $src, "to" => $dst, "answer_url" => "http://s3.amazonaws.com/static.plivo.com/answer.xml");
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }

    public function checkSMS($messageId)
    {
        $sms_settings = $this->ci->sms;
        $AUTH_ID = $sms_settings['authId'];
        $AUTH_TOKEN = $sms_settings['authToken'];
        $url = 'https://api.plivo.com/v1/Account/' . $AUTH_ID . '/Message/' . $messageId . '/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function validateMobileNUmber($mobile)
    {
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

    public function findAndVerifyOTP($data)
    {
        $checkIfthisExists = $this->ci->db->get("sms_verifications", array("id"), array(
            "AND" => array(
                "phone_number" => $data['phone_number'],
                "otp_code" => md5($data['otp_code']),
                "message_api_code" => $data['message_api_code'],
                "used" => "0",
            ), "ORDER" => array("id" => "DESC"),
        ));
        if (isset($checkIfthisExists['id']) && $checkIfthisExists['id']) {
            return 1;
        } else {
            return 0;
        }
    }

    public function printData($type, $data)
    {
        $result = array();
        if ($type == "error") {
            $result['success'] = false;
            $result['error'] = true;
            $result['data'] = $data;
        } else {
            $result['success'] = true;
            $result['error'] = false;
            $result['data'] = $data;
        }
        return $result;
    }

    public function validateEmail($value, $validation_for)
    {
        if ($validation_for == "email") {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        } elseif ($validation_for == "password") {
            $varlength = strlen($value);
            if ($varlength <= 6) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return "Fill proper parameters to run function";
        }
    }

    public function randomPassword($length = 6)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function verification_code()
    {
        $alphabet = "0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 4; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function cleanValue($str)
    {
        $str = @trim($str);
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        $str = strip_tags($str);
        return $str;
    }

    public function validateUser($userID, $userToken)
    {
        $get_user = $this->ci->db->get("users", array("user_id", "token", "token_expire"), array("AND" => array("user_id" => $userID, "token" => $userToken)));

        if (!$userID || !$userToken) {
            return false;
        }

        if ($get_user['token'] && $get_user['token'] == $userToken) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserIP()
    {
        $get_ips = getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?:
            getenv('HTTP_FORWARDED_FOR') ?:
            getenv('HTTP_FORWARDED') ?:
            getenv('REMOTE_ADDR');

        $ip_list = explode(",", $get_ips);
        $ip = trim($ip_list[0]);
        return $ip;
    }

    public function toSeconds($timstring)
    {
        $replaceam = str_replace(" am", "", $timstring);
        $replaceam = str_replace(" pm", "", $replaceam);

        $times = explode(":", $replaceam);
        $hour = ($times[0] * 60 * 60);
        $minutes = ($times[1] * 60);

        return ($hour + $minutes);
    }


    public function updatePatientStatus($data)
    {
        $this->ci->db->insert("patient_updates", $data);
        $this->markPatientChecked($data['patient_id'], $data['user_id']);
    }

    public function getUserDetails($userId)
    {
        $user = $this->ci->db->get("users", "*", array("user_id" => $userId));

        $user['user_department_raw'] = $user['user_department'];
        $user['user_role_raw'] = $user['user_role'];

        $user['user_department'] = $this->formatNames($user['user_department']);
        $user['user_role'] = $this->formatNames($user['user_role']);

        if ($user['center_id']) {
            $user['center_id'] = $this->ci->db->get("centers", "*", array("id" => $user['center_id']));

            // user is from Hub
            if ($user['center_id']['is_hub'] == "yes") {
                $user['is_hub_user'] = true;
            } else {
                $user['is_hub_user'] = false;
            }

            // user is from spoke
            if ($user['center_id']['is_spoke'] == "yes") {
                $user['is_spoke_user'] = true;
            } else {
                $user['is_spoke_user'] = false;
            }

            // user is from center
            if ($user['center_id']['is_center'] == "yes") {
                $user['is_center_user'] = true;
            } else {
                $user['is_center_user'] = false;
            }
        }
        if ($user['online_status'] == "1") {
            $user['online_status'] = "Online";
        } else {
            $user['online_status'] = "Offline";
        }
        unset($user['password']);
        return $user;
    }

    public function getUserDetailsBasic($userId)
    {
        $user = $this->ci->db->get("users", array("user_id", "user_department", "user_role", "fullname"), array("user_id" => $userId));
        $user['user_department'] = $this->formatNames($user['user_department']);
        $user['user_role'] = $this->formatNames($user['user_role']);
        return $user;
    }

    public function getLastMessageFromPatientConversations($userId, $patientId)
    {
        $lastMessageData = array();
        $lastMessageQuery = "SELECT id, user_id, other_user_id, firebase_id, last_message, already_read, last_message_at FROM conversations WHERE type = 'patient' AND patient_id = " . $patientId . " AND (user_id = " . $userId . " OR other_user_id = " . $userId . ") ORDER by id DESC LIMIT 1 ;";
        $lastMessage = $this->ci->db->query($lastMessageQuery)->fetchAll();
        foreach ($lastMessage[0] as $k => $v) {
            if (is_int($k)) {
                unset($lastMessage[0][$k]);
            }
        }
        if (isset($lastMessage[0]) && isset($lastMessage[0]['id'])) {
            $lastMessageData = $lastMessage[0];

            if ($lastMessage[0]['user_id'] == $userId) {
                $lastMessageData['user'] = $this->getUserDetailsBasic($lastMessage[0]['other_user_id']);
            }
            if ($lastMessage[0]['other_user_id'] == $userId) {
                $lastMessageData['user'] = $this->getUserDetailsBasic($lastMessage[0]['user_id']);
            }

            if ($lastMessage[0]['already_read'] == "1") {
                $lastMessageData['last_message_read'] = true;
            } else {
                $lastMessageData['last_message_read'] = false;
            }
        }
        return $lastMessageData;
    }

    public function addNotification($data)
    {
        $insert_notification = $this->ci->db->insert("notifications", $data);
        return $insert_notification;
    }

    public function getLatLongfromIP()
    {
        $ip = $this->getUserIP();
        $res = file_get_contents('http://freegeoip.net/json/' . $ip);
        $res = json_decode($res);
        return $res;
    }

    public function getIPtolocation($request, $response, $args)
    {
        $ip = $this->getUserIP();
        $res = file_get_contents('http://freegeoip.net/json/' . $ip);
        $res = json_decode($res);
        $latitude = $res->latitude;
        $longitude = $res->longitude;

        $googleAPI = $this->ci->googlemaps;
        $mapsapiKey = $googleAPI['apiKey'];

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latitude . "," . $longitude . "&key=" . $mapsapiKey;

        $data = @file_get_contents($url);
        $location = json_decode($data);
        $geoResults = [];
        foreach ($location->results as $result) {
            $geoResult = [];
            $geoResult['latitude'] = $latitude;
            $geoResult['longitude'] = $longitude;
            $geoResult['formatted_address'] = $result->formatted_address;
            foreach ($result->address_components as $address) {
                if ($address->types[0] == 'country') {
                    $geoResult['country'] = $address->long_name;
                }
                if ($address->types[0] == 'administrative_area_level_1') {
                    $geoResult['state'] = $address->long_name;
                }
                if ($address->types[0] == 'administrative_area_level_2') {
                    $geoResult['county'] = $address->long_name;
                }
                if ($address->types[0] == 'locality') {
                    $geoResult['city'] = $address->long_name;
                }
                if ($address->types[0] == 'postal_code') {
                    $geoResult['postal_code'] = $address->long_name;
                }
                if ($address->types[0] == 'route') {
                    $geoResult['route'] = $address->long_name;
                }
                if ($address->types[0] == 'sub_locality') {
                    $geoResult['locality'] = $address->long_name;
                }
            }
            $geoResult['formatted_address'] = $result->formatted_address;
            $geoResults[] = $geoResult;
        }

        $result_final = array();
        $result_final['ip_info'] = $res;
        $result_final['location_info'] = $geoResults[0];
        $output = $this->printData("success", $result_final);
        return $response->withJson($output, 200);
    }

    public function checkIfUserHasOneSignalId($userId)
    {
        $user = $this->ci->db->get("users", array("user_id", "onesignal_userid", "chat_notifications"), array("user_id" => $userId));
        if ($user['user_id'] && $user['chat_notifications'] == "1") {
            if ($user['onesignal_userid']) {
                return $user['onesignal_userid'];
            }
        }
    }

    public function patientCheckedbyUser($patientId, $userId, $patientlastUpdated)
    {
        // get last update on the patient
        $getLastPatientUpdate = $this->ci->db->get("patient_updates", "*", array("patient_id" => $patientId, "ORDER" => array(
            "id" => "DESC"
        )));
        if (isset($getLastPatientUpdate['id']) && $getLastPatientUpdate['id']) {
            $getCheckedPatient = $this->ci->db->get("patient_last_viewed", "*", array("AND" => array(
                "user_id" => $userId,
                "patient_id" => $patientId,
                "last_update_id" => $getLastPatientUpdate['id'],
            )));

            if (isset($getCheckedPatient['id']) && $getCheckedPatient['id']) {
                $patientChecked = true;
            } else {
                $patientChecked = false;
            }
        } else {
            $patientChecked = true;
        }
        return $patientChecked;
    }

    public function markPatientChecked($patientId, $userId)
    {
        $getLastPatientUpdate = $this->ci->db->get("patient_updates", "*", array("patient_id" => $patientId, "ORDER" => array(
            "id" => "DESC"
        )));

        if (isset($getLastPatientUpdate['id']) && $getLastPatientUpdate['id']) {

            // Check If Exists
            $getCheckedPatient = $this->ci->db->get("patient_last_viewed", "*", array("AND" => array(
                "user_id" => $userId,
                "patient_id" => $patientId,
            )));

            if (isset($getCheckedPatient['id']) && $getCheckedPatient['id']) {
                $this->ci->db->update("patient_last_viewed", array(
                    "patient_id" => $patientId,
                    "user_id" => $userId,
                    "last_update_id" => $getLastPatientUpdate['id'],
                    "last_checked" => date("Y-m-d: H:i:s")
                ), array("id" => $getCheckedPatient['id']));
            } else {
                $this->ci->db->insert("patient_last_viewed", array(
                    "patient_id" => $patientId,
                    "user_id" => $userId,
                    "last_update_id" => $getLastPatientUpdate['id'],
                    "last_checked" => date("Y-m-d: H:i:s")
                ));
            }
        }
    }

    public function getOneSignalIdsOfTheUsers($patientId)
    {
        $getPatient = $this->ci->db->get("patients", array("center_id"), array("id" => $patientId));

        $checkCenter = $this->ci->db->get("centers", array("id", "center_name", "short_name", "center_location", "is_hub", "main_hub"), array("id" => $getPatient['center_id']));

        $getHub = array();
        $getHub = $this->ci->db->get("centers", array("id"), array("id" => $checkCenter['main_hub']));
        if ($checkCenter['is_hub'] == "yes") {
            $getHub['id'] = $checkCenter['id'];
        }
        // Get All Online Users from Hub
        $getOneSignalIDsOfAllUsersFromHub = $this->ci->db->select(
            "users",
            array("user_id", "onesignal_userid", "phone_number"),
            array("center_id" => $getHub['id'])
        );

        $pushIDs = array();
        $mobileNumbers = array();
        if ($checkCenter['is_hub'] == "yes") {
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        } else {
            $getOneSignalIDsOfAllUsersFromSpoke = $this->ci->db->select("users", array("user_id", "onesignal_userid", "phone_number"), array("center_id" => $getPatient['center_id']));
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
            foreach ($getOneSignalIDsOfAllUsersFromSpoke as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        }
        return array(
            "pushIDs" => $pushIDs,
            "mobileNumbers" => $mobileNumbers,
        );
    }
    public function getOneSignalIdsOfTheRadioWithoutDiagonisUsers($patientId)
    {
        $getPatient = $this->ci->db->get("patients", array("center_id"), array("id" => $patientId));
        $checkCenter = $this->ci->db->get("centers", array("id", "center_name", "short_name", "center_location", "is_hub", "main_hub"), array("id" => $getPatient['center_id']));

        $getHub = array();
        $getHub = $this->ci->db->get("centers", array("id"), array("id" => $checkCenter['main_hub']));
        if ($checkCenter['is_hub'] == "yes") {
            $getHub['id'] = $checkCenter['id'];
        }

        // Get All Online Users from Hub
        $getOneSignalIDsOfAllUsersFromHub = $this->ci->db->select(
            "users",
            array("user_id", "onesignal_userid", "phone_number"),
            array("AND" => array(
                "center_id" => $getHub['id'],
                "user_department[!]" => "radio_diagnosis",
            ))
        );

        $pushIDs = array();
        $mobileNumbers = array();
        if ($checkCenter['is_hub'] == "yes") {
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        } else {
            $getOneSignalIDsOfAllUsersFromSpoke = $this->ci->db->select("users", array("user_id", "onesignal_userid", "phone_number"), array(
                "AND" => array(
                    "center_id" => $getPatient['center_id'],
                    "user_department[!]" => "radio_diagnosis",
                ),
            ));
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
            foreach ($getOneSignalIDsOfAllUsersFromSpoke as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        }
        return array(
            "pushIDs" => $pushIDs,
            "mobileNumbers" => $mobileNumbers,
        );
    }

    public function getOneSignalIdsOfTheRadioDiagonisUsers($patientId)
    {
        $getPatient = $this->ci->db->get("patients", array("center_id"), array("id" => $patientId));
        $checkCenter = $this->ci->db->get("centers", array("id", "center_name", "short_name", "center_location", "is_hub", "main_hub"), array("id" => $getPatient['center_id']));

        $getHub = array();
        $getHub = $this->ci->db->get("centers", array("id"), array("id" => $checkCenter['main_hub']));
        if ($checkCenter['is_hub'] == "yes") {
            $getHub['id'] = $checkCenter['id'];
        }

        // Get All Online Users from Hub
        $getOneSignalIDsOfAllUsersFromHub = $this->ci->db->select(
            "users",
            array("user_id", "onesignal_userid", "phone_number"),
            array("AND" => array(
                "center_id" => $getHub['id'],
                "user_department" => "radio_diagnosis",
            ))
        );

        $pushIDs = array();
        $mobileNumbers = array();
        if ($checkCenter['is_hub'] == "yes") {
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        } else {
            $getOneSignalIDsOfAllUsersFromSpoke = $this->ci->db->select("users", array("user_id", "onesignal_userid", "phone_number"), array(
                "AND" => array(
                    "center_id" => $getPatient['center_id'],
                    "user_department" => "radio_diagnosis",
                ),
            ));
            foreach ($getOneSignalIDsOfAllUsersFromHub as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
            foreach ($getOneSignalIDsOfAllUsersFromSpoke as $user) {
                $pushIDs[] = $user['onesignal_userid'];
                $mobileNumbers[] = $user['phone_number'];
            }
        }
        return array(
            "pushIDs" => $pushIDs,
            "mobileNumbers" => $mobileNumbers,
        );
    }

    public function getOneSignalTokens($request, $response, $args)
    {
        // $data = $this->getOneSignalIdsOfTheRadioWithoutDiagonisUsers(48);

        // $numebrsList = "+919815066990<+919888948964<+919855012233<+918146605941<+918920081311";
        // $numbers = explode("<", $numebrsList);
        // $pushData = array();
        // foreach($numbers as $number){
        //     $callData = array();
        //     $callData['to'] = $number;
        //     $data = $this->createCall($callData);
        //     $pushData[] = $data;
        // }

        // // $smsData['to'] = "+919888948964<+918920081311<+919814696741";
        // $callData['to'] = "+919815066990<+91919888948964<+919855012233<+918146605941<+918920081311";
        // // $smsData['message'] = "Testing SMS Service Again";
        // $data = $this->createCall($callData);

        // $output = $this->printData("success", $pushData);
        // return $response->withJson($output, 200);
    }
}