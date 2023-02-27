<?php

namespace Acme\Controllers;

class ConversationsController extends BaseController
{
    public function fetchAllOnlineUsers($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            // get the current hosptial of the patient
            $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));

            $getHubIdFromCenter = $this->ci->db->get("centers", array("main_hub", "id"), array("id" => $getUserCenterId['center_id']));


            $getAllCenters = array();
            $getAllCenters[] = $this->ci->db->get("centers", "*", array("id" => $getHubIdFromCenter['main_hub']));
                
            $onlineUsers = array();
            $getCenters = $this->ci->db->select("centers", array("id", "center_name", "is_hub"), array("AND" => array("status" => "1", "main_hub" => $getHubIdFromCenter['main_hub'])));
            foreach($getCenters as $center){
                $getAllCenters[] = $center;
            }

            foreach ($getAllCenters as $ckey => $cVal) {

                if ($cVal['is_hub'] == "yes") {
                    $getAllCenters[$ckey]['center_name'] = $cVal['center_name'] . "(Hub)";
                } else {
                    $getAllCenters[$ckey]['center_name'] = $cVal['center_name'] . "(Spoke)";
                }

                $getCenterEmployees = $this->ci->db->select("users", array("user_id", "fullname", "user_department", "user_role", "center_id", "onesignal_userid", "online_status", "phone_number"), array("AND" => array("user_id[!]" =>  $header_userId[0],"online_status" => "1", "center_id" => $cVal['id'])));
                
                foreach ($getCenterEmployees as $ekey => $eValue) {
                    $getCenterEmployees[$ekey]['user_department'] = $this->formatNames($eValue['user_department']);
                    $getCenterEmployees[$ekey]['user_role'] = $this->formatNames($eValue['user_role']);
                }
                $getAllCenters[$ckey]['online_users'] = $getCenterEmployees;

                if (count($getCenterEmployees) == 0) {
                    unset($getAllCenters[$ckey]);
                }
            }

            $output = $this->printData("success", array_values($getAllCenters));
            return $response->withJson($output, 200);

        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }

    }

    public function createConversation($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            if (isset($args['userId']) && $args['userId']) {

                $patientId = $args['patientId'];

                if ($args['userId'] == $header_userId[0]) {
                    $output = $this->printData("error", array("message" => "You cannot send message to yourself."));
                    return $response->withJson($output, 403);
                }

                $check_if_exists = $this->ci->db->query("SELECT * FROM conversations WHERE ((user_id = " . $args['userId'] . " AND other_user_id = " . $header_userId[0] . ") OR (user_id = " . $header_userId[0] . " AND other_user_id = " . $args['userId'] . ")) AND type='patient' AND patient_id = " . $patientId)->fetchAll();
                if ($check_if_exists[0]['id']) {
                    foreach ($check_if_exists[0] as $k => $v) {
                        if (is_int($k)) {
                            unset($check_if_exists[0][$k]);
                        }
                    }
                    $output = $this->printData("error", $check_if_exists[0]);
                    return $response->withJson($output, 200);
                } else {
                    $data = array();
                    $data['type'] = 'patient';
                    $data['user_id'] = $header_userId[0];
                    $data['other_user_id'] = $args['userId'];
                    $data['patient_id'] = $patientId;
                    $data['firebase_id'] = "conversation_" . $header_userId[0] . "_" . $args['userId'] . "_patient_" . $patientId;
                    $insert_conversation = $this->ci->db->insert("conversations", $data);
                    if ($insert_conversation) {
                        $get_conversation = $this->ci->db->get("conversations", "*", array("id" => $insert_conversation));
                        $output = $this->printData("error", $get_conversation);
                        return $response->withJson($output, 200);
                    }
                }
            } else {
                $output = $this->printData("error", array("message" => "USERID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function createInternalConversation($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            if (isset($args['userId']) && $args['userId']) {

                if ($args['userId'] == $header_userId[0]) {
                    $output = $this->printData("error", array("message" => "You cannot send message to yourself."));
                    return $response->withJson($output, 403);
                }
                $check_if_exists = $this->ci->db->query("SELECT * FROM conversations WHERE ((user_id = " . $args['userId'] . " AND other_user_id = " . $header_userId[0] . ") OR (user_id = " . $header_userId[0] . " AND other_user_id = " . $args['userId'] . ")) AND type = 'internal'")->fetchAll();
                if ($check_if_exists[0]['id']) {
                    foreach ($check_if_exists[0] as $k => $v) {
                        if (is_int($k)) {
                            unset($check_if_exists[0][$k]);
                        }
                    }
                    $output = $this->printData("error", $check_if_exists[0]);
                    return $response->withJson($output, 200);
                } else {
                    $data = array();
                    $data['type'] = 'internal';
                    $data['user_id'] = $header_userId[0];
                    $data['other_user_id'] = $args['userId'];
                    $data['firebase_id'] = "internal_conversation_" . $header_userId[0] . "_" . $args['userId'];
                    $insert_conversation = $this->ci->db->insert("conversations", $data);
                    if ($insert_conversation) {
                        $get_conversation = $this->ci->db->get("conversations", "*", array("id" => $insert_conversation));
                        $output = $this->printData("error", $get_conversation);
                        return $response->withJson($output, 200);
                    }
                }
            } else {
                $output = $this->printData("error", array("message" => "USERID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function sendPushMessageChat($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $get_conversation = $this->ci->db->get("conversations", "*", array("firebase_id" => $args['chatId']));
            if ($get_conversation['user_id'] == $header_userId[0]) {
                $other_userId = $get_conversation['other_user_id'];
            } else {
                $other_userId = $get_conversation['user_id'];
            }

            if (isset($get_conversation['patient_id']) && $get_conversation['patient_id']) {
                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $get_conversation['patient_id']));
                $pushMessage = $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . "): Chat Message";
            } else {
                $pushMessage = "New Chat Message";
            }

            $checkOneSignalId = $this->checkIfUserHasOneSignalId($other_userId);
            if ($get_conversation['chat_left'] == "0") {
                if ($checkOneSignalId) {
                    $data_push = array();
                    $data_push['url'] = "snetchd://strokenetchandigarh.com/chat/" . $args['chatId'];
                    $data_push['message'] = $data['last_message'];
                    $data_push['title'] = $pushMessage;
                    $data_push['devices'][0] = $checkOneSignalId;
                    $this->sendPush($data_push);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function createLastMessageInConversation($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            if (isset($args['chatId']) && $args['chatId']) {

                $data = $request->getParsedBody();
                foreach ($data as $key => $val) {
                    $data[$key] = $this->cleanValue($val);
                }

                $message_data = array();
                $message_data['last_message'] = $data['last_message'];
                $message_data['already_read'] = "0";
                $message_data['last_message_by'] = $header_userId[0];

                if (isset($data['last_message']) && $data['last_message'] !== "") {
                    $message_data['last_message_at'] = date("Y-m-d H:i:s");
                } else {
                    $message_data['last_message_at'] = null;
                }
                $get_conversation = $this->ci->db->get("conversations", "*", array("firebase_id" => $args['chatId']));

                //Check if the conversation  is left
                if ($get_conversation['chat_left'] && $get_conversation['chat_left'] == "1") {
                    if ($get_conversation['chat_left_user_id'] == $header_userId[0]) {
                        $this->ci->db->update("conversations", array("status" => "1", "chat_left" => "0", "chat_left_user_id" => null), array("firebase_id" => $args['chatId']));
                    }
                }

                $get_conversation = $this->ci->db->get("conversations", "*", array("firebase_id" => $args['chatId']));

                if (isset($get_conversation['patient_id']) && $get_conversation['patient_id']) {
                    // Check if any user is blocked from either side
                    $check_blocked_users = $this->ci->db->query("SELECT * FROM blocked_users WHERE ((user_id = " . $get_conversation['user_id'] . " AND block_user_id = " . $get_conversation['other_user_id'] . ") OR (user_id = " . $get_conversation['other_user_id'] . " AND block_user_id = " . $get_conversation['user_id'] . ")) AND patient_id = " . $get_conversation['patient_id'] . " LIMIT 1;")->fetchAll();
                } else {
                    // Check if any user is blocked from either side
                    $check_blocked_users = $this->ci->db->query("SELECT * FROM blocked_users WHERE ((user_id = " . $get_conversation['user_id'] . " AND block_user_id = " . $get_conversation['other_user_id'] . ") OR (user_id = " . $get_conversation['other_user_id'] . " AND block_user_id = " . $get_conversation['user_id'] . ")) LIMIT 1;")->fetchAll();
                }

                if ($check_blocked_users[0]['id']) {

                    $quickChecks = array();
                    if ($get_conversation['chat_left'] == "1") {
                        $quickChecks['chat_left'] = true;
                    } else {
                        $quickChecks['chat_left'] = false;
                    }

                    if ($get_conversation['chat_blocked'] == "1") {
                        $quickChecks['chat_blocked'] = true;
                    } else {
                        $quickChecks['chat_blocked'] = false;
                    }

                    if ($get_conversation['status'] == "1") {
                        $quickChecks['status'] = true;
                    } else {
                        $quickChecks['status'] = false;
                    }

                    $output = $this->printData("success", array("message" => "UNABLE_TO_MESSAGE", "quick_checks" => $quickChecks));
                    return $response->withJson($output, 200);
                } else {

                    if ($get_conversation['chat_blocked'] !== "1") {

                        $update_conversation = $this->ci->db->update("conversations", $message_data, array("firebase_id" => $args['chatId']));
                        if ($get_conversation['user_id'] == $header_userId[0]) {
                            $other_userId = $get_conversation['other_user_id'];
                        } else {
                            $other_userId = $get_conversation['user_id'];
                        }

                        // Check if the chat is blocked or left
                        $quickChecks = array();
                        if ($get_conversation['chat_left'] == "1") {
                            $quickChecks['chat_left'] = true;
                        } else {
                            $quickChecks['chat_left'] = false;
                        }

                        if ($get_conversation['chat_blocked'] == "1") {
                            $quickChecks['chat_blocked'] = true;
                        } else {
                            $quickChecks['chat_blocked'] = false;
                        }

                        if ($get_conversation['status'] == "1") {
                            $quickChecks['status'] = true;
                        } else {
                            $quickChecks['status'] = false;
                        }

                        $output = $this->printData("success", array("message" => "UPDATED", "quick_checks" => $quickChecks));
                        return $response->withJson($output, 200);
                    } else {

                        // Check if the chat is blocked or left
                        $quickChecks = array();
                        if ($get_conversation['chat_left'] == "1") {
                            $quickChecks['chat_left'] = true;
                        } else {
                            $quickChecks['chat_left'] = false;
                        }

                        if ($get_conversation['chat_blocked'] == "1") {
                            $quickChecks['chat_blocked'] = true;
                        } else {
                            $quickChecks['chat_blocked'] = false;
                        }

                        if ($get_conversation['status'] == "1") {
                            $quickChecks['status'] = true;
                        } else {
                            $quickChecks['status'] = false;
                        }

                        $output = $this->printData("success", array("message" => "UNABLE_TO_MESSAGE", "quick_checks" => $quickChecks));
                        return $response->withJson($output, 400);

                    }

                }

            } else {
                $output = $this->printData("error", array("message" => "USERID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function getConversation($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            if (isset($args['chatId']) && $args['chatId']) {

                $updateReadMessages = $this->ci->db->update("conversations", array("already_read" => "1"), array("firebase_id" => $args['chatId']));

                $get_conversation = $this->ci->db->get("conversations", "*", array("firebase_id" => $args['chatId']));

                if ($get_conversation['chat_left'] == "1") {
                    $get_conversation['chat_left'] = true;
                } else {
                    $get_conversation['chat_left'] = false;
                }

                if ($get_conversation['chat_blocked'] == "1") {
                    $get_conversation['chat_blocked'] = true;
                } else {
                    $get_conversation['chat_blocked'] = false;
                }

                if ($get_conversation['status'] == "1") {
                    $get_conversation['status'] = true;
                } else {
                    $get_conversation['status'] = false;
                }

                if ($get_conversation['last_message_by'] == $header_userId[0]) {
                    $get_conversation['already_read'] = true;
                } else {
                    if ($get_conversation['already_read'] == "1") {
                        $get_conversation['already_read'] = true;
                    } else {
                        $get_conversation['already_read'] = false;
                    }
                }

                $user_id = $get_conversation['user_id'];
                $friend_id = $get_conversation['other_user_id'];

                if (isset($get_conversation['patient_id']) && $get_conversation['patient_id']) {
                    $get_patient = $this->ci->db->get("patients", array("id", "name", "patient_code"), array("id" => $get_conversation['patient_id']));
                    $get_conversation['patient_info'] = $get_patient;
                }

                $get_user = $this->ci->db->get("users", array("user_id", "fullname", "user_department", "user_role", "center_id"), array("user_id" => $user_id));
                $get_friend = $this->ci->db->get("users", array("user_id", "fullname", "user_department", "user_role", "center_id"), array("user_id" => $friend_id));

                // if($get_user){
                //     if($get_user['user_image']){
                //         $get_user['user_image'] = main_url."/uploads/thumb/compress.php?zc=1&q=100&w=200&h=200&src=".$get_user['user_image'];
                //     }else{
                //         $get_user['user_image'] = false;
                //     }
                // }
                // if($get_friend){
                //     if($get_friend['user_image']){
                //         $get_friend['user_image'] = main_url."/uploads/thumb/compress.php?zc=1&q=100&w=200&h=200&src=".$get_friend['user_image'];
                //     }else{
                //         $get_friend['user_image'] = false;
                //     }
                // }

                if ($header_userId[0] == $user_id) {
                    $get_conversation['friend_info'] = $get_friend;
                    $get_conversation['fullname'] = $get_friend['fullname'];
                    $get_conversation['user_role'] = $this->formatNames($get_friend['user_role']);
                } else {
                    $get_conversation['friend_info'] = $get_user;
                    $get_conversation['fullname'] = $get_user['fullname'];
                    $get_conversation['user_role'] = $this->formatNames($get_user['user_role']);
                }

                if (isset($get_conversation['patient_id']) && $get_conversation['patient_id']) {
                    // Check if any user is blocked from either side
                    $check_blocked_users = $this->ci->db->query("SELECT * FROM blocked_users WHERE ((user_id = " . $user_id . " AND block_user_id = " . $friend_id . ") OR (user_id = " . $friend_id . " AND block_user_id = " . $user_id . ")) AND patient_id = " . $get_conversation['patient_id'] . " LIMIT 1;")->fetchAll();

                } else {
                    // Check if any user is blocked from either side
                    $check_blocked_users = $this->ci->db->query("SELECT * FROM blocked_users WHERE ((user_id = " . $user_id . " AND block_user_id = " . $friend_id . ") OR (user_id = " . $friend_id . " AND block_user_id = " . $user_id . ")) LIMIT 1;")->fetchAll();
                }
                if ($check_blocked_users[0]['id']) {
                    $get_conversation['blocked_conversation'] = true;

                    if ($check_blocked_users[0]['user_id'] == $header_userId[0]) {
                        $get_conversation['can_unblock'] = true;
                        $get_conversation['blocker_id'] = $check_blocked_users[0]['user_id'];
                    } else {
                        $get_conversation['can_unblock'] = false;
                    }
                } else {
                    $get_conversation['blocked_conversation'] = false;
                }

                $output = $this->printData("success", $get_conversation);
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData("error", array("message" => "USERID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function deleteConversation($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            if (isset($args['chatId']) && $args['chatId']) {

                $leaveConversation = $this->ci->db->update("conversations", array(
                    "status" => "0",
                    "chat_left" => "1",
                    "chat_left_user_id" => $header_userId[0],
                    "chat_blocked" => "0",
                    "chat_block_user_id" => null,
                ), array("firebase_id" => $args['chatId']));

                $output = $this->printData("success", array("message" => "DELETED"));
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData("error", array("message" => "CHATID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
    

    public function getConversations($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            $final_conversations = array();
            $final_conversations['active'] = array();
            $final_conversations['archived'] = array();

            $get_conversations = $this->ci->db->select("conversations", "*", array("AND" => array(
                "conversation_closed" => "0",
                "OR" => array(
                    "user_id" => $header_userId[0],
                    "other_user_id" => $header_userId[0],
                ),
            ), "ORDER" => array("last_message_at" => "DESC")));

            foreach ($get_conversations as $key => $val) {
                $user_id = $val['user_id'];
                $friend_id = $val['other_user_id'];

                if ($val['last_message_by'] == $header_userId[0]) {
                    $get_conversations[$key]['already_read'] = true;
                } else {
                    if ($val['already_read'] == "1") {
                        $get_conversations[$key]['already_read'] = true;
                    } else {
                        $get_conversations[$key]['already_read'] = false;
                    }
                }

                if (isset($val['patient_id']) && $val['patient_id']) {
                    $get_patient = $this->ci->db->get("patients", array("id", "name", "patient_code"), array("id" => $val['patient_id']));
                    $get_conversations[$key]['patient_info'] = $get_patient;
                }

                $get_user = $this->ci->db->get("users", array("user_id", "fullname", "user_department", "user_role", "center_id"), array("user_id" => $user_id));
                $get_friend = $this->ci->db->get("users", array("user_id", "fullname", "user_department", "user_role", "center_id"), array("user_id" => $friend_id));

                if ($header_userId[0] == $user_id) {
                    $get_conversations[$key]['friend_info'] = $get_friend;
                    $get_conversations[$key]['fullname'] = $get_friend['fullname'];
                    $get_conversations[$key]['username'] = $get_friend['fullname'];
                    $get_conversations[$key]['profile_type'] = $get_friend['user_role'];
                    $get_conversations[$key]['friend_info']['username'] = $get_friend['fullname'];

                } else {
                    $get_conversations[$key]['friend_info'] = $get_user;
                    $get_conversations[$key]['fullname'] = $get_user['fullname'];
                    $get_conversations[$key]['username'] = $get_user['fullname'];
                    $get_conversations[$key]['profile_type'] = $get_user['user_role'];

                    $get_conversations[$key]['friend_info']['username'] = $get_user['fullname'];
                }

                if ($get_conversations[$key]['status'] == "0" || $get_conversations[$key]['chat_left'] == "1" || $get_conversations[$key]['chat_blocked'] == "1") {
                    $final_conversations['archived'][] = $get_conversations[$key];
                } else {
                    $final_conversations['active'][] = $get_conversations[$key];
                }
            }

            $output = $this->printData("success", $final_conversations);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function checkTotalUnreadMessages($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            $countTotalUnreadMessages = $this->ci->db->count("conversations", array("AND" => array(
                "AND" => array(
                    "OR" => array(
                        "user_id" => $header_userId[0],
                        "other_user_id" => $header_userId[0],
                    ),
                    "last_message_by[!]" => $header_userId[0],
                    "already_read" => "0",
                ),
            ), "ORDER" => array("last_message_at" => "DESC")));

            // echo $this->ci->db->last_query();
            $arrayFinal = array();
            $arrayFinal['total_unread_chats'] = $countTotalUnreadMessages;
            if ($countTotalUnreadMessages == 0) {
                $arrayFinal['has_chats'] = false;
            } else {
                $arrayFinal['has_chats'] = true;
            }

            $output = $this->printData("success", $arrayFinal);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function blockUnblockUser($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }

            if (isset($args['userId']) && $args['userId']) {
                $userId = $args['userId'];

                $check_block_exists = $this->ci->db->get("blocked_users", "*", array(
                    "AND" => array(
                        "user_id" => $header_userId[0],
                        "block_user_id" => $userId,
                        "patient_id" => $data['patient_id'],
                    )));

                if (isset($check_block_exists['id']) && $check_block_exists['id']) {
                    $remove_fromBlockList = $this->ci->db->delete("blocked_users", array("id" => $check_block_exists['id']));

                    // Find conversations that deals with these two users
                    $check_if_exists = $this->ci->db->query("SELECT * FROM conversations WHERE ((user_id = " . $userId . " AND other_user_id = " . $header_userId[0] . ") OR (user_id = " . $header_userId[0] . " AND other_user_id = " . $userId . ")) AND patient_id = " . $data['patient_id'])->fetchAll();

                    if ($check_if_exists[0]['id']) {
                        $this->ci->db->update("conversations", array(
                            "status" => "1",
                            "chat_left" => "0",
                            "chat_left_user_id" => null,
                            "chat_blocked" => "0",
                            "chat_block_user_id" => null,
                        ), array("id" => $check_if_exists[0]['id']));
                    }

                    $output = $this->printData("success", array("message" => "User unblocked successfully!", "blocked" => false));
                    return $response->withJson($output, 200);
                } else {
                    $insert_block = array();
                    $insert_block['user_id'] = $header_userId[0];
                    $insert_block['block_user_id'] = $userId;

                    if (isset($data['reason']) && $data['reason']) {
                        $insert_block['reason'] = $data['reason'];
                    }

                    if (isset($data['patient_id']) && $data['patient_id']) {
                        $insert_block['patient_id'] = $data['patient_id'];
                    }

                    $insert_block['created'] = date("Y-m-d H:i:s");
                    $blockUser = $this->ci->db->insert("blocked_users", $insert_block);
                    if ($blockUser) {

                        // Find conversations that deals with these two users
                        $check_if_exists = $this->ci->db->query("SELECT * FROM conversations WHERE ((user_id = " . $userId . " AND other_user_id = " . $header_userId[0] . ") OR (user_id = " . $header_userId[0] . " AND other_user_id = " . $userId . ")) AND patient_id = " . $data['patient_id'])->fetchAll();
                        if ($check_if_exists[0]['id']) {
                            $this->ci->db->update("conversations", array(
                                "status" => "0",
                                "chat_left" => "0",
                                "chat_left_user_id" => null,
                                "chat_blocked" => "1",
                                "chat_block_user_id" => $header_userId[0],
                            ), array(
                                "id" => $check_if_exists[0]['id'],
                            ));
                        }

                        $output = $this->printData("success", array("message" => "User blocked successfully!", "blocked" => true));
                        return $response->withJson($output, 200);
                    }
                }
            } else {
                $output = $this->printData("success", array("message" => "USERID_REQUIRED"));
                return $response->withJson($output, 403);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

}
