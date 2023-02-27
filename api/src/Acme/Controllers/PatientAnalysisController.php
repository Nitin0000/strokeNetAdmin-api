<?php

namespace Acme\Controllers;

class PatientAnalysisController extends BaseController
{


    // Fetch Online Users
    public function fetchOnlineUsers($patientId, $userId)
    {
        // Check if the patient is from Hub or Spoke
        $getPatient = $this->ci->db->get("patients", array("center_id"), array("id" => $patientId));

        $checkCenter = $this->ci->db->get("centers", array("id", "center_name", "short_name", "center_location", "is_hub", "main_hub"), array("id" => $getPatient['center_id']));

        $getHub = array();
        $getHub = $this->ci->db->get("centers", array("id", "center_name"), array("id" => $checkCenter['main_hub']));
        if ($checkCenter['is_hub'] == "yes") {
            $getHub['id'] = $checkCenter['id'];
        }

        // Get All Online Users from Hub
        $getAllOnlineUsersfromHub = $this->ci->db->select("users", array("user_id", "fullname", "user_department", "user_role", "onesignal_userid", "online_status", "phone_number"), array("AND" => array("user_id[!]" =>  $userId, "center_id" => $getHub['id'], "online_status" => "1")));

        foreach ($getAllOnlineUsersfromHub as $key => $value) {
            $getAllOnlineUsersfromHub[$key]['user_department'] = $this->formatNames($value['user_department']);
            $getAllOnlineUsersfromHub[$key]['user_role'] = $this->formatNames($value['user_role']);

            // Get last message
            $lastMessageQuery = "SELECT id,firebase_id,last_message,already_read,last_message_at FROM conversations WHERE type = 'patient' AND patient_id = " . $patientId . " AND ((user_id = " . $userId . " AND other_user_id = " . $value['user_id'] . ") OR (user_id = " . $value['user_id'] . " AND other_user_id = " . $userId . "));";
            $lastMessage = $this->ci->db->query($lastMessageQuery)->fetchAll();
            foreach ($lastMessage[0] as $k => $v) {
                if (is_int($k)) {
                    unset($lastMessage[0][$k]);
                }
            }
            if (isset($lastMessage[0]) && isset($lastMessage[0]['id'])) {
                $getAllOnlineUsersfromHub[$key]['last_message'] = $lastMessage[0];
                if ($lastMessage[0]['already_read'] == "1") {
                    $getAllOnlineUsersfromHub[$key]['last_message_read'] = true;
                } else {
                    $getAllOnlineUsersfromHub[$key]['last_message_read'] = false;
                }
            }
        }

        $onlineUsers = array();
        if ($checkCenter['is_hub'] == "yes") {
            $onlineUsers["hub_users"] = array(
                "name" => $checkCenter['center_name'] . " (Hub)",
                "online_users" => $getAllOnlineUsersfromHub,
            );
        } else {

            $getAllOnlineUsersfromSpoke = $this->ci->db->select("users", array("user_id", "fullname", "user_department", "user_role", "onesignal_userid", "online_status", "phone_number"), array("AND" => array("user_id[!]" =>  $userId, "center_id" => $getPatient['center_id'], "online_status" => "1")));

            foreach ($getAllOnlineUsersfromSpoke as $key => $value) {
                $getAllOnlineUsersfromSpoke[$key]['user_department'] = $this->formatNames($value['user_department']);
                $getAllOnlineUsersfromSpoke[$key]['user_role'] = $this->formatNames($value['user_role']);

                // Get last message
                $lastMessageQuery = "SELECT id,firebase_id,last_message,already_read,last_message_at FROM conversations WHERE type = 'patient' AND patient_id = " . $patientId . " AND ((user_id = " . $userId . " AND other_user_id = " . $value['user_id'] . ") OR (user_id = " . $value['user_id'] . " AND other_user_id = " . $userId . "));";
                $lastMessage = $this->ci->db->query($lastMessageQuery)->fetchAll();
                foreach ($lastMessage[0] as $k => $v) {
                    if (is_int($k)) {
                        unset($lastMessage[0][$k]);
                    }
                }
                if (isset($lastMessage[0]) && isset($lastMessage[0]['id'])) {
                    $getAllOnlineUsersfromHub[$key]['last_message'] = $lastMessage[0];
                    if ($lastMessage[0]['already_read'] == "1") {
                        $getAllOnlineUsersfromHub[$key]['last_message_read'] = true;
                    } else {
                        $getAllOnlineUsersfromHub[$key]['last_message_read'] = false;
                    }
                }
            }



            $onlineUsers["hub_users"] = array(
                "name" => $getHub['center_name'] . " (Hub)",
                "online_users" => $getAllOnlineUsersfromHub,
            );
            $onlineUsers["spoke_users"] = array(
                "name" => $checkCenter['center_name'] . " (Spoke)",
                "online_users" => $getAllOnlineUsersfromSpoke,
            );
        }

        return $onlineUsers;
    }

    // Add and Remvoe Users frm ONline Users
    public function addRemoveFromOnlineUsers($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $type = $args['type'];

            if ($type == "add") {
                $removeFromOnline = $this->ci->db->delete("online_users", array("AND" => array("user_id" => $header_userId[0], "patient_id" => $patientId)));
                $insertOnlineStatus = $this->ci->db->insert("online_users", array("user_id" => $header_userId[0], "patient_id" => $patientId));
                $output = $this->printData("success", $this->fetchOnlineUsers($patientId, $header_userId[0]));
                return $response->withJson($output, 200);
            } else {
                $removeFromOnline = $this->ci->db->delete("online_users", array("AND" => array("user_id" => $header_userId[0], "patient_id" => $patientId)));
                $output = $this->printData("success", $this->fetchOnlineUsers($patientId, $header_userId[0]));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    // Get Online Users API
    public function getOnlineUsers($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $output = $this->printData("success", $this->fetchOnlineUsers($patientId, $header_userId[0]));
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    // Fetch Comments from Database
    public function fetchComments($patientId)
    {
        $getComments = $this->ci->db->select("comments", "*", array("patient_id" => $patientId, "ORDER" => array("id" => "ASC")));
        foreach ($getComments as $key => $val) {
            $getUser = $this->ci->db->get("users", array("user_id", "fullname"), array("user_id" => $val['user_id']));
            $getComments[$key]['user_id'] = $getUser;
        }
        return $getComments;
    }

    // Get Comments API
    public function getComments($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $output = $this->printData("success", $this->fetchComments($patientId));
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function postCommentPushNotification($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }

            $errors = array();
            if (!isset($data['patient_id']) || $data['patient_id'] == "") {
                $errors[] = "patient_id is required";
            }
            if (!isset($data['message']) || $data['message'] == "") {
                $errors[] = "message is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));

                // Send Push notification to all users who in discussion.
                $getPushIDs = $this->getOneSignalIdsOfTheUsers($data['patient_id']);
                if (count($getPushIDs['pushIDs']) > 0) {
                    $pushData = array();
                    $pushData['title'] = $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . "): New Comment";
                    $pushData['message'] = $data['message'];
                    $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_analysis/' . $data['patient_id'] . "/comments";
                    $pushData['devices'] = $getPushIDs['pushIDs'];
                    $this->sendPush($pushData);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function postComment($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if (!isset($data['patient_id']) || $data['patient_id'] == "") {
                $errors[] = "patient_id is required";
            }
            if (!isset($data['message']) || $data['message'] == "") {
                $errors[] = "message is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $insertCommentData = array();
                $insertCommentData['user_id'] = $header_userId[0];
                $insertCommentData['patient_id'] = $data['patient_id'];
                $insertCommentData['message'] = $this->cleanValue($data['message']);
                $insertCommentData['created'] = date("Y-m-d H:i:s");

                $insertComment = $this->ci->db->insert("comments", $insertCommentData);
                if ($insertComment) {

                    // Global Status
                    $updateData = array(
                        "user_id" => $header_userId[0],
                        "patient_id" => $data['patient_id'],
                        "update_type" => "comment",
                        "url" => 'snetchd://strokenetchandigarh.com/patient_analysis/' . $data['patient_id'] . '/comments',
                        "last_updated" => date("Y-m-d H:i:s"),
                    );
                    $this->updatePatientStatus($updateData);
                    // Global Status

                    $output = $this->printData("success", $this->fetchComments($data['patient_id']));
                    return $response->withJson($output, 200);
                } else {
                    $output = $this->printData("success", array("message" => "Problem posting your comment. Please try again."));
                    return $response->withJson($output, 400);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function fetchTransitionStatuses($patientId)
    {
        // Check if the patient is transitioned
        $checkPatientTransitioned = $this->ci->db->get("user_patients", array("in_transition", "is_spoke", "is_hub"), array("patient_id" => $patientId));

        $getPunchedStatuses = $this->ci->db->select("transition_statuses_view", "*", array("patient_id" => $patientId, "ORDER" => array("id" => "DESC")));
        foreach ($getPunchedStatuses as $key => $val) {
            $getPunchedStatuses[$key]['user_role'] = ucfirst(str_replace("_", " ", $val['user_role']));
            $getPunchedStatuses[$key]['date'] = date("jS F, Y", strtotime($val['created']));
            $getPunchedStatuses[$key]['time'] = date("h:i a", strtotime($val['created']));
        }
        $getAllStatuses = $this->ci->db->select("status_types", "*", array("ORDER" => array("position" => "ASC")));
        $statusTypes = array();

        foreach ($getAllStatuses as $key => $val1) {
            foreach ($getPunchedStatuses as $k => $punchedStatus) {
                if ($val1['id'] == $punchedStatus['status_id']) {
                    unset($getAllStatuses[$key]);
                }
            }
            if ($getAllStatuses[$key] !== null) {
                $statusTypes[$val1['loc_type']][] = $getAllStatuses[$key];
            }
        }

        $available_statuses = array();
        if ($checkPatientTransitioned["in_transition"] == "0" && $checkPatientTransitioned["is_spoke"] == "1") {
            $available_statuses = $statusTypes['spoke'];
        } else {
            $available_statuses = $statusTypes['hub'];
        }

        foreach ($available_statuses as $key => $val) {
            if ($val['id'] == 25 || $val['id'] == 24 || $val['id'] == 17 || $val['id'] == 16 || $val['id'] == 15 || $val['id'] == 14) {
                unset($available_statuses[$key]);
            }
        }

        $all_statuses = array();
        $all_statuses['available'] = array_values($available_statuses);

        $all_statuses['punched'] = $getPunchedStatuses;
        return $all_statuses;
    }

    public function getTransitionStatuses($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $output = $this->printData("success", $this->fetchTransitionStatuses($patientId));
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function postTransitionStatus($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if (!isset($data['patient_id']) || $data['patient_id'] == "") {
                $errors[] = "patient_id is required";
            }
            if (!isset($data['status_id']) || $data['status_id'] == "") {
                $errors[] = "status_id is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));

                $insertStatusData = array();
                $insertStatusData['user_id'] = $header_userId[0];
                $insertStatusData['patient_id'] = $data['patient_id'];
                $insertStatusData['status_id'] = $data['status_id'];
                $insertStatusData['center_id'] = $getUserCenterId['center_id'];
                $insertStatusData['created'] = date("Y-m-d H:i:s");

                $insertStatus = $this->ci->db->insert("transition_statuses", $insertStatusData);
                if ($insertStatus) {

                    // Update Last Updated
                    $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                    $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                    // Send message to all people about the status change
                    $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));
                    $getStatusInfo = $this->ci->db->get("status_types", array("title"), array("id" => $data['status_id']));

                    $getPushIDs = $this->getOneSignalIdsOfTheRadioWithoutDiagonisUsers($data['patient_id']);
                    // Get all the user's Push IDs where department not equal to Radio Diagnosis
                    if (count($getPushIDs['pushIDs']) > 0) {
                        $pushData = array();
                        $pushData['title'] = "Status Changed: " . $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . ")";

                        // Custom Message for Transfer of Patient to CT.
                        if ($data['status_id'] == "3" || $data['status_id'] == "5" || $data['status_id'] == "20") {
                            $pushData['message'] = "Patient is being shifted to CT.";
                            $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                        } else {
                            $pushData['message'] = "Status has been changed to: " . $getStatusInfo['title'];
                            $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                        }

                        $pushData['devices'] = $getPushIDs['pushIDs'];
                        $this->sendPush($pushData);

                        // Send SMS
                        // $phoneNumbers = array();
                        // foreach ($getPushIds['mobileNumbers'] as $phoneNumber) {
                        //     $phoneNumbers[] = "+91" . $phoneNumber;
                        // }
                        // $smsData = array(
                        //     "to" => implode("<", $phoneNumbers),
                        //     "message" => "Status Changed: " . $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . "). Patient is being shifted to CT.",
                        // );
                        // $this->sendSMS($smsData);
                    }

                    // If status  =  3 or 5 (Shift to CT), send push notificaton to Radiology People
                    if ($data['status_id'] == "3" || $data['status_id'] == "5" || $data['status_id'] == "20") {
                        $getRadioPushIDs = $this->getOneSignalIdsOfTheRadioDiagonisUsers($data['patient_id']);
                        if (count($getRadioPushIDs['pushIDs']) > 0) {
                            $pushData = array();
                            $pushData['title'] = "Acute Stroke arriving for CT";
                            $pushData['message'] = "Patient (" . $getPatientNameCode['name'] . ") is being shifted to CT.";
                            $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                            $pushData['devices'] = $getRadioPushIDs['pushIDs'];
                            $this->sendPush($pushData);

                            // Send SMS
                            $phoneRNumbers = array();
                            foreach ($getRadioPushIDs['mobileNumbers'] as $phoneNumber) {
                                $phoneRNumbers[] = "+91" . $phoneNumber;
                            }
                            $smsData = array(
                                "to" => implode("<", $phoneRNumbers),
                                "message" => "Acute Stroke arriving for CT. Patient (" . $getPatientNameCode['name'] . ") is being shifted to CT.",
                            );
                            $this->sendSMS($smsData);
                        }
                        // Update patients table scans_needed = 1
                        $this->ci->db->update("patients", array("scans_needed" => "1", "last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                        $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));
                    }

                    // Global Status
                    $updateData = array(
                        "user_id" => $header_userId[0],
                        "patient_id" => $data['patient_id'],
                        "update_type" => "status_update",
                        "url" => 'snetchd://strokenetchandigarh.com/patient_analysis/' . $data['patient_id'] . '/status',
                        "last_updated" => date("Y-m-d H:i:s"),
                    );
                    $this->updatePatientStatus($updateData);
                    // Global Status

                    $output = $this->printData("success", array("message" => "Status was updated successfully.", "transition_statuses" => $this->fetchTransitionStatuses($data['patient_id'])));
                    return $response->withJson($output, 200);
                } else {
                    $output = $this->printData("success", array("message" => "Problem posting your status. Please try again."));
                    return $response->withJson($output, 400);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function getConclusionTypes($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $finalArray = array();

            $checkIfExpiredOutomeExists = $this->ci->db->get("conclusion_outcomes", "*", array(
                "AND" => array(
                    "patient_id" => $patientId,
                    "conclusion_type" => "Outcome",
                    "conclusion_value" => "Expired",
                )
            ));

            $checkIfDischargedOutomeExists = $this->ci->db->get("conclusion_outcomes", "*", array(
                "AND" => array(
                    "patient_id" => $patientId,
                    "conclusion_type" => "Outcome",
                    "conclusion_value" => "Discharge",
                )
            ));

            $checkIfLAMAOutomeExists = $this->ci->db->get("conclusion_outcomes", "*", array(
                "AND" => array(
                    "patient_id" => $patientId,
                    "conclusion_type" => "Outcome",
                    "conclusion_value" => "LAMA",
                )
            ));
            if (isset($checkIfExpiredOutomeExists['id'])) {
                $types = array();
                $getConclustionTypes = array();
                $finalArray['patient_discharged'] = true;
                $finalArray['patient_expired'] = true;
            }
            // If patient is discharged
            else if (isset($checkIfDischargedOutomeExists['id'])) {
                $finalArray['patient_discharged'] = true;

                $getConclustionTypes = $this->ci->db->select("conclusion_types", "*", array("OR" => array(
                    "id" => 11,
                )));
                $allTypes = array();
                foreach ($getConclustionTypes as $key => $val) {
                    if ($finalArray['patient_discharged'])
                        $allTypes[$val['type']][] = $val;
                }
                $types = array();
                foreach ($allTypes as $key => $val) {
                    $types[] = $key;
                }
            }
            // If patient is on LAMA
            else if (isset($checkIfLAMAOutomeExists['id'])) {
                $finalArray['patient_onLAMA'] = true;

                $getConclustionTypes = $this->ci->db->select("conclusion_types", "*", array("OR" => array(
                    "id" => 11,
                )));
                $allTypes = array();
                foreach ($getConclustionTypes as $key => $val) {
                    if ($finalArray['patient_discharged'])
                        $allTypes[$val['type']][] = $val;
                }
                $types = array();
                foreach ($allTypes as $key => $val) {
                    $types[] = $key;
                }
            } else {
                $finalArray['patient_discharged'] = false;
                $finalArray['patient_expired'] = false;
                $finalArray['patient_onLAMA'] = false;

                $getConclustionTypes = $this->ci->db->select("conclusion_types", "*");
                $allTypes = array();
                foreach ($getConclustionTypes as $key => $val) {
                    if ($finalArray['patient_discharged'])
                        $allTypes[$val['type']][] = $val;
                }
                $types = array();
                foreach ($allTypes as $key => $val) {
                    $types[] = $key;
                }
            }

            $patient_conclusion_outcomes = $this->ci->db->select("conclusion_outcomes_view", array("conclusion_type", "conclusion_value", "created", "user_name"), array("patient_id" => $patientId, "ORDER" => array("id" => "DESC")));
            foreach ($patient_conclusion_outcomes as $key => $val) {
                $patient_conclusion_outcomes[$key]['date'] = date("jS F, Y", strtotime($val['created']));
                $patient_conclusion_outcomes[$key]['time'] = date("h:i a", strtotime($val['created']));
            }

            $finalArray['types'] = $types;
            $finalArray['values'] = $getConclustionTypes;
            $finalArray['outcomes'] = $patient_conclusion_outcomes;

            $output = $this->printData("success", $finalArray);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function postConclusion($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if (!isset($data['patient_id']) || $data['patient_id'] == "") {
                $errors[] = "patient_id is required";
            }
            if (!isset($data['conclusion_type']) || $data['conclusion_type'] == "") {
                $errors[] = "conclusion_type is required";
            }
            if (!isset($data['conclusion_value']) || $data['conclusion_value'] == "") {
                $errors[] = "conclusion_value is required";
            }

            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $insertConclusionData = array();
                $insertConclusionData['user_id'] = $header_userId[0];
                $insertConclusionData['patient_id'] = $data['patient_id'];

                $insertConclusionData['conclusion_type'] = $data['conclusion_type'];
                $insertConclusionData['conclusion_value'] = $data['conclusion_value'];
                $insertConclusionData['created'] = date("Y-m-d H:i:s");

                $insertConclusion = $this->ci->db->insert("conclusion_outcomes", $insertConclusionData);
                if ($insertConclusion) {
                    //Close all the conversations where any consclustion outcome happens

                    $this->ci->db->update("conversations", array("conversation_closed" => "1"), array("patient_id" => $data['patient_id']));

                    // Update Last Updated
                    $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                    $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                    // Global Status
                    $updateData = array(
                        "user_id" => $header_userId[0],
                        "patient_id" => $data['patient_id'],
                        "update_type" => "status_update",
                        "url" => 'snetchd://strokenetchandigarh.com/patient_analysis/' . $data['patient_id'] . '/status',
                        "last_updated" => date("Y-m-d H:i:s"),
                    );
                    $this->updatePatientStatus($updateData);
                    // Global Status

                    $output = $this->printData("success", array("message" => "Status was updated successfully."));
                    return $response->withJson($output, 200);
                } else {
                    $output = $this->printData("success", array("message" => "Problem posting your status. Please try again."));
                    return $response->withJson($output, 400);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
}