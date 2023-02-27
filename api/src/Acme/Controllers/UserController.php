<?php

namespace Acme\Controllers;

class UserController extends BaseController { 
    
    public function updateNotificationsSettings($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
            
            $data = $request->getParsedBody();
            foreach($data as $key=>$val){
                $data[$key] = $this->cleanValue($val);
            }
            
            $get_settings = $this->ci->db->get("notification_settings", "*", array("user_id" => $header_userId[0]));
            if(!isset($get_settings['user_id'])){
                $this->ci->db->insert("notification_settings", array("user_id" => $header_userId[0]));
            }
            
            // Update the settings
            $this->ci->db->update("notification_settings", array($data['notification_type'] => $data['notification_value']), array("user_id" => $header_userId[0]));
            
            $get_settings = $this->ci->db->get("notification_settings", "*", array("user_id" => $header_userId[0]));
            $settings_array = array("thumbs_up","new_comments","new_messages","help_request","best_answer","answer_received","new_friend_request", "new_rank");
            
            foreach($settings_array as $setting){
                if($get_settings[$setting] == "1"){
                    $get_settings[$setting] = true;
                }else{
                    $get_settings[$setting] = false;
                }
            }
            
            $output = $this->printData("success", $get_settings);
            return $response->withJson($output, 200);
            
        }else{
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
    
    public function getNotificationsSettings($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
                
            $get_settings = $this->ci->db->get("notification_settings", "*", array("user_id" => $header_userId[0]));
            if(!isset($get_settings['user_id'])){
                $this->ci->db->insert("notification_settings", array("user_id" => $header_userId[0]));
            }
            
            $get_settings = $this->ci->db->get("notification_settings", "*", array("user_id" => $header_userId[0]));
            $settings_array = array("thumbs_up","new_comments","new_messages","help_request","best_answer","answer_received","new_friend_request", "new_rank");
            
            foreach($settings_array as $setting){
                if($get_settings[$setting] == "1"){
                    $get_settings[$setting] = true;
                }else{
                    $get_settings[$setting] = false;
                }
            }
            
            $output = $this->printData("success", $get_settings);
            return $response->withJson($output, 200);
            
        }else{
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
    
    public function userProfile($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
            $user = $this->getUserDetails($header_userId[0]);
            if ($user['user_id']) {           
                $output = $this->printData("success", $user);
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData("error", "USER_NOT_FOUND");
                return $response->withJson($output, 403);
            }
        }else{
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
    
    public function changeOnlineStatus($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
           $data = $request->getParsedBody();
           foreach($data as $key=>$val){
               $data[$key] = $this->cleanValue($val);
           }
           // Check current status of the user
           $get_user = $this->ci->db->get("users", array("user_id", "online_status"), array("user_id" => $header_userId[0]));
           if(isset($get_user['user_id'])){
               if($get_user['online_status'] == "1"){                   
                   $this->ci->db->update("users", array("online_status" => "0"), array("user_id" => $header_userId[0]));
                   $user = $this->getUserDetails($header_userId[0]);
                   $output = $this->printData("error", array("status" => "Offline", "user_data" => $this->getUserDetails($header_userId[0])));
                   return $response->withJson($output, 200);
               }else{
                  $this->ci->db->update("users", array("online_status" => "1"), array("user_id" => $header_userId[0]));
                   $user = $this->getUserDetails($header_userId[0]);
                   $output = $this->printData("error", array("status" => "Online", "user_data" => $this->getUserDetails($header_userId[0])));                   
                   return $response->withJson($output, 200);
               }
           }else{
               $output = $this->printData("error", array("message" => "User not found"));
                return $response->withJson($output, 403);
           }
           
        }else{
           $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
           return $response->withJson($output, 403);
        }
    }
    
    
    public function editProfile($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
           $user_id = $header_userId[0];
           $data = $request->getParsedBody();
           foreach($data as $key=>$val){
               $data[$key] = $this->cleanValue($val);
           }
           
           $errors = array();
           if (!isset($data['first_name']) || $data['first_name'] == "") {
                $errors[] = "First name is required";
           }
           if (!isset($data['last_name']) || $data['last_name'] == "") {
                    $errors[] = "Last name is required";
            }
            if (!isset($data['email_address']) || $data['email_address'] == "") {
                $errors[] = "Email address is required";
           }
            if (!isset($data['phone_number']) || $data['phone_number'] == "") {
                $errors[] = "Phone number is required";
           }
           if ($errors) {
                $output = $this->printData("error", $errors[0]);
                return $response->withJson($output, 403);
            } else {
                $data['fullname'] = $data['first_name']." ".$data['last_name'];

                if($data['password']){
                    unset($data['password']);
                }   
                
                $update_data = $this->ci->db->update("users", $data, array("user_id" => $user_id));
                $user = $this->getUserDetails($header_userId[0]);

                $output = $this->printData("success", $user);
                return $response->withJson($output, 200);
            }          
        }else{
           $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
           return $response->withJson($output, 403);
        }
    }
    
    public function changePassword($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        
        if($this->validateUser($header_userId[0], $header_userToken[0])){
           $user_id = $header_userId[0];
           $data = $request->getParsedBody();
           foreach($data as $key=>$val){
               $data[$key] = $this->cleanValue($val);
           }
           $errors = array();
           if (!isset($data['old_password']) || $data['old_password'] == "") {
                $errors[] = "Old password is required";
           }
           if (!isset($data['new_password']) || $data['new_password'] == "") {
                $errors[] = "New password is required";
           }
           if (!isset($data['repeat_password']) || $data['repeat_password'] == "") {
                $errors[] = "Repeat password is required";
           }
           if ($data['new_password']) {
                if (strlen(trim($data['new_password'])) < 6) {
                    $errors[] = "Password needs to be atleast of 6 characters";
                }
           }
           if ($errors) {
                $output = $this->printData("error", $errors[0]);
                return $response->withJson($output, 403);
            } else {
                $old_password = $data['old_password'];
                $new_password = $data['new_password'];
                $repeat_password = $data['repeat_password'];
                
                $user_data = $this->ci->db->get("users", "*", array("user_id" => $header_userId[0]));
                if (md5($old_password) !== $user_data['password']) {
                    $output = $this->printData("error", "Old Password doesn't match.");
                    return $response->withJson($output, 403);
                    
                } elseif ($new_password == $old_password && $old_password) {
                    $output = $this->printData("error", "New password cannot be same as the old password");
                    return $response->withJson($output, 403);
                    
                } elseif ($new_password !== $repeat_password) {
                    $output = $this->printData("error", "Both passwords doesn't match");
                    return $response->withJson($output, 403);
                    
                } else {
                    $data = array();
                    $data['password'] = md5($new_password);
                    $update_data = $this->ci->db->update("users", $data, array("user_id" => $user_id));
                    if($update_data) {
                        $output = $this->printData("success", "Password was successfully changed.");
                        return $response->withJson($output, 200);
                    }
                }
            }
        }else{
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
            
    public function checkUserSession($request, $response, $args) {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if($this->validateUser($header_userId[0], $header_userToken[0])){
            $output = $this->printData("success", true);
            return $response->withJson($output, 200);
        }else{
            $output = $this->printData("error", false);
            return $response->withJson($output, 403);
        }
    }
}

