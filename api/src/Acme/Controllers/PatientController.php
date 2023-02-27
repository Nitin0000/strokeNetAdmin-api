<?php

namespace Acme\Controllers;

use DateTime;

date_default_timezone_set('Asia/Kolkata');


class PatientController extends BaseController
{
    public function convert_seconds($seconds)
    {
        $dt1 = new DateTime("@0");
        $dt2 = new DateTime("@$seconds");
        $interval = $dt1->diff($dt2);
        return $interval->format('%H') . "h:" . $interval->format('%I') . "m";
    }

    public function calculateTimeBetweenTwoTimes($time_start, $time_end)
    {
        $datetime1 = new DateTime($time_start);
        $datetime2 = new DateTime($time_end);
        $interval = $datetime1->diff($datetime2);
        $timeArray = array();
        $timeArray['time_in_seconds'] = $datetime2->getTimestamp() - $datetime1->getTimestamp();
        $timeArray['time_in_string'] = $interval->format('%D') . "d:" . $interval->format('%H') . "h:" . $interval->format('%I') . "m:" . $interval->format('%s') . "s";
        return $timeArray;
    }



    public function getPatientCalculatedTimes($patientId)
    {
        $finalTimeArray = array();

        // Get Patient Stroke time and admission time
        $getPatientTableTimes = $this->ci->db->get("patients", array("admission_time", "datetime_of_stroke", "created"), array("id" => $patientId));
        $finalTimeArray['admission_time'] = $getPatientTableTimes['admission_time'];
        $finalTimeArray['datetime_of_stroke'] = $getPatientTableTimes['datetime_of_stroke'];

        // Get Patient Scan Times
        $getPatientScanTimes = $this->ci->db->get("patient_scan_times", array("ct_scan_time", "mr_mra_time", "dsa_time_completed"), array("patient_id" => $patientId));
        $finalTimeArray['ct_scan_time'] = $getPatientScanTimes['ct_scan_time'];

        // Get Patient IVT times
        $getPatientIVTTimes = $this->ci->db->get("patient_ivt_medications", array("door_to_needle_time"), array("patient_id" => $patientId));
        $finalTimeArray['door_to_needle_time'] = $getPatientIVTTimes['door_to_needle_time'];

        // Get Hub MT Started
        $getPatientHubMTStartedTime = $this->ci->db->get("transition_statuses", array("created"), array("AND" => array(
            "patient_id" => $patientId,
            "status_id" => "6",
        )));
        if (isset($getPatientHubMTStartedTime['created']) && $getPatientHubMTStartedTime['created']) {
            $finalTimeArray['mt_started_time'] = $getPatientHubMTStartedTime['created'];
        } else {
            $finalTimeArray['mt_started_time'] = null;
        }

        // Get Hub MT Completed
        $getPatientHubMTCompletedTime = $this->ci->db->get("transition_statuses", array("created"), array("AND" => array(
            "patient_id" => $patientId,
            "status_id" => "18",
        )));
        if (isset($getPatientHubMTCompletedTime['created']) && $getPatientHubMTCompletedTime['created']) {
            $finalTimeArray['mt_completed_time'] = $getPatientHubMTCompletedTime['created'];
        } else {
            $finalTimeArray['mt_completed_time'] = null;
        }

        $calculatedTimes = array();

        // Stroke to Door in time
        $calculatedTimes['tfso_time'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['datetime_of_stroke'], $finalTimeArray['admission_time']); // stroke to admission time

        // Door to CT Time
        if (isset($finalTimeArray['ct_scan_time']) && $finalTimeArray['ct_scan_time']) {
            $calculatedTimes['door_to_ct_time'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['admission_time'], $finalTimeArray['ct_scan_time']);
        }

        // Door to Needle Time
        if (isset($finalTimeArray['door_to_needle_time']) && $finalTimeArray['door_to_needle_time']) {
            $calculatedTimes['door_to_needle_time'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['admission_time'], $finalTimeArray['door_to_needle_time']);
        }

        // Door to Groin Puncture
        if (isset($finalTimeArray['mt_started_time']) && $finalTimeArray['mt_started_time']) {
            $calculatedTimes['door_to_groin_puncture'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['admission_time'], $finalTimeArray['mt_started_time']);
        }

        // CT to Groin Puncture
        if (isset($finalTimeArray['mt_started_time']) && isset($finalTimeArray['ct_scan_time']) && $finalTimeArray['mt_started_time'] && $finalTimeArray['ct_scan_time']) {
            $calculatedTimes['ct_to_groin_puncture'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['ct_scan_time'], $finalTimeArray['mt_started_time']);
        }

        // Door to Hub MT Completed
        if (isset($finalTimeArray['mt_completed_time']) && $finalTimeArray['mt_started_time']) {
            $calculatedTimes['door_to_hub_mt_completed'] = $this->calculateTimeBetweenTwoTimes($finalTimeArray['admission_time'], $finalTimeArray['mt_completed_time']);
        }

        $timesArray = array();
        $timesArray['times'] = $finalTimeArray;
        $timesArray['calculated'] = $calculatedTimes;

        // $this->pr($timesArray);
        return $timesArray;
    }

    // public function testCall($request, $response, $args)
    // {
    //     $callData = array(
    //         "to" => "+919888948964",
    //     );
    //     $createCall = $this->createCall($callData);
    //     return $createCall;
    // }

    public function calculateBulkPatientTimings($request, $response, $args)
    {

        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {

            $patientType = $args['patientType'];
            $timePeriod = $args['timePeriod'];
            $dateStart = "";
            $dateEnd = "";
            switch ($timePeriod) {
                case "past_one_week":
                    $dateEnd = date('Y-m-d');
                    $dateStart = date('Y-m-d', strtotime("-1 week"));
                    break;
                case "past_one_month":
                    $dateEnd = date('Y-m-d');
                    $dateStart = date('Y-m-d', strtotime("-1 month"));
                    break;
                case "past_six_months":
                    $dateEnd = date('Y-m-d');
                    $dateStart = date('Y-m-d', strtotime("-6 months"));
                    break;
                case "past_one_year":
                    $dateEnd = date('Y-m-d');
                    $dateStart = date('Y-m-d', strtotime("-1 year"));
                    break;
            }
            if ($patientType == "ischemic") {
                $getPatientsQuery = "SELECT DISTINCT(patient_id) FROM `patient_scan_times` WHERE type_of_stroke = 'Ischemic' AND DATE(last_updated) BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'";
            } else if ($patientType == "ivt_bolus") {
                $getPatientsQuery = "SELECT DISTINCT(patient_id) FROM `transition_statuses` WHERE status_id = '10' OR status_id = '9' OR status_id = '22' AND DATE(created) BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'";
            } else {
                // Get All Patients
                $getPatientsQuery = "SELECT DISTINCT(patient_id) FROM `transition_statuses` WHERE (status_id = '1' OR status_id ='2' OR status_id ='19') AND DATE(created) BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'";
            }

            $getPatientIds = $this->ci->db->query($getPatientsQuery)->fetchAll();

            $finalTimes = array(
                "tfso_time" => array(),
                "door_to_ct_time" => array(),
                "door_to_needle_time" => array(),
                "door_to_groin_puncture" => array(),
                "ct_to_groin_puncture" => array(),
                "door_to_hub_mt_completed" => array(),
            );
            $totalPatients = 0;
            foreach ($getPatientIds as $patientId) {
                // Check if patient exists
                $getPatient = $this->ci->db->get("patients", array("id"), array("id" => $patientId['patient_id']));
                if (isset($getPatient['id']) && $getPatient['id']) {
                    $totalPatients = $totalPatients + 1;
                    $getPatientTimes = $this->getPatientCalculatedTimes($getPatient['id']);
                    $times = $getPatientTimes['calculated'];

                    if (isset($times['tfso_time']) && $times['tfso_time']) {
                        $finalTimes['tfso_time'][] = $times['tfso_time']['time_in_seconds'];
                    }
                    if (isset($times['door_to_ct_time']) && $times['door_to_ct_time']) {
                        $finalTimes['door_to_ct_time'][] = $times['door_to_ct_time']['time_in_seconds'];
                    }
                    if (isset($times['door_to_needle_time']) && $times['door_to_needle_time']) {
                        $finalTimes['door_to_needle_time'][] = $times['door_to_needle_time']['time_in_seconds'];
                    }
                    if (isset($times['door_to_groin_puncture']) && $times['door_to_groin_puncture']) {
                        $finalTimes['door_to_groin_puncture'][] = $times['door_to_groin_puncture']['time_in_seconds'];
                    }
                    if (isset($times['ct_to_groin_puncture']) && $times['ct_to_groin_puncture']) {
                        $finalTimes['ct_to_groin_puncture'][] = $times['ct_to_groin_puncture']['time_in_seconds'];
                    }
                    if (isset($times['door_to_hub_mt_completed']) && $times['door_to_hub_mt_completed']) {
                        $finalTimes['door_to_hub_mt_completed'][] = $times['door_to_hub_mt_completed']['time_in_seconds'];
                    }
                }
            }

            $averages = array();
            if (count($finalTimes['tfso_time']) > 0) {
                $average = intval(array_sum($finalTimes['tfso_time']) / count($finalTimes['tfso_time']));

                $averages['tfso_time'] = $average;
                $averages['tfso_time_text'] = $this->convert_seconds($average);
            }
            if (count($finalTimes['door_to_ct_time']) > 0) {
                $average = intval(array_sum($finalTimes['door_to_ct_time']) / count($finalTimes['door_to_ct_time']));

                $averages['door_to_ct_time'] = $average;
                $averages['door_to_ct_time_text'] = $this->convert_seconds($average);
            }
            if (count($finalTimes['door_to_needle_time']) > 0) {
                $average = intval(array_sum($finalTimes['door_to_needle_time']) / count($finalTimes['door_to_needle_time']));

                $averages['door_to_needle_time'] = $average;
                $averages['door_to_needle_time_text'] = $this->convert_seconds($average);
            }
            if (count($finalTimes['door_to_groin_puncture']) > 0) {
                $average = intval(array_sum($finalTimes['door_to_groin_puncture']) / count($finalTimes['door_to_groin_puncture']));

                $averages['door_to_groin_puncture'] = $average;
                $averages['door_to_groin_puncture_text'] = $this->convert_seconds($average);
            }
            if (count($finalTimes['ct_to_groin_puncture']) > 0) {
                $average = intval(array_sum($finalTimes['ct_to_groin_puncture']) / count($finalTimes['ct_to_groin_puncture']));

                $averages['ct_to_groin_puncture'] = $average;
                $averages['ct_to_groin_puncture_text'] = $this->convert_seconds($average);
            }
            if (count($finalTimes['door_to_hub_mt_completed']) > 0) {
                $average = intval(array_sum($finalTimes['door_to_hub_mt_completed']) / count($finalTimes['door_to_hub_mt_completed']));

                $averages['door_to_hub_mt_completed'] = $average;
                $averages['door_to_hub_mt_completed_text'] = $this->convert_seconds($average);
            }

            $finalData = array();
            $finalData['total_patients'] = $totalPatients;
            $finalData['averages'] = $averages;

            $output = $this->printData("success", $finalData);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function getPatientTimes($request, $response, $args)
    {
        $this->getPatientCalculatedTimes($args['patientId']);
    }

    public function getPatientDetails($patientID, $userId)
    {
        // Get the information of the patient
        $patient = $this->ci->db->get("patients", "*", array("id" => $patientID));
        $patient['covid_score'] = intval($patient['covid_score']);
        $patient['covid_values'] = json_decode($patient['covid_values'], true);

        $patient['created'] = date("M d, Y h:i a", strtotime($patient['created']));
        $patient['datetime_of_stroke_formatted'] = date("M d, Y h:i a", strtotime($patient['datetime_of_stroke']));

        // Check if patient information checked already
        $patient['patient_checked'] = $this->patientCheckedbyUser($patient['id'], $userId, $patient['last_updated']);

        $patient['last_update'] = $this->ci->db->get("patient_updates", "*", array("patient_id" => $patientID, "ORDER" => array("id" => "DESC")));
        if (isset($patient['last_update']) && $patient['last_update']['id']) {
            $patient['last_update']['user_id'] = $this->getUserDetailsBasic($patient['last_update']['user_id']);
        }

        $patient['last_message'] = $this->getLastMessageFromPatientConversations($userId, $patientID);

        //Calculate Age of the patient
        $today = date("Y-m-d");
        $diff = date_diff(date_create($patient['date_of_birth']), date_create($today));
        $patient['age'] = $diff->format('%y');

        $datetimeStrokeStarts = strtotime($patient['datetime_of_stroke']);
        $datetimeStrokeEnds = strtotime($patient['datetime_of_stroke_timeends']);
        $currentTime = time();

        if ($currentTime > $datetimeStrokeStarts) {
            $patient['show_increment_timer'] = true;
        }

        $getPatientTransitionStatusesOfEntry = $this->ci->db->query("SELECT created, id, status_id, title FROM `transition_statuses_view` WHERE patient_id = " . $patientID . " AND (status_id = 1 OR status_id = 2 OR status_id = 19) ORDER BY id DESC LIMIT 1")->fetchAll();
        foreach (@$getPatientTransitionStatusesOfEntry as $key => $value) {
            foreach ($value as $k => $v) {
                if (is_int($k)) {
                    unset($getPatientTransitionStatusesOfEntry[$key][$k]);
                }
            }
        }
        if (count($getPatientTransitionStatusesOfEntry) > 0) {
            $fortyfiveminsStart = $getPatientTransitionStatusesOfEntry[0]['created'];
            $fortyfiveminsEnds = date("Y-m-d H:i:s", strtotime($fortyfiveminsStart . "+45 minutes"));
            if ($currentTime > strtotime($fortyfiveminsStart) && $currentTime < strtotime($fortyfiveminsEnds)) {
                $patient['show_decrement_timer'] = true;
                $patient['datetime_of_procedure_to_be_done'] = $fortyfiveminsEnds;
            } else {
                $patient['show_decrement_timer'] = false;
                $patient['datetime_of_procedure_to_be_done'] = $fortyfiveminsEnds;
            }
        }

        // hide Decrement timer if TFSO is more than 4.5 hours
        $checkTFSO = date("Y-m-d H:i:s", strtotime($patient['datetime_of_stroke'] . "4.5 hours"));
        $currentTime = time();
        if ($datetimeStrokeStarts > $currentTime) {
            $patient['show_decrement_timer'] = false;
        }

        // Stop all timers if any of the status is available: IVT and MT ineligible, or Clock is stopped
        $getPatientTransitionStatusesOfEntryForStoppingClock = $this->ci->db->query("SELECT created, id, status_id, title FROM `transition_statuses_view` WHERE patient_id = " . $patientID . " AND (status_id = 11 OR status_id = 16 OR status_id = 17 OR status_id = 23 OR status_id = 25) ORDER BY id DESC LIMIT 1")->fetchAll();
        foreach (@$getPatientTransitionStatusesOfEntryForStoppingClock as $key => $value) {
            foreach ($value as $k => $v) {
                if (is_int($k)) {
                    unset($getPatientTransitionStatusesOfEntryForStoppingClock[$key][$k]);
                }
            }
        }

        if ($getPatientTransitionStatusesOfEntryForStoppingClock && count($getPatientTransitionStatusesOfEntryForStoppingClock > 0)) {
            $clockedStoppedat = $getPatientTransitionStatusesOfEntryForStoppingClock[0]['created'];

            $date_a = new DateTime($patient['datetime_of_stroke']);
            $date_b = new DateTime($clockedStoppedat);

            $interval = date_diff($date_a, $date_b);
            $date_array = array();
            if (isset($interval->d) && $interval->d > 0) {
                $date_array[] = $interval->format("%d") . "d";
            }
            if (isset($interval->h) && $interval->h > 0) {
                $date_array[] = $interval->format("%h") . "h";
            }
            if (isset($interval->i)) {
                $date_array[] = $interval->format("%i") . "m";
            }
            if (isset($interval->s)) {
                $date_array[] = $interval->format("%d") . "s";
            }
            $patient['show_increment_timer'] = false;
            $patient['show_tfso_total_time_message_box'] = true;
            $patient['show_total_time_taken_from_entry'] = implode(" : ", $date_array);

            // Needle Time Clock
            $getPatientIVTTimes = $this->ci->db->get("patient_ivt_medications", array("door_to_needle_time"), array("patient_id" => $patient['id']));
            $fortfive_date_a = new DateTime($patient['admission_time']);
            $fortfive_date_b = new DateTime($getPatientIVTTimes['door_to_needle_time']);

            $decrement_interval = date_diff($fortfive_date_a, $fortfive_date_b);

            // $this->pr($decrement_interval);
            // exit;
            $decrment_date_array = array();
            if (isset($decrement_interval->d) && $decrement_interval->d > 0) {
                $decrment_date_array[] = $decrement_interval->format("%d") . "d";
            }
            if (isset($decrement_interval->h) && $decrement_interval->h > 0) {
                $decrment_date_array[] = $decrement_interval->format("%h") . "h";
            }
            if (isset($decrement_interval->i)) {
                $decrment_date_array[] = $decrement_interval->format("%i") . "m";
            }
            if (isset($decrement_interval->s)) {
                $decrment_date_array[] = $decrement_interval->format("%s") . "s";
            }
            $patient['show_decrement_timer'] = false;
            $patient['show_45_minutes_deadline_box'] = true;
            $patient['show_45_minutes_taken_deadline'] = implode(" : ", $decrment_date_array);
        }

        // Needle Time Clock
        $getPatientIVTTimes = $this->ci->db->get("patient_ivt_medications", array("door_to_needle_time"), array("patient_id" => $patient['id']));
        if (isset($getPatientIVTTimes['door_to_needle_time']) && $getPatientIVTTimes['door_to_needle_time']) {
            $fortfive_date_a = new DateTime($patient['admission_time']);
            $fortfive_date_b = new DateTime($getPatientIVTTimes['door_to_needle_time']);

            $decrement_interval = date_diff($fortfive_date_a, $fortfive_date_b);
            $decrment_date_array = array();
            if (isset($decrement_interval->d) && $decrement_interval->d > 0) {
                $decrment_date_array[] = $decrement_interval->format("%d") . "d";
            }
            if (isset($decrement_interval->h) && $decrement_interval->h > 0) {
                $decrment_date_array[] = $decrement_interval->format("%h") . "h";
            }
            if (isset($decrement_interval->i)) {
                $decrment_date_array[] = $decrement_interval->format("%i") . "m";
            }
            if (isset($decrement_interval->s)) {
                $decrment_date_array[] = $decrement_interval->format("%s") . "s";
            }
            $patient['show_decrement_timer'] = false;
            $patient['show_45_minutes_deadline_box'] = true;
            $patient['show_45_minutes_taken_deadline'] = implode(" : ", $decrment_date_array);
        }

        $patient['user_data'] = $this->ci->db->get("users", array("user_id", "fullname", "phone_number"), array("user_id" => $patient['created_by']));

        // Get quick information about the patients from user_patients table
        $patientQuickData = $this->ci->db->get("user_patients", "*", array("patient_id" => $patientID));

        // Check if the patient is from Spoke
        if ($patientQuickData['is_spoke'] == "1") {
            $patient['is_spoke_patient'] = true;
        } else {
            $patient['is_spoke_patient'] = false;
        }

        // Check if the patient is from Hub
        if ($patientQuickData['is_hub'] == "1") {
            $patient['is_hub_patient'] = true;
        } else {
            $patient['is_hub_patient'] = false;
        }

        // Check if the patient is from center
        if ($patientQuickData['is_center'] == "1") {
            $patient['is_center_patient'] = true;
        } else {
            $patient['is_center_patient'] = false;
        }

        // Check if the user has been transition
        if ($patientQuickData['in_transition'] == "1") {
            $patient['in_transition'] = true;
        } else {
            $patient['in_transition'] = false;
        }

        // Check if the patient can be transitioned to Hub
        if ($patient['is_spoke_patient'] && !$patient['in_transition']) {
            $patient['can_be_transitioned_to_hub'] = true;
        } else {
            $patient['can_be_transitioned_to_hub'] = false;
        }
        if ($patient['is_spoke_patient'] && $patient['in_transition']) {
            $patient['already_transitioned'] = true;
        } else {
            $patient['already_transitioned'] = false;
        }

        // Check if the patient can be transitioned to Spoke
        if ($patient['is_center_patient'] && !$patient['in_transition']) {
            $patient['can_be_transitioned_to_spoke'] = true;
        } else {
            $patient['can_be_transitioned_to_spoke'] = false;
        }
        if ($patient['is_center_patient'] && $patient['in_transition']) {
            $patient['already_transitioned'] = true;
        } else {
            $patient['already_transitioned'] = false;
        }

        // Check if the logged in user is from Hub or Spoke
        $getTheUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $userId));
        $getCenterInfo = $this->ci->db->get("centers", "*", array("id" => $getTheUserCenterId['center_id']));
        if ($getCenterInfo['is_hub'] == "yes") {
            $patient['is_user_from_hub'] = true;
        } else {
            $patient['is_user_from_hub'] = false;
        }
        // Get the center information
        $patient['center_info'] = $getCenterInfo;

        // Check if the logged in user is from hub
        if ($patient['is_user_from_hub']) {
            // Check if the patient is from hub or the patient is from spoke and is transitioned
            if ($patient['is_hub_patient'] || ($patient['in_transition'] && ($patient['is_spoke_patient'] || $patient['is_center_patient']))) {
                $patient['can_edit_patient_details'] = true;
                $patient['show_original_name'] = true;
            } else {
                $patient['can_edit_patient_details'] = false;
                $patient['show_original_name'] = false;
            }
        } else {
            // Check if the user is from Spoke and Patient is not in Transition or is a Spoke Patient
            if ($patient['is_spoke_patient'] && !$patient['in_transition']) {
                $patient['can_edit_patient_details'] = true;
                $patient['show_original_name'] = true;
            } else if ($patient['is_center_patient'] && !$patient['in_transition']) {
                $patient['can_edit_patient_details'] = true;
                $patient['show_original_name'] = true;
            } else {
                $patient['can_edit_patient_details'] = false;
                $patient['show_original_name'] = false;
            }
        }

        // get the basic details of tha patient and create an array for form in the app
        $patient['patient_basic_details'] = array(
            "first_name" => $patient['first_name'],
            "last_name" => $patient['last_name'],
            "name" => $patient['name'],
            "date_of_birth" => $patient['date_of_birth'],
            "gender" => $patient['gender'],
            "contact_number" => $patient['contact_number'],
            "address" => $patient['address'],
            "datetime_of_stroke" => $patient['datetime_of_stroke'],
            "handedness" => $patient['handedness'],
            "weakness_side" => $patient['weakness_side'],
            "facial_deviation" => $patient['facial_deviation'],
            "co_morbidities" => $patient['co_morbidities'],
            "similar_episodes_in_past" => $patient['similar_episodes_in_past'],
            "similar_episodes_in_past_text" => $patient['similar_episodes_in_past_text'],
            "inclusion_exclusion_assessed" => $patient['inclusion_exclusion_assessed'],
            "bp_x" => $patient['bp_x'],
            "bp_y" => $patient['bp_y'],
            "rbs" => $patient['rbs'],
            "inr" => $patient['inr'],
            "aspects" => $patient['aspects'],
            "body_weight" => $patient['body_weight'],
            "blood_group" => $patient['blood_group'],
            "admission_time" => $patient['admission_time'],
            "is_wakeup_stroke" => $patient['is_wakeup_stroke'],
            "is_hospital_stroke" => $patient['is_hospital_stroke'],
            "notes" => $patient['notes'],
        );

        //  Check if its a wakeup stroke
        if ($patient['patient_basic_details']['is_wakeup_stroke'] == "1") {
            $patient['patient_basic_details']['is_wakeup_stroke'] = true;
        } else {
            $patient['patient_basic_details']['is_wakeup_stroke'] = false;
        }

        //  Check if its a hospital stroke
        if ($patient['patient_basic_details']['is_hospital_stroke'] == "1") {
            $patient['patient_basic_details']['is_hospital_stroke'] = true;
        } else {
            $patient['patient_basic_details']['is_hospital_stroke'] = false;
        }

        //  Get the admission time
        if ($patient['patient_basic_details']['admission_time']) {
            $dt = new DateTime($patient['patient_basic_details']['admission_time']);
            $patient['patient_basic_details']['admission_time'] = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
            $patient['patient_basic_details']['admission_time_formatted'] = date("M d, Y h:i a", strtotime($patient['patient_basic_details']['admission_time'] . " -330 mins"));
        }

        if ($patient['scans_needed'] == "1") {
            $patient['scans_needed'] = true;
        } else {
            $patient['scans_needed'] = false;
        }

        if ($patient['scans_completed'] == "1") {
            $patient['scans_completed'] = true;
        } else {
            $patient['scans_completed'] = false;
        }

        if ($patient['scans_uploaded'] == "1") {
            $patient['scans_uploaded'] = true;
        } else {
            $patient['scans_uploaded'] = false;
        }

        // get the patient's presentation data
        $patient['patient_presentation'] = $this->ci->db->get("patient_presentation", array("pol_right_ul", "pol_right_ll", "pol_left_ul", "pol_left_ll", "loc"), array("patient_id" => $patientID));

        // get the patient scan times
        $patient['patient_scan_times'] = $this->ci->db->get("patient_scan_times", array(
            "ct_scan_time", "mr_mra_time", "dsa_time_completed", "type_of_stroke",
            "lvo", "lvo_types", "lvo_site", "aspects",
        ), array("patient_id" => $patientID));

        if ($patient['patient_scan_times']['type_of_stroke'] && $patient['patient_scan_times']['type_of_stroke'] !== null) {
            if ($patient['patient_scan_times']['type_of_stroke'] == "Hemorrhagic") {
                $patient['show_stroke_type_text'] = true;
                $patient['showIVTProtocolBox'] = false;
                $patient['stroke_type'] = "H";
            } else {
                $patient['show_stroke_type_text'] = true;
                $patient['stroke_type'] = "I";
                $patient['showIVTProtocolBox'] = true;
            }
        } else {
            $patient['show_stroke_type_text'] = false;
            $patient['showIVTProtocolBox'] = true;
        }

        if ($patient['patient_scan_times']['lvo'] == "1") {
            $patient['patient_scan_times']['lvo'] = true;
        } else {
            $patient['patient_scan_times']['lvo'] = false;
        }

        // get the patient contradictions
        $patient['patient_contradictions'] = $this->ci->db->get("patient_contradictions", array(
            "contradictions_data", "absolute_score", "relative_score", "ivt_eligible", "checked",
        ), array("patient_id" => $patientID));

        if ($patient['patient_contradictions']['absolute_score'] > 0) {
            $patient['patient_contradictions']['show_ivteligible_box'] = false;
            $patient['patient_contradictions']['show_ivtineligible_box'] = true;
        } else {
            $patient['patient_contradictions']['show_ivtineligible_box'] = false;
            if ($patient['patient_contradictions']['relative_score'] == 0 && $patient['patient_contradictions']['checked']) {
                $patient['patient_contradictions']['show_ivteligible_box'] = true;
            }
            if ($patient['patient_contradictions']['relative_score'] > 0 && $patient['patient_contradictions']['checked']) {
                $patient['patient_contradictions']['show_ivteligible_box'] = true;
            }
            if ($patient['patient_contradictions']['absolute_score'] > 0 && $patient['patient_contradictions']['checked']) {
                $patient['patient_contradictions']['show_ivteligible_box'] = false;
            }

            if (
                $patient['patient_contradictions']['absolute_score'] == 0
                && $patient['patient_contradictions']['relative_score'] == 0
                && $patient['patient_contradictions']['checked'] == "1"
            ) {
                $patient['patient_contradictions']['ivt_eligible'] = "1";
            }
        }

        if ($patient['patient_contradictions']['ivt_eligible'] == "1") {
            $patient['patient_contradictions']['ivt_eligible'] = true;
        } else {
            $patient['patient_contradictions']['ivt_eligible'] = false;
        }

        if ($patient['patient_contradictions']['checked'] == "1") {
            $patient['patient_contradictions']['checked'] = true;
        } else {
            $patient['patient_contradictions']['checked'] = false;
        }

        // get the patient medications
        $patient['patient_ivt_medications'] = $this->ci->db->get("patient_ivt_medications", array(
            "medicine", "dose_value", "patient_weight", "total_dose", "bolus_dose", "infusion_dose", "door_to_needle_time",
        ), array("patient_id" => $patientID));

        if ($patient['patient_ivt_medications']['door_to_needle_time']) {
            $dt = new DateTime($patient['patient_ivt_medications']['door_to_needle_time']);
            $patient['patient_ivt_medications']['door_to_needle_time'] = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
            $patient['patient_ivt_medications']['door_to_needle_time_formatted'] = date("M d, Y h:i a", strtotime($patient['patient_ivt_medications']['door_to_needle_time'] . " -330 mins"));
        }

        if ($patient['patient_scan_times']['ct_scan_time']) {
            // Total time taken from Door to CT/CT Scan Completed.

            $patient["door_to_ct_time"] = $this->calculateTimeBetweenTwoTimes($patient['created'], $patient['patient_scan_times']['ct_scan_time']);

            $dt = new DateTime($patient['patient_scan_times']['ct_scan_time']);
            $patient['patient_scan_times']['ct_scan_time'] = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
            $patient['patient_scan_times']['ct_scan_time_formatted'] = date("M d, Y h:i a", strtotime($patient['patient_scan_times']['ct_scan_time'] . " -330 mins"));
        }

        if ($patient['patient_scan_times']['mr_mra_time']) {
            $dt = new DateTime($patient['patient_scan_times']['mr_mra_time']);
            $patient['patient_scan_times']['mr_mra_time'] = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
            $patient['patient_scan_times']['mr_mra_time_formatted'] = date("M d, Y h:i a", strtotime($patient['patient_scan_times']['mr_mra_time'] . " -330 mins"));
        }
        if ($patient['patient_scan_times']['dsa_time_completed']) {
            $dt = new DateTime($patient['patient_scan_times']['dsa_time_completed']);
            $patient['patient_scan_times']['dsa_time_completed'] = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';
            $patient['patient_scan_times']['dsa_time_completed_formatted'] = date("M d, Y h:i a", strtotime($patient['patient_scan_times']['dsa_time_completed'] . " -330 mins"));
        }

        // get the patient's complications data
        $patient['patient_complications'] = $this->ci->db->get("patient_complications", array(
            "bed_sore", "bed_sore_site", "bed_sore_degree", "bed_sore_duration", "bed_sore_photo",
            "aspiration", "aspiration_duration",
            "deep_vein_thormobosis", "deep_vein_thormobosis_site", "deep_vein_thormobosis_duration",
            "frozen_shoulder", "frozen_shoulder_site", "frozen_shoulder_duration",
            "contracture", "contracture_site", "contracture_duration",
            "spasticity", "spasticity_site", "spasticity_duration",
            "cauti", "cauti_duration",
            "others", "others_information", "others_duration",
        ), array("patient_id" => $patientID));

        if ($patient['patient_complications']['bed_sore_photo'] && $patient['patient_complications']['bed_sore_photo'] !== null) {
            $patient['patient_complications']['bed_sore_photo_thumb'] = uploads_url . '/thumb/compress.php?src=' . $patient['patient_complications']['bed_sore_photo'] . '&w=200&h=200&zc=1';
            $patient['patient_complications']['bed_sore_photo_full'] = uploads_url . $patient['patient_complications']['bed_sore_photo'];
        }

        if ($patient['patient_complications']['bed_sore'] == "1") {
            $patient['patient_complications']['bed_sore'] = true;
        } else {
            $patient['patient_complications']['bed_sore'] = false;
        }

        if ($patient['patient_complications']['aspiration'] == "1") {
            $patient['patient_complications']['aspiration'] = true;
        } else {
            $patient['patient_complications']['aspiration'] = false;
        }

        if ($patient['patient_complications']['deep_vein_thormobosis'] == "1") {
            $patient['patient_complications']['deep_vein_thormobosis'] = true;
        } else {
            $patient['patient_complications']['deep_vein_thormobosis'] = false;
        }

        if ($patient['patient_complications']['frozen_shoulder'] == "1") {
            $patient['patient_complications']['frozen_shoulder'] = true;
        } else {
            $patient['patient_complications']['frozen_shoulder'] = false;
        }

        if ($patient['patient_complications']['contracture'] == "1") {
            $patient['patient_complications']['contracture'] = true;
        } else {
            $patient['patient_complications']['contracture'] = false;
        }

        if ($patient['patient_complications']['spasticity'] == "1") {
            $patient['patient_complications']['spasticity'] = true;
        } else {
            $patient['patient_complications']['spasticity'] = false;
        }
        if ($patient['patient_complications']['cauti'] == "1") {
            $patient['patient_complications']['cauti'] = true;
        } else {
            $patient['patient_complications']['cauti'] = false;
        }
        if ($patient['patient_complications']['others'] == "1") {
            $patient['patient_complications']['others'] = true;
        } else {
            $patient['patient_complications']['others'] = false;
        }

        // Get the patient files and sort them according to the folders.
        $patient_files = $this->ci->db->select("patient_files", "*", array("patient_id" => $patientID, "ORDER" => array("created" => "DESC")));
        foreach ($patient_files as $k_file => $v_file) {
            // 'physician','senior_resident','emergency_resident','stroke_nurse','consultant','junior_resident','physiatrist','physiotherapist','occupational_therapist','student/resident'
            if (isset($v_file['user_id']) && $v_file['user_id']) {
                $getUserRole = $this->ci->db->get("users", array("user_role"), array("user_id" => $v_file['user_id']));
                $patient_files[$k_file]['user_role'] = $this->formatNames($getUserRole['user_role']);
            }
        }

        $count_patient_files = $this->ci->db->count("patient_files", array("patient_id" => $patientID));

        if ($count_patient_files > 0) {
            $patient['scans_exists'] = true;
            $patient['total_scans'] = $count_patient_files;
        } else {
            $patient['scans_exists'] = false;
            $patient['total_scans'] = $count_patient_files;
        }

        $patient['patient_files'] = array(
            "ncct" => array(),
            "cta_ctp" => array(),
            "mri" => array(),
            "mra" => array(),
        );
        foreach (@$patient_files as $key => $val) {
            if ($val['file_type'] !== "mp4") {
                $patient_files[$key]['file_thumb'] = uploads_url . '/thumb/compress.php?src=' . $val['file'] . '&w=200&h=200&zc=1';
                $patient_files[$key]['file'] = uploads_url . '/thumb/compress.php?src=' . $val['file'] . '&w=1000&zc=1';
            } else {
                $patient_files[$key]['file'] = uploads_url . $val['file'];
            }
            $patient['patient_files'][$val['scan_type']][] = $patient_files[$key];
        }

        // Get the patient's NIHSS
        $patient_nihss = $this->ci->db->select("patient_nihss", array("nihss_time", "nihss_value", "nihss_options"), array("patient_id" => $patientID));
        foreach ($patient_nihss as $key => $val) {
            $patient_nihss[$key]['nihss_options'] = json_decode($val['nihss_options']);
            $patient['patient_nihss'][$val['nihss_time']] = $patient_nihss[$key];
        }

        // Get the Patient's MRS
        $patient_mrs = $this->ci->db->select("patient_mrs", array("mrs_time", "mrs_options", "mrs_points"), array("patient_id" => $patientID));
        foreach ($patient_mrs as $key => $val) {
            $patient['patient_mrs'][$val['mrs_time']] = $patient_mrs[$key];
        }

        // Get the patient's conclusions/outcomes
        $patient_conclusion_outcomes = $this->ci->db->select("conclusion_outcomes_view", array("conclusion_type", "conclusion_value", "created", "user_name"), array("patient_id" => $patientID, "ORDER" => array("id" => "DESC")));
        $patient['patient_conclusion_outcomes'] = $patient_conclusion_outcomes;


        // Get the Calculated Times
        $patient['calculated_times'] = $this->getPatientCalculatedTimes($patientID);

        // Check if IVT Done
        if (isset($patient['calculated_times']['times']) && $patient['calculated_times']['times']['door_to_needle_time'] !== null) {
            $patient['show_ivt_icon'] = true;
        } else {
            $patient['show_ivt_icon'] = false;
        }
        if (isset($patient['calculated_times']) && $patient['calculated_times']['times'] && $patient['calculated_times']['times']['mt_started_time'] !== null) {
            $patient['show_mt_icon'] = true;
        } else {
            $patient['show_mt_icon'] = false;
        }

        // Mark that the patient has been checked by the user
        $this->markPatientChecked($patientID, $userId);
        return $patient;
    }

    public function getSinglePatient($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientId = $args['patientId'];
            $patientDetails = $this->getPatientDetails($patientId, $header_userId[0]);

            $output = $this->printData("success", $patientDetails);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function findPatientWithPatientCode($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientCode = $args['patientCode'];
            $getPatient = $this->ci->db->get("patients", array("id"), array("patient_code" => $patientCode));
            if (isset($getPatient) && $getPatient['id']) {
                $patientDetails = $this->getPatientDetails($getPatient['id'], $header_userId[0]);
                $output = $this->printData("success", array("patient_id" => $getPatient['id'], 'can_edit_details' => $patientDetails['can_edit_patient_details']));
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData("success", array("message" => "Patient not found!"));
                return $response->withJson($output, 404);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function getUserPatients($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            // Get the Logged in User's Center
            $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
            // Get the Center details


            $getCenterInfo = $this->ci->db->get("centers", array("id", "short_name", "is_hub", "main_hub"), array("id" => $getUserCenterId['center_id']));


            $mainHubId = 0;
            if (isset($getCenterInfo['main_hub']) && $getCenterInfo['main_hub']) {
                $mainHubId = $getCenterInfo['main_hub'];
            } else {
                $mainHubId = $getCenterInfo['id'];
            }



            $patientTypes = array();
            // $patientTypes['center_patients'] = array();
            $patientTypes['spoke_patients'] = array();
            $patientTypes['hub_spoke_patients'] = array();
            $patientTypes['hub_patients'] = array();


            if ($getCenterInfo['is_hub'] == "yes") {
                $getSpokePatients = $this->ci->db->select("user_patients", "*", array("AND" => array("is_hub" => "0", "in_transition" => "0", "hub_id" => $mainHubId), "ORDER" => array("created" => "DESC")));
            } else {
                $getSpokePatients = $this->ci->db->select("user_patients", "*", array("AND" => array("center_id" => $getUserCenterId['center_id'], "hub_id" => $mainHubId), "ORDER" => array("created" => "DESC")));
            }


            foreach ($getSpokePatients as $key => $val) {
                $patientId = $val['patient_id'];
                $patientDetails = $this->ci->db->get("patients", array("id", "created_by", "name", "patient_code", "age", "gender", "last_updated", "created"), array("id" => $patientId));
                $patientDetails['created'] = date("M d, Y h:i a", strtotime($patientDetails['created']));

                $patientDetails['assets'] = array(
                    "photos" => 0,
                    "videos" => 0
                );

                $patientDetails['assets']['photos'] = $this->ci->db->count("patient_files", array("AND" => array(
                    "patient_id" => $patientId,
                    "file_type" => "jpg",
                )));
                $patientDetails['assets']['videos'] = $this->ci->db->count("patient_files", array("AND" => array(
                    "patient_id" => $patientId,
                    "file_type[!]" => "jpg",
                )));

                // Get Stroke Type of the patient
                $getStrokeType = $this->ci->db->get("patient_scan_times", array(
                    "type_of_stroke",
                ), array("patient_id" => $patientId));
                if ($getStrokeType['type_of_stroke'] && $getStrokeType['type_of_stroke'] !== null) {
                    if ($getStrokeType['type_of_stroke'] == "Hemorrhagic") {
                        $patientDetails['show_stroke_type_text'] = true;
                        $patientDetails['stroke_type'] = "H";
                    } else {
                        $patientDetails['show_stroke_type_text'] = true;
                        $patientDetails['stroke_type'] = "I";
                    }
                } else {
                    $patientDetails['show_stroke_type_text'] = false;
                }

                $patientDetails['patient_checked'] = $this->patientCheckedbyUser($patientDetails['id'], $header_userId[0], $patientDetails['last_updated']);

                if ($val['is_center'] == "1") {
                    $patientDetails['is_center_patient'] = true;
                } else {
                    $patientDetails['is_center_patient'] = false;
                }

                if ($val['is_spoke'] == "1") {
                    $patientDetails['is_spoke_patient'] = true;
                } else {
                    $patientDetails['is_spoke_patient'] = false;
                }

                if ($val['is_hub'] == "1") {
                    $patientDetails['is_hub_patient'] = true;
                } else {
                    $patientDetails['is_hub_patient'] = false;
                }

                if ($val['in_transition'] == "1") {
                    $patientDetails['in_transition'] = true;
                } else {
                    $patientDetails['in_transition'] = false;
                }

                // Check if the user is from Hub or Spoke
                $getTheUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                $getCenterInfo = $this->ci->db->get("centers", array("id", "short_name", "is_hub"), array("id" => $getTheUserCenterId['center_id']));
                if ($getCenterInfo['is_hub'] == "yes") {
                    $patientDetails['is_user_from_hub'] = true;
                } else {
                    $patientDetails['is_user_from_hub'] = false;
                }



                if ($patientDetails['is_user_from_hub']) {
                    if ($patientDetails['is_hub_patient'] || ($patientDetails['in_transition'] && $patientDetails['is_spoke_patient'])) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } else {
                        $patientDetails['can_edit_patient_details'] = false;
                        $patientDetails['show_original_name'] = false;
                    }
                } else {
                    // Check if the user is from Spoke and Patient is not in Transition or is a Spoke Patient
                    if ($patientDetails['is_spoke_patient'] && !$patientDetails['in_transition']) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } elseif ($patientDetails['is_center_patient'] && !$patientDetails['in_transition']) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } else {
                        $patientDetails['can_edit_patient_details'] = false;
                        $patientDetails['show_original_name'] = false;
                    }
                }

                $patientCenter = $this->ci->db->get("centers", array("center_name"), array("id" => $val['center_id']));
                $patientDetails['center'] = $patientCenter['center_name'];

                // Get the Calculated Times
                $patientDetails['calculated_times'] = $this->getPatientCalculatedTimes($patientDetails['id']);
                if (isset($patientDetails['calculated_times']) && $patientDetails['calculated_times']['times'] && $patientDetails['calculated_times']['times']['door_to_needle_time'] !== null) {
                    $patientDetails['show_ivt_icon'] = true;
                } else {
                    $patientDetails['show_ivt_icon'] = false;
                }
                if (isset($patientDetails['calculated_times']) && $patientDetails['calculated_times']['times'] && $patientDetails['calculated_times']['times']['mt_started_time'] !== null) {
                    $patientDetails['show_mt_icon'] = true;
                } else {
                    $patientDetails['show_mt_icon'] = false;
                }

                $patientTypes['spoke_patients'][] = $patientDetails;
            }

            $getHubTransitionPatients = $this->ci->db->select("user_patients", "*", array(
                "AND" => array(
                    "hub_id" => $mainHubId,
                    "OR" => array("in_transition" => "1", "is_hub" => "1")
                ), "ORDER" => array("created" => "DESC")
            ));


            foreach ($getHubTransitionPatients as $key => $val) {
                $patientId = $val['patient_id'];

                $patientDetails = $this->ci->db->get("patients", array("id", "created_by", "name", "patient_code", "age", "gender", "last_updated", "created"), array("id" => $patientId));
                $patientDetails['created'] = date("M d, Y h:i a", strtotime($patientDetails['created']));

                $patientDetails['assets'] = array(
                    "photos" => 0,
                    "videos" => 0
                );
                $patientDetails['assets']['photos'] = $this->ci->db->count("patient_files", array("AND" => array(
                    "patient_id" => $patientId,
                    "file_type" => "jpg",
                )));
                $patientDetails['assets']['videos'] = $this->ci->db->count("patient_files", array("AND" => array(
                    "patient_id" => $patientId,
                    "file_type[!]" => "jpg",
                )));

                // Get Stroke Type of the patient
                $getStrokeType = $this->ci->db->get("patient_scan_times", array(
                    "type_of_stroke",
                ), array("patient_id" => $patientId));
                if ($getStrokeType['type_of_stroke'] && $getStrokeType['type_of_stroke'] !== null) {
                    if ($getStrokeType['type_of_stroke'] == "Hemorrhagic") {
                        $patientDetails['show_stroke_type_text'] = true;
                        $patientDetails['stroke_type'] = "H";
                    } else {
                        $patientDetails['show_stroke_type_text'] = true;
                        $patientDetails['stroke_type'] = "I";
                    }
                } else {
                    $patientDetails['show_stroke_type_text'] = false;
                }

                $patientDetails['patient_checked'] = $this->patientCheckedbyUser($patientDetails['id'], $header_userId[0], $patientDetails['last_updated']);

                if ($val['is_spoke'] == "1") {
                    $patientDetails['is_spoke_patient'] = true;
                } else {
                    $patientDetails['is_spoke_patient'] = false;
                }

                if ($val['is_hub'] == "1") {
                    $patientDetails['is_hub_patient'] = true;
                } else {
                    $patientDetails['is_hub_patient'] = false;
                }

                if ($val['is_center'] == "1") {
                    $patientDetails['is_center_patient'] = true;
                } else {
                    $patientDetails['is_center_patient'] = false;
                }

                if ($val['in_transition'] == "1") {
                    $patientDetails['in_transition'] = true;
                } else {
                    $patientDetails['in_transition'] = false;
                }

                // Check if the user is from Hub or Spoke
                $getTheUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                $getCenterInfo = $this->ci->db->get("centers", array("id", "short_name", "is_hub"), array("id" => $getTheUserCenterId['center_id']));
                if ($getCenterInfo['is_hub'] == "yes") {
                    $patientDetails['is_user_from_hub'] = true;
                } else {
                    $patientDetails['is_user_from_hub'] = false;
                }

                // // Check if the user is from Hub and Patient is in Transition or is a Hub Patient
                if ($patientDetails['is_user_from_hub']) {
                    if ($patientDetails['is_hub_patient'] || ($patientDetails['in_transition'] && ($patientDetails['is_spoke_patient'] || $patientDetails['is_center_patient']))) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } else {
                        $patientDetails['can_edit_patient_details'] = false;
                        $patientDetails['show_original_name'] = false;
                    }
                } else {
                    // Check if the user is from Spoke and Patient is not in Transition or is a Spoke Patient
                    if ($patientDetails['is_spoke_patient'] && !$patientDetails['in_transition']) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } elseif ($patientDetails['is_center_patient'] && !$patientDetails['in_transition']) {
                        $patientDetails['can_edit_patient_details'] = true;
                        $patientDetails['show_original_name'] = true;
                    } else {
                        $patientDetails['can_edit_patient_details'] = false;
                        $patientDetails['show_original_name'] = false;
                    }
                }

                $patientCenter = $this->ci->db->get("centers", array("center_name"), array("id" => $val['center_id']));
                $patientDetails['center'] = $patientCenter['center_name'];

                // Get the Calculated Times
                $patientDetails['calculated_times'] = $this->getPatientCalculatedTimes($patientDetails['id']);
                if (isset($patientDetails['calculated_times']) && $patientDetails['calculated_times']['times'] && $patientDetails['calculated_times']['times']['door_to_needle_time'] !== null) {
                    $patientDetails['show_ivt_icon'] = true;
                } else {
                    $patientDetails['show_ivt_icon'] = false;
                }
                if (isset($patientDetails['calculated_times']) && $patientDetails['calculated_times']['times'] && $patientDetails['calculated_times']['times']['mt_started_time'] !== null) {
                    $patientDetails['show_mt_icon'] = true;
                } else {
                    $patientDetails['show_mt_icon'] = false;
                }

                $patientTypes['hub_patients'][] = $patientDetails;
            }


            $patientTypes["centers"] = array();

            if ($getCenterInfo['is_hub'] == "yes") {
                $hubSpokePatients = array();
                foreach ($patientTypes['spoke_patients'] as $patient) {
                    $hubSpokePatients[$patient['center']] = array(
                        "name" => $patient['center'],
                        "patients" => array(),
                    );
                }
                foreach ($patientTypes['spoke_patients'] as $patient) {
                    $hubSpokePatients[$patient['center']]['patients'][] = $patient;
                }
                $patientTypes["hub_spoke_patients"] =  array_values($hubSpokePatients);

                $patientTypes["centers"] = array_keys($hubSpokePatients);
            }

            $output = $this->printData("success", $patientTypes);
            return $response->withJson($output, 200);
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updateBasicData($request, $response, $args)
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

            if (!isset($data['first_name']) || $data['first_name'] == "") {
                $errors[] = "First name is required";
            }
            if (!isset($data['first_name']) || $data['first_name'] == "") {
                $errors[] = "First name is required";
            }
            if (!isset($data['weakness_side']) || $data['weakness_side'] == "") {
                $errors[] = "Weakness side is required";
            }

            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientBasicData = array();

                if (isset($data['first_name']) && $data['first_name']) {
                    $patientBasicData['first_name'] = $data['first_name'];
                }
                if (isset($data['last_name']) && $data['last_name']) {
                    $patientBasicData['last_name'] = $data['last_name'];
                }
                if (isset($data['name']) && $data['name']) {
                    $patientBasicData['name'] = $data['first_name'] . " " . $data['last_name'];
                }

                $patientBasicData['date_of_birth'] = $this->cleanValue($data['date_of_birth']);

                //Calculate Age of the patient
                $today = date("Y-m-d");
                $diff = date_diff(date_create($patientBasicData['date_of_birth']), date_create($today));
                $patientBasicData['age'] = $diff->format('%y');

                if (isset($data['gender']) && $data['gender']) {
                    $patientBasicData['gender'] = $data['gender'];
                }
                if (isset($data['contact_number']) && $data['contact_number']) {
                    $patientBasicData['contact_number'] = $data['contact_number'];
                }
                if (isset($data['contact_number']) && $data['contact_number']) {
                    $patientBasicData['contact_number'] = $data['contact_number'];
                }
                if (isset($data['address']) && $data['address']) {
                    $patientBasicData['address'] = $data['address'];
                }
                if (isset($data['handedness']) && $data['handedness']) {
                    $patientBasicData['handedness'] = $data['handedness'];
                }

                if (isset($data['is_wakeup_stroke']) && $data['is_wakeup_stroke']) {
                    $patientBasicData['is_wakeup_stroke'] = $data['is_wakeup_stroke'];
                }

                if (isset($data['is_hospital_stroke']) && $data['is_hospital_stroke']) {
                    $patientBasicData['is_hospital_stroke'] = $data['is_hospital_stroke'];
                }

                if (isset($data['notes']) && $data['notes']) {
                    $patientBasicData['notes'] = $data['notes'];
                }

                if (isset($data['weakness_side']) && $data['weakness_side']) {
                    $patientBasicData['weakness_side'] = $data['weakness_side'];
                }

                if (isset($data['facial_deviation']) && $data['facial_deviation']) {
                    $patientBasicData['facial_deviation'] = $data['facial_deviation'];
                }

                if (isset($data['co_morbidities']) && $data['co_morbidities']) {
                    $patientBasicData['co_morbidities'] = $data['co_morbidities'];
                }
                if (isset($data['similar_episodes_in_past']) && $data['similar_episodes_in_past']) {
                    $patientBasicData['similar_episodes_in_past'] = $data['similar_episodes_in_past'];
                }

                if (isset($data['similar_episodes_in_past_text']) && $data['similar_episodes_in_past_text']) {
                    $patientBasicData['similar_episodes_in_past_text'] = $data['similar_episodes_in_past_text'];
                }

                if (isset($data['inclusion_exclusion_assessed']) && $data['inclusion_exclusion_assessed']) {
                    $patientBasicData['inclusion_exclusion_assessed'] = $data['inclusion_exclusion_assessed'];
                }

                if (isset($data['bp_x']) && $data['bp_x']) {
                    $patientBasicData['bp_x'] = $data['bp_x'];
                }
                if (isset($data['bp_y']) && $data['bp_y']) {
                    $patientBasicData['bp_y'] = $data['bp_y'];
                }
                if (isset($data['rbs']) && $data['rbs']) {
                    $patientBasicData['rbs'] = $data['rbs'];
                }
                if (isset($data['inr']) && $data['inr']) {
                    $patientBasicData['inr'] = $data['inr'];
                }
                if (isset($data['aspects']) && $data['aspects']) {
                    $patientBasicData['aspects'] = $data['aspects'];
                }
                if (isset($data['body_weight']) && $data['body_weight']) {
                    $patientBasicData['body_weight'] = $data['body_weight'];
                }
                if (isset($data['blood_group']) && $data['blood_group']) {
                    $patientBasicData['blood_group'] = $data['blood_group'];
                }

                if (isset($data['admission_time']) && $data['admission_time']) {
                    $patientBasicData['admission_time'] = $data['admission_time'];
                }

                $patientBasicData['last_updated'] = date("Y-m-d H:i:s");

                $updatePatientBasicDetails = $this->ci->db->update("patients", $patientBasicData, array("id" => $data['patient_id']));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "basic_details",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", "Basic Details updates successfully.");
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function deletePatientFile($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            if (!isset($data['file_id']) || $data['file_id'] == "") {
                $errors[] = "file_id is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $checkFile = $this->ci->db->get("patient_files", array("id"), array("id" => $data['file_id']));
                if (isset($checkFile['id']) && $checkFile['id']) {
                    $deleteFile = $this->ci->db->delete("patient_files", array("id" => $data['file_id']));
                    $output = $this->printData("success", "file_deleted");
                    return $response->withJson($output, 200);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function movePatientFile($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if (!isset($data['file_id']) || $data['file_id'] == "") {
                $errors[] = "file_id is required";
            }
            if (!isset($data['scan_type']) || $data['scan_type'] == "") {
                $errors[] = "scan_type is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $update_file = $this->ci->db->update("patient_files", array("scan_type" => $data['scan_type']), array("id" => $data['file_id']));

                $output = $this->printData("success", "file_moved");
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function addPatientScanFile($request, $response, $args)
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
            if (!isset($data['file_type']) || $data['file_type'] == "") {
                $errors[] = "file_type is required";
            }
            if (!isset($data['scan_type']) || $data['scan_type'] == "") {
                $errors[] = "scan_type is required";
            }
            if (!isset($data['file']) || $data['file'] == "") {
                $errors[] = "file is required";
            }

            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientFile = array();
                $patientFile['patient_id'] = $data['patient_id'];
                $patientFile['file_type'] = $data['file_type'];
                $patientFile['scan_type'] = $data['scan_type'];
                $patientFile['file'] = $data['file'];
                $patientFile['user_id'] = $header_userId[0];

                $insert_file = $this->ci->db->insert("patient_files", $patientFile);
                if ($insert_file) {
                    $output = $this->printData("success", array("message" => "file_uploaded"));
                    return $response->withJson($output, 200);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updatePatientPresentation($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientPresentation = array();
                if (isset($data['loc']) && $data['loc']) {
                    $patientPresentation['loc'] = $data['loc'];
                }

                if (isset($data['pol_right_ul']) && $data['pol_right_ul']) {
                    $patientPresentation['pol_right_ul'] = $data['pol_right_ul'];
                }
                if (isset($data['pol_right_ll']) && $data['pol_right_ll']) {
                    $patientPresentation['pol_right_ll'] = $data['pol_right_ll'];
                }
                if (isset($data['pol_left_ul']) && $data['pol_left_ul']) {
                    $patientPresentation['pol_left_ul'] = $data['pol_left_ul'];
                }
                if (isset($data['pol_left_ll']) && $data['pol_left_ll']) {
                    $patientPresentation['pol_left_ll'] = $data['pol_left_ll'];
                }

                $patientPresentation['last_updated'] = date("Y-m-d H:i:s");

                $updatePatientPresentation = $this->ci->db->update("patient_presentation", $patientPresentation, array("patient_id" => $data['patient_id']));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "presentation",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", "Presentation data updated successfully.");
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updatePatientComplications($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientComplications = array();

                // Bedsore
                if (isset($data['bed_sore']) && $data['bed_sore']) {
                    $patientComplications['bed_sore'] = $data['bed_sore'];
                }
                if (isset($data['bed_sore_site']) && $data['bed_sore_site']) {
                    $patientComplications['bed_sore_site'] = $data['bed_sore_site'];
                }
                if (isset($data['bed_sore_degree']) && $data['bed_sore_degree']) {
                    $patientComplications['bed_sore_degree'] = $data['bed_sore_degree'];
                }
                if (isset($data['bed_sore_duration']) && $data['bed_sore_duration']) {
                    $patientComplications['bed_sore_duration'] = $data['bed_sore_duration'];
                }
                if (isset($data['bed_sore_photo']) && $data['bed_sore_photo']) {
                    $patientComplications['bed_sore_photo'] = $data['bed_sore_photo'];
                }
                // Aspiration
                if (isset($data['aspiration']) && $data['aspiration']) {
                    $patientComplications['aspiration'] = $data['aspiration'];
                }
                if (isset($data['aspiration_duration']) && $data['aspiration_duration']) {
                    $patientComplications['aspiration_duration'] = $data['aspiration_duration'];
                }

                // Deep Vein Thrombosis
                if (isset($data['deep_vein_thormobosis']) && $data['deep_vein_thormobosis']) {
                    $patientComplications['deep_vein_thormobosis'] = $data['deep_vein_thormobosis'];
                }
                if (isset($data['deep_vein_thormobosis_site']) && $data['deep_vein_thormobosis_site']) {
                    $patientComplications['deep_vein_thormobosis_site'] = $data['deep_vein_thormobosis_site'];
                }
                if (isset($data['deep_vein_thormobosis_duration']) && $data['deep_vein_thormobosis_duration']) {
                    $patientComplications['deep_vein_thormobosis_duration'] = $data['deep_vein_thormobosis_duration'];
                }

                // Frozen Shoulder
                if (isset($data['frozen_shoulder']) && $data['frozen_shoulder']) {
                    $patientComplications['frozen_shoulder'] = $data['frozen_shoulder'];
                }
                if (isset($data['frozen_shoulder_site']) && $data['frozen_shoulder_site']) {
                    $patientComplications['frozen_shoulder_site'] = $data['frozen_shoulder_site'];
                }
                if (isset($data['frozen_shoulder_duration']) && $data['frozen_shoulder_duration']) {
                    $patientComplications['frozen_shoulder_duration'] = $data['frozen_shoulder_duration'];
                }

                // Contracture
                if (isset($data['contracture']) && $data['contracture']) {
                    $patientComplications['contracture'] = $data['contracture'];
                }
                if (isset($data['contracture_site']) && $data['contracture_site']) {
                    $patientComplications['contracture_site'] = $data['contracture_site'];
                }
                if (isset($data['contracture_duration']) && $data['contracture_duration']) {
                    $patientComplications['contracture_duration'] = $data['contracture_duration'];
                }

                // Spasticity
                if (isset($data['spasticity']) && $data['spasticity']) {
                    $patientComplications['spasticity'] = $data['spasticity'];
                }
                if (isset($data['spasticity_site']) && $data['spasticity_site']) {
                    $patientComplications['spasticity_site'] = $data['spasticity_site'];
                }
                if (isset($data['spasticity_duration']) && $data['spasticity_duration']) {
                    $patientComplications['spasticity_duration'] = $data['spasticity_duration'];
                }

                // Catheter Associated Urinary Tract Infection
                if (isset($data['cauti']) && $data['cauti']) {
                    $patientComplications['cauti'] = $data['cauti'];
                }
                if (isset($data['cauti_duration']) && $data['cauti_duration']) {
                    $patientComplications['cauti_duration'] = $data['cauti_duration'];
                }

                // Others
                if (isset($data['others']) && $data['others']) {
                    $patientComplications['others'] = $data['others'];
                }
                if (isset($data['others_information']) && $data['others_information']) {
                    $patientComplications['others_information'] = $data['others_information'];
                }
                if (isset($data['others_duration']) && $data['others_duration']) {
                    $patientComplications['others_duration'] = $data['others_duration'];
                }

                $patientComplications['last_updated'] = date("Y-m-d H:i:s");
                $updatePatientComplications = $this->ci->db->update("patient_complications", $patientComplications, array("patient_id" => $data['patient_id']));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "complications",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", "Complications updated successfully.");
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updateScanTimesofPatient($request, $response, $args)
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
            if (!isset($data['ct_scan_time']) || $data['ct_scan_time'] == "") {
                $errors[] = "CT/CTA Time is required";
            }
            if (!isset($data['type_of_stroke']) || $data['type_of_stroke'] == "") {
                $errors[] = "Type of Stroke is required";
            }
            // if (!isset($data['aspects']) || $data['aspects'] == "") {
            //     $errors[] = "Aspects is required";
            // }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientScanTimes = array();

                // Bedsore
                if (isset($data['ct_scan_time']) && $data['ct_scan_time']) {
                    $patientScanTimes['ct_scan_time'] = $data['ct_scan_time'];
                }
                if (isset($data['mr_mra_time']) && $data['mr_mra_time']) {
                    $patientScanTimes['mr_mra_time'] = $data['mr_mra_time'];
                }
                if (isset($data['dsa_time_completed']) && $data['dsa_time_completed']) {
                    $patientScanTimes['dsa_time_completed'] = $data['dsa_time_completed'];
                }
                if (isset($data['type_of_stroke']) && $data['type_of_stroke']) {
                    $patientScanTimes['type_of_stroke'] = $data['type_of_stroke'];
                }

                if (isset($data['lvo'])) {
                    $patientScanTimes['lvo'] = $data['lvo'];
                }
                if (isset($data['lvo_types']) && $data['lvo_types']) {
                    $patientScanTimes['lvo_types'] = $data['lvo_types'];
                }
                if (isset($data['lvo_site']) && $data['lvo_site']) {
                    $patientScanTimes['lvo_site'] = $data['lvo_site'];
                }
                if (isset($data['aspects'])) {
                    $patientScanTimes['aspects'] = $data['aspects'];
                }

                $patientScanTimes['last_updated'] = date("Y-m-d H:i:s");
                $updatePatientScanTimes = $this->ci->db->update("patient_scan_times", $patientScanTimes, array("patient_id" => $data['patient_id']));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array(
                    "scans_needed" => "0",
                    "scans_completed" => "1",
                    "last_updated" => date(
                        "Y-m-d H:i:s"
                    )
                ), array("id" => $data['patient_id']));

                // if aspects exists update it in patients
                if (isset($data['aspects'])) {
                    $this->ci->db->update(
                        "patients",
                        array(
                            "aspects" => $patientScanTimes['aspects'],
                            "last_updated" => date("Y-m-d H:i:s")
                        ),
                        array("id" => $data['patient_id'])
                    );
                }

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "scan_details",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", array("message" => "Scan times updated successfully."));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updatePatientMedications($request, $response, $args)
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
            if (!isset($data['medicine']) || $data['medicine'] == "") {
                $errors[] = "Select a medicine";
            }
            if (!isset($data['patient_weight']) || $data['patient_weight'] == "") {
                $errors[] = "Enter patient's body weight";
            }
            // if (!isset($data['door_to_needle_time']) || $data['door_to_needle_time'] == "") {
            //     $errors[] = "Please specify door to needle time.";
            // }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientMedicationsData = array();

                if (isset($data['medicine']) && $data['medicine']) {
                    $patientMedicationsData['medicine'] = $data['medicine'];
                }
                if (isset($data['patient_weight']) && $data['patient_weight']) {
                    $patientMedicationsData['patient_weight'] = $data['patient_weight'];
                }
                if (isset($data['dose_value']) && $data['dose_value']) {
                    $patientMedicationsData['dose_value'] = $data['dose_value'];
                }
                if (isset($data['total_dose']) && $data['total_dose']) {
                    $patientMedicationsData['total_dose'] = $data['total_dose'];
                }
                if (isset($data['bolus_dose']) && $data['bolus_dose']) {
                    $patientMedicationsData['bolus_dose'] = $data['bolus_dose'];
                }
                if (isset($data['infusion_dose']) && $data['infusion_dose']) {
                    $patientMedicationsData['infusion_dose'] = $data['infusion_dose'];
                }
                if (isset($data['door_to_needle_time']) && $data['door_to_needle_time']) {
                    $patientMedicationsData['door_to_needle_time'] = $data['door_to_needle_time'];
                } else {
                    $patientMedicationsData['door_to_needle_time'] = null;
                }
                $patientMedicationsData['last_updated'] = date("Y-m-d H:i:s");

                $updatePatientMedications = $this->ci->db->update("patient_ivt_medications", $patientMedicationsData, array("patient_id" => $data['patient_id']));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array(
                    "ivt_medication" => "1",
                    "body_weight" => $data['patient_weight'],
                    "last_updated" => date("Y-m-d H:i:s"),
                ), array("id" => $data['patient_id']));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));
                // Update last updated field in Patients table

                // Update a new status and update
                $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                $checkCenter = $this->ci->db->get("centers", array("is_hub", "is_center"), array("id" => $getUserCenterId['center_id']));
                if ($checkCenter['is_hub'] == "yes") {
                    $statusId = "10";
                } else {
                    if ($checkCenter['is_center'] == "yes") {
                        $statusId = "22";
                    } else {
                        $statusId = "9";
                    }
                }
                $insertStatusData = array();
                $insertStatusData['user_id'] = $header_userId[0];
                $insertStatusData['patient_id'] = $data['patient_id'];
                $insertStatusData['status_id'] = $statusId;
                $insertStatusData['center_id'] = $getUserCenterId['center_id'];
                $insertStatusData['created'] = date("Y-m-d H:i:s");
                $insertStatus = $this->ci->db->insert("transition_statuses", $insertStatusData);
                if ($insertStatus) {
                    // Update Status for IVT Bolus
                    $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));
                    $getStatusInfo = $this->ci->db->get("status_types", array("title"), array("id" => $data['status_id']));

                    $getPushIds = $this->getOneSignalIdsOfTheUsers($data['patient_id']);
                    if (count($getPushIds['pushIDs']) > 0) {
                        $pushData = array();
                        $pushData['title'] = "IVT (" . $getPatientNameCode['name'] . ")";
                        $pushData['message'] = "IVT Eligible, proceed for thrombolysis.";
                        $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                        $pushData['devices'] = $getPushIds['pushIDs'];
                        $this->sendPush($pushData);

                        // Send SMS
                        $phoneNumbers = array();
                        foreach ($getPushIds['mobileNumbers'] as $phoneNumber) {
                            $phoneNumbers[] = "+91" . $phoneNumber;
                        }
                        $smsData = array(
                            "to" => implode("<", $phoneNumbers),
                            "message" => "IVT Eligible (" . $getPatientNameCode['name'] . ") proceed for thrombolysis. snetchd://strokenetchandigarh.com/patient_detail/" . $data['patient_id'],
                        );
                        $this->sendSMS($smsData);
                    }
                }

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "medications",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", array("message" => "Medications updated successfully."));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updatePatientContradictions($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientContradictionsData = array();

                if (isset($data['contradictions_data']) && $data['contradictions_data']) {
                    $patientContradictionsData['contradictions_data'] = $data['contradictions_data'];
                } else {
                    $patientContradictionsData['contradictions_data'] = null;
                }
                if (isset($data['absolute_score'])) {
                    $patientContradictionsData['absolute_score'] = $data['absolute_score'];
                }
                if (isset($data['relative_score'])) {
                    $patientContradictionsData['relative_score'] = $data['relative_score'];
                }
                if (isset($data['ivt_eligible'])) {
                    $patientContradictionsData['ivt_eligible'] = $data['ivt_eligible'];
                }
                $patientContradictionsData['checked'] = "1";
                $patientContradictionsData['last_updated'] = date("Y-m-d H:i:s");

                $updatePatientContradictions = $this->ci->db->update("patient_contradictions", $patientContradictionsData, array("patient_id" => $data['patient_id']));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array(
                    "ivt_medication" => "1",
                    "body_weight" => $data['patient_weight'],
                    "last_updated" => date("Y-m-d H:i:s"),
                ), array("id" => $data['patient_id']));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));
                // Update last updated field in Patients table

                // Update a new status and update

                if ($data['ivt_eligible'] == "0" && $data['absolute_score'] > 0) {
                    $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                    $checkCenter = $this->ci->db->get("centers", array("is_hub", "is_center"), array("id" => $getUserCenterId['center_id']));

                    if ($checkCenter['is_hub'] == "yes") {
                        $statusId = "12";
                    } else {
                        if ($checkCenter['is_center'] == "yes") {
                            $statusId = "23";
                        } else {
                            $statusId = "11";
                        }
                    }
                    $insertStatusData = array();
                    $insertStatusData['user_id'] = $header_userId[0];
                    $insertStatusData['patient_id'] = $data['patient_id'];
                    $insertStatusData['status_id'] = $statusId;
                    $insertStatusData['center_id'] = $getUserCenterId['center_id'];
                    $insertStatusData['created'] = date("Y-m-d H:i:s");
                    $insertStatus = $this->ci->db->insert("transition_statuses", $insertStatusData);
                }

                // if ($insertStatus) {
                //     // Update Status for IVT Bolus
                //     $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));
                //     $getStatusInfo = $this->ci->db->get("status_types", array("title"), array("id" => $data['status_id']));
                //     $getRadioPushIDs = $this->getOneSignalIdsOfTheUsers($data['patient_id']);
                //     if (count($getRadioPushIDs) > 0) {
                //         $pushData = array();
                //         $pushData['title'] = "IVT (".$getPatientNameCode['name'].")";
                //         $pushData['message'] = "";
                //         $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                //         $pushData['devices'] = $getRadioPushIDs;
                //         // $this->sendPush($pushData);
                //     }
                // }

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "ivt_checklist",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", array("message" => "IVT Checklist updated successfully."));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function scansUploadedAlertToTeam($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                // $patientScanTimes = array();

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array(
                    "scans_uploaded" => "1",
                    "last_updated" => date(
                        "Y-m-d H:i:s"
                    )
                ), array("id" => $data['patient_id']));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                // Send Push notfiication to the teams
                // Get all the user's Push IDs where department not equal to Radio Diagnosis
                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));
                // $getPushIds = $this->getOneSignalIdsOfTheRadioWithoutDiagonisUsers($data['patient_id']);
                $getPushIds = $this->getOneSignalIdsOfTheUsers($data['patient_id']);

                if (count($getPushIds['pushIDs']) > 0) {

                    $pushData = array();
                    $pushData['title'] = "Scans Uploaded! " . $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . ")";
                    $pushData['message'] = "New Scans available for review.";
                    $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                    $pushData['devices'] = $getPushIds['pushIDs'];
                    $this->sendPush($pushData);

                    // Send SMS
                    // $phoneNumbers = array();
                    // foreach ($getPushIds['mobileNumbers'] as $phoneNumber) {
                    //     $phoneNumbers[] = "+91" . $phoneNumber;
                    // }
                    // $smsData = array(
                    //     "to" => implode("<", $phoneNumbers),
                    //     "message" => "Scans Uploaded! " . $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . ") snetchd://strokenetchandigarh.com/patient_detail/" . $data['patient_id'],
                    // );
                    // $this->sendSMS($smsData);

                    // Global Status
                    $updateData = array(
                        "user_id" => $header_userId[0],
                        "patient_id" => $data['patient_id'],
                        "update_type" => "scans_uploaded",
                        "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                        "last_updated" => date("Y-m-d H:i:s"),
                    );
                    $this->updatePatientStatus($updateData);
                    // Global Status

                    $output = $this->printData("success", array("message" => "ALERT_SENT"));
                    return $response->withJson($output, 200);
                } else {
                    $output = $this->printData("success", array("message" => "ALERT_NOT_SENT"));
                    return $response->withJson($output, 200);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updateMRSofPatient($request, $response, $args)
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
            if (!isset($data['mrs_time']) || $data['mrs_time'] == "") {
                $errors[] = "mrs_time is required";
            }
            if (!isset($data['mrs_options']) || $data['mrs_options'] == "") {
                $errors[] = "mrs_options is required";
            }
            if (!isset($data['mrs_points']) || $data['mrs_points'] == "") {
                $errors[] = "mrs_points is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientMRS = array();
                $patientMRS['mrs_time'] = $data['mrs_time'];
                $patientMRS['mrs_options'] = $data['mrs_options'];
                $patientMRS['mrs_points'] = $data['mrs_points'];

                $updatePatientMRS = $this->ci->db->update("patient_mrs", $patientMRS, array(
                    "AND" => array(
                        "patient_id" => $data['patient_id'],
                        "mrs_time" => $data['mrs_time'],
                    ),
                ));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                $patient_mrs = $this->ci->db->select("patient_mrs", array("mrs_time", "mrs_options"), array("patient_id" => $data['patient_id']));

                $mrsData = array();
                foreach ($patient_mrs as $key => $val) {
                    $mrsData[$val['mrs_time']] = $patient_mrs[$key];
                }

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "mrs",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", array("message" => "MRS was updated successfully", "mrs_data" => $mrsData));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function updateNIHSSofPatient($request, $response, $args)
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
            if (!isset($data['nihss_time']) || $data['nihss_time'] == "") {
                $errors[] = "nihss_time is required";
            }
            if (!isset($data['nihss_value']) || $data['nihss_value'] == "") {
                $errors[] = "nihss_value is required";
            }

            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientNIHSS = array();
                $patientNIHSS['nihss_time'] = $data['nihss_time'];
                $patientNIHSS['nihss_value'] = $data['nihss_value'];

                if (isset($data['nihss_options']) && $data['nihss_options']) {
                    $patientNIHSS['nihss_options'] = $data['nihss_options'];
                }

                $updatePatientNIHSS = $this->ci->db->update("patient_nihss", $patientNIHSS, array(
                    "AND" => array(
                        "patient_id" => $data['patient_id'],
                        "nihss_time" => $data['nihss_time'],
                    ),
                ));

                // Update last updated field in Patients table
                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));
                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s")), array("patient_id" => $data['patient_id']));

                $patient_nihss = $this->ci->db->select("patient_nihss", array("nihss_time", "nihss_value", "nihss_options"), array("patient_id" => $data['patient_id']));
                $nihssData = array();
                foreach ($patient_nihss as $key => $val) {
                    $nihssData[$val['nihss_time']] = $patient_nihss[$key];
                }

                // Global Status
                $updateData = array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "update_type" => "nihss",
                    "url" => 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'],
                    "last_updated" => date("Y-m-d H:i:s"),
                );
                $this->updatePatientStatus($updateData);
                // Global Status

                $output = $this->printData("success", array("message" => "NIHSS was updated successfully", "nihss_data" => $nihssData));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function addPatient($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();

            if (!isset($data['name']) || $data['name'] == "") {
                $errors[] = "Name is required";
            }
            if (!isset($data['datetime_of_stroke']) || $data['datetime_of_stroke'] == "") {
                $errors[] = "Date/Time of Stroke is required";
            }
            if (!isset($data['weakness_side']) || $data['weakness_side'] == "") {
                $errors[] = "Weakness side is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $patientData = array();

                if (isset($data['first_name']) && $data['first_name']) {
                    $patientData['first_name'] = $data['first_name'];
                    $patientData['name'] = $data['first_name'];
                }
                if (isset($data['last_name']) && $data['last_name']) {
                    $patientData['last_name'] = $data['last_name'];
                    $patientData['name'] = $data['first_name'] . " " . $data['last_name'];
                }

                if (isset($data['name']) && $data['name']) {
                    $explode_name = explode(" ", $data['name']);
                    if (isset($explode_name[0])) {
                        $patientData['first_name'] = $explode_name[0];
                    }
                    if (isset($explode_name[1])) {
                        $patientData['last_name'] = $explode_name[1];
                    }
                    $patientData['name'] = $data['name'];
                }

                if (isset($data['date_of_birth']) && $data['date_of_birth']) {
                    $patientData['date_of_birth'] = $this->cleanValue($data['date_of_birth']);
                    $today = date("Y-m-d");
                    $diff = date_diff(date_create($patientData['date_of_birth']), date_create($today));
                    $patientData['age'] = $diff->format('%y');
                }

                if (isset($data['age']) && $data['age']) {
                    $patientData['age'] = $data['age'];
                    $today = date("Y-m-d");
                    $pastDate = date('Y-m-d', strtotime('-' . $data['age'] . ' years', strtotime($today)));
                    $patientData['date_of_birth'] = $pastDate;
                }

                if (isset($data['gender']) && $data['gender']) {
                    $patientData['gender'] = $this->cleanValue($data['gender']);
                }

                if (isset($data['weakness_side']) && $data['weakness_side']) {
                    $patientData['weakness_side'] = $this->cleanValue($data['weakness_side']);
                }
                $patientData['created_by'] = $header_userId[0];

                if (isset($data['contact_number']) && $data['contact_number']) {
                    $patientData['contact_number'] = $data['contact_number'];
                }
                if (isset($data['address']) && $data['address']) {
                    $patientData['address'] = $data['address'];
                }

                if (isset($data['covid_score']) && $data['covid_score']) {
                    $patientData['covid_score'] = $data['covid_score'];
                }
                if (isset($data['covid_values']) && $data['covid_values']) {
                    $patientData['covid_values'] = $data['covid_values'];
                }

                $getTheUserCenterId = $this->ci->db->get("users", array("center_id", "phone_number"), array("user_id" => $header_userId[0]));
                $patientData['center_id'] = $getTheUserCenterId['center_id'];

                // Time Calculation
                $removeT = str_replace("T", " ", $data['datetime_of_stroke']);
                $removeZ = str_replace("Z", " ", $removeT);
                $patientData['datetime_of_stroke'] = $removeZ;
                $patientData['datetime_of_stroke_timeends'] = date("Y-m-d H:i:s", strtotime($removeZ . "+4.5 hours"));
                $patientData['last_updated'] = date("Y-m-d H:i:s");
                $patientData['created'] = date("Y-m-d H:i:s");
                $patientData['admission_time'] = date("Y-m-d H:i:s");

                $patientData['datetime_of_stroke_fortyfive_deadline'] = date("Y-m-d H:i:s", strtotime($patientData['created'] . "+45 minutes"));

                $insert_patient = $this->ci->db->insert("patients", $patientData);
                if ($insert_patient) {

                    // Create slots for push notifications
                    $getCenterInfo = $this->ci->db->get("centers", array("id", "center_name", "short_name", "is_hub", "main_hub", "is_center", "is_spoke"), array("id" => $getTheUserCenterId['center_id']));

                    $generatePatientCode = $getCenterInfo['short_name'] . "-" . date("ymd") . $insert_patient;

                    $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $insert_patient));

                    $this->ci->db->update("patients", array("patient_code" => $generatePatientCode), array("id" => $insert_patient));

                    // Check if the patient is ineligible for IVT
                    $currentTime = date("Y-m-d H:i:s");
                    $timeOfStroke = date("Y-m-d H:i:s", strtotime($data['datetime_of_stroke'] . " -330 mins"));
                    $calculateDifference = $this->calculateTimeBetweenTwoTimes($timeOfStroke, $currentTime);

                    // Add Transition Statuses
                    if ($getCenterInfo['is_hub'] == "yes") {
                        $isHubUser = "1";
                        $isSpokeUser = "0";
                        $isCenterUser = "0";

                        // Hub/Spoke In Status Insert
                        $this->ci->db->insert("transition_statuses", array(
                            "patient_id" => $insert_patient,
                            "center_id" => $getTheUserCenterId['center_id'],
                            "status_id" => 1, // "Hub in status ID"
                            "user_id" => $header_userId[0],
                            "created" => date("Y-m-d H:i:s"),
                        ));
                        if ($calculateDifference['time_in_seconds'] > 16200) {
                            $this->ci->db->insert("transition_statuses", array(
                                "patient_id" => $insert_patient,
                                "center_id" => $getTheUserCenterId['center_id'],
                                "status_id" => 12,
                                "user_id" => $header_userId[0],
                                "created" => date("Y-m-d H:i:s"),
                            ));
                        }
                    } else {
                        $isHubUser = "0";
                        $isSpokeUser = "0";
                        $isCenterUser = "0";

                        if ($getCenterInfo['is_center'] == "yes") {
                            $isHubUser = "0";
                            $isSpokeUser = "0";
                            $isCenterUser = "1";

                            $this->ci->db->insert("transition_statuses", array(
                                "patient_id" => $insert_patient,
                                "center_id" => $getTheUserCenterId['center_id'],
                                "status_id" => 19, // "Center in status ID"
                                "user_id" => $header_userId[0],
                                "created" => date("Y-m-d H:i:s"),
                            ));
                            if ($calculateDifference['time_in_seconds'] > 16200) {
                                $this->ci->db->insert("transition_statuses", array(
                                    "patient_id" => $insert_patient,
                                    "center_id" => $getTheUserCenterId['center_id'],
                                    "status_id" => 23,
                                    "user_id" => $header_userId[0],
                                    "created" => date("Y-m-d H:i:s"),
                                ));
                            }
                        } else {
                            $isHubUser = "0";
                            $isSpokeUser = "1";
                            $isCenterUser = "0";

                            $this->ci->db->insert("transition_statuses", array(
                                "patient_id" => $insert_patient,
                                "center_id" => $getTheUserCenterId['center_id'],
                                "status_id" => 2, // "Spoke in status ID"
                                "user_id" => $header_userId[0],
                                "created" => date("Y-m-d H:i:s"),
                            ));
                            if ($calculateDifference['time_in_seconds'] > 16200) {
                                $this->ci->db->insert("transition_statuses", array(
                                    "patient_id" => $insert_patient,
                                    "center_id" => $getTheUserCenterId['center_id'],
                                    "status_id" => 11,
                                    "user_id" => $header_userId[0],
                                    "created" => date("Y-m-d H:i:s"),
                                ));
                            }
                        }
                    }


                    // Insert patient/users user_patients
                    $this->ci->db->insert("user_patients", array("center_id" => $getCenterInfo['id'], "patient_id" => $insert_patient, "user_id" => $header_userId[0], "is_hub" => $isHubUser, "hub_id" => $isHubUser ? $getCenterInfo['id'] : $getCenterInfo['main_hub'], "is_spoke" => $isSpokeUser, "is_center" => $isCenterUser, "in_transition" => "0", "last_updated" => date("Y-m-d H:i:s")));


                    // Patient Presentations Record
                    $this->ci->db->insert("patient_presentation", array("patient_id" => $insert_patient));

                    // Patient Complications Record
                    $this->ci->db->insert("patient_complications", array("patient_id" => $insert_patient));

                    // Patient Contradictions Record
                    $this->ci->db->insert("patient_contradictions", array("patient_id" => $insert_patient));

                    // Patient Scan Times Record
                    $this->ci->db->insert("patient_scan_times", array("patient_id" => $insert_patient));

                    // Patient IVT Medications Record
                    $this->ci->db->insert("patient_ivt_medications", array("patient_id" => $insert_patient));

                    // NIHSS Insertions
                    $this->ci->db->insert("patient_nihss", array("patient_id" => $insert_patient, "nihss_time" => "admission"));
                    $this->ci->db->insert("patient_nihss", array("patient_id" => $insert_patient, "nihss_time" => "24_hours"));
                    $this->ci->db->insert("patient_nihss", array("patient_id" => $insert_patient, "nihss_time" => "discharge"));

                    // MRS Insertions
                    $this->ci->db->insert("patient_mrs", array("patient_id" => $insert_patient, "mrs_time" => "discharge"));
                    $this->ci->db->insert("patient_mrs", array("patient_id" => $insert_patient, "mrs_time" => "1_month"));
                    $this->ci->db->insert("patient_mrs", array("patient_id" => $insert_patient, "mrs_time" => "3_months"));

                    if (isset($data['scan_files']) && $data['scan_files']) {
                        $scan_files = json_decode(@$data['scan_files'], true);
                        foreach ($scan_files as $file) {
                            $patientFile = array();
                            $patientFile['patient_id'] = $insert_patient;
                            $patientFile['file_type'] = $file['file_type'];
                            $patientFile['scan_type'] = $file['scan_type'];
                            $patientFile['file'] = $file['file'];
                            $patientFile['created'] = date("Y-m-d H:i:s");
                            $this->ci->db->insert("patient_files", $patientFile);
                        }
                    }

                    if (isset($data['window_period']) && $data['window_period']) {
                        // Update Window Period
                        $this->ci->db->update(
                            "patient_brief_history",
                            array("window_period" => $this->cleanValue($data['window_period'])),
                            array("patient_id" => $insert_patient)
                        );
                    }

                    // Update the NIHSS of the patient
                    $nihssData = array();
                    $nihssData['patient_id'] = $insert_patient;
                    $nihssData['nihss_value'] = $data['nihss_admission'];
                    // $nihssData['nihss_options'] = $data['nihss_data'];
                    $nihssData['last_updated'] = date("Y-m-d H:i:s");

                    $this->ci->db->update("patient_nihss", $nihssData, array("AND" => array("patient_id" => $insert_patient, "nihss_time" => "admission")));

                    /* Extra code for sending push notifications*/

                    // Send Push notification to all users who in discussion.
                    $getPushIDs = $this->getOneSignalIdsOfTheUsers($insert_patient);

                    if (count($getPushIDs['pushIDs']) > 0) {
                        $pushData = array();
                        $pushData['title'] = "Code Stroke: New Patient";
                        $pushData['message'] = "There is a new patient received at " . $getCenterInfo['center_name'];
                        $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $insert_patient;
                        $pushData['devices'] = $getPushIDs['pushIDs'];
                        $this->codeStrokeSendPush($pushData);
                    }

                    if (count($getPushIDs['mobileNumbers']) > 0) {
                        // Send SMS
                        $phoneNumbers = array();
                        foreach ($getPushIDs['mobileNumbers'] as $phoneNumber) {
                            if ($getTheUserCenterId["phone_number"] !== $phoneNumber) {
                                $phoneNumbers[] = "+91" . $phoneNumber;
                            }
                        }
                        $smsData = array(
                            "to" => implode("<", $phoneNumbers),
                            "message" => "Code Stroke: New Patient! " . $getPatientNameCode['name'] . "(" . $getPatientNameCode['patient_code'] . ") There is a new patient received at " . $getCenterInfo['center_name'] . "snetchd://strokenetchandigarh.com/patient_detail/" . $insert_patient,
                        );
                        // $this->sendSMS($smsData);

                        // Make Calls to people
                        foreach ($phoneNumbers as $number) {
                            $callData = array(
                                "to" => $number,
                            );
                            $this->createCall($callData);
                        }
                    }

                    // Check if the center is
                    if ($getCenterInfo['is_hub'] == "yes") {
                        $codeStrokeTransitionId = 14; // Code Stroke Status Code from Hub
                    } else if ($getCenterInfo['is_center'] == "yes") {
                        $codeStrokeTransitionId = 24;
                    } else {
                        $codeStrokeTransitionId = 15; // Code Stroke Status Code from Spoke
                    }
                    $this->ci->db->insert("transition_statuses", array(
                        "patient_id" => $data['patient_id'],
                        "center_id" => $getCenterInfo['id'],
                        "status_id" => $codeStrokeTransitionId, // "Hub in status ID"
                        "user_id" => $header_userId[0],
                        "created" => date("Y-m-d H:i:s"),
                    ));

                    /* Extra code for sending push notifications*/
                    $patientDetails = $this->getPatientDetails($insert_patient, $header_userId[0]);
                    $patientDetails['getPushIDs'] = $getPushIDs;

                    $output = $this->printData("success", $patientDetails);
                    return $response->withJson($output, 200);
                }
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function alertHubAndStartTransition($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                // Add a Transition Status : Spoke Out
                $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                $this->ci->db->insert("transition_statuses", array(
                    "patient_id" => $data['patient_id'],
                    "center_id" => $getUserCenterId['center_id'],
                    "status_id" => 7, // "Hub in status ID"
                    "user_id" => $header_userId[0],
                    "created" => date("Y-m-d H:i:s"),
                ));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s"), "in_transition" => "1"), array("patient_id" => $data['patient_id']));

                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));

                // Send Push notifications to all the users from the Hub
                $getCenterInfo = $this->ci->db->get("centers", array("center_name", "main_hub"), array("id" => $getUserCenterId['center_id']));

                $hubInfo = $this->ci->db->get("centers", array("id", "center_name", "center_location"), array("id" => $getCenterInfo['main_hub']));

                $getAllUsersOneSignalFromHub = $this->ci->db->select("users", array("onesignal_userid", "phone_number"), array("center_id" => $hubInfo['id']));

                $getPushIDs = array();
                $getPushPhoneNumbers = array();
                foreach ($getAllUsersOneSignalFromHub as $user) {
                    $getPushIDs[] = $user["onesignal_userid"];
                    $getPushPhoneNumbers[] = $user["phone_number"];
                }

                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));

                // Create Push Data
                $pushData = array();
                $pushData['title'] = "Code Stroke: Patient Referred";
                $pushData['message'] = $getPatientNameCode['name'] . " is being referred from " . $getCenterInfo['center_name'];
                $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                $pushData['devices'] = $getPushIDs;
                $this->sendPush($pushData);

                $smsData = array(
                    "to" => implode("<", $getPushPhoneNumbers),
                    "message" => "Code Stroke: Patient Referred! " . $getPatientNameCode['name'] . " is being referred from " . $getCenterInfo['center_name'],
                );
                // $this->sendSMS($smsData);

                $output = $this->printData("error", array("message" => "The patient is in transition to " . $hubInfo['center_name'] . "(" . $hubInfo['center_location'] . ")"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function alertSpokeAndStartTransition($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {

                // $data['spokeId']

                // Add a Transition Status : Center Out
                $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));
                $this->ci->db->insert("transition_statuses", array(
                    "patient_id" => $data['patient_id'],
                    "center_id" => $getUserCenterId['center_id'],
                    "status_id" => 21,
                    "user_id" => $header_userId[0],
                    "created" => date("Y-m-d H:i:s"),
                ));

                $this->ci->db->update("user_patients", array("last_updated" => date("Y-m-d H:i:s"), "in_transition" => "1"), array("patient_id" => $data['patient_id']));

                $this->ci->db->update("patients", array("last_updated" => date("Y-m-d H:i:s")), array("id" => $data['patient_id']));

                // Send Push notifications to all the users from the Hub
                $getCenterInfo = $this->ci->db->get("centers", array("center_name"), array("id" => $getUserCenterId['center_id']));

                $getAllUsersOneSignalFromHub = $this->ci->db->select("users", array("onesignal_userid", "phone_number"), array("center_id" => $data['spokeId']));

                $getPushIDs = array();
                $getPushPhoneNumbers = array();
                foreach ($getAllUsersOneSignalFromHub as $user) {
                    $getPushIDs[] = $user["onesignal_userid"];
                    $getPushPhoneNumbers[] = $user["phone_number"];
                }

                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));

                // Create Push Data
                $pushData = array();
                $pushData['title'] = "Code Stroke: Patient Referred";
                $pushData['message'] = $getPatientNameCode['name'] . " is being referred from " . $getCenterInfo['center_name'];
                $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                $pushData['devices'] = $getPushIDs;
                $this->sendPush($pushData);

                $smsData = array(
                    "to" => implode("<", $getPushPhoneNumbers),
                    "message" => "Code Stroke: Patient Referred! " . $getPatientNameCode['name'] . " is being referred from " . $getCenterInfo['center_name'],
                );
                $this->sendSMS($smsData);

                $spokeInfo = $this->ci->db->get("centers", array("center_name", "center_location"), array("id" => $data['spokeId']));


                $output = $this->printData("error", array("message" => "The patient is in transition to " . $spokeInfo['center_name'] . "(" . $spokeInfo['center_location'] . ")"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function codeStrokeAlert($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {

                $getUserCenterId = $this->ci->db->get("users", array("center_id", "fullname", "user_role"), array("user_id" => $header_userId[0]));

                $getCenterInfo = $this->ci->db->get("centers", array("id", "center_name", "is_hub", "main_hub"), array("id" => $getUserCenterId['center_id']));

                $getHub = $this->ci->db->get("centers", array("id", "center_name", "short_name", "center_location", "is_hub"), array("id" => $getCenterInfo['main_hub']));

                $getPushIDs = array();
                $getPhoneNumbers = array();

                if ($getCenterInfo['is_hub'] == "yes") {
                    $locationType = "Hub";
                    $getAllUsersOneSignalFromHub = $this->ci->db->select("users", array("onesignal_userid", "phone_number"), array("center_id" => $getCenterInfo['id']));

                    foreach ($getAllUsersOneSignalFromHub as $user) {
                        $getPushIDs[] = $user["onesignal_userid"];
                        $getPhoneNumbers[] = "+91" . $user["phone_number"];
                    }
                } else {
                    $locationType = "Spoke";
                    $getAllUsersOneSignalFromHub = $this->ci->db->select("users", array("onesignal_userid", "phone_number"), array("center_id" => $getCenterInfo['main_hub']));
                    foreach ($getAllUsersOneSignalFromHub as $user) {
                        $getPushIDs[] = $user["onesignal_userid"];
                        $getPhoneNumbers[] = "+91" . $user["phone_number"];
                    }
                    $getAllUsersOneSignalFromSpoke = $this->ci->db->select("users", array("onesignal_userid", "phone_number"), array("center_id" => $getUserCenterId['center_id']));
                    foreach ($getAllUsersOneSignalFromSpoke as $user) {
                        $getPushIDs[] = $user["onesignal_userid"];
                        $getPhoneNumbers[] = "+91" . $user["phone_number"];
                    }
                }

                // Create Push Data & Send Push
                $pushData = array();
                $pushData['title'] = "Code Stroke";
                $pushData['message'] = "Acute Stroke in " . $getCenterInfo['center_name'] . "(" . $getUserCenterId['user_role'] . ")";
                $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                $pushData['devices'] = $getPushIDs;
                $this->codeStrokeSendPush($pushData);

                // Send SMS
                // $smsData = array(
                //     "to" => implode("<", $getPhoneNumbers),
                //     "message" => "Acute Stroke in " . $getCenterInfo['center_name'] . "(" . $getUserCenterId['user_role'] . ") snetchd://strokenetchandigarh.com/patient_detail/" . $data['patient_id'],
                // );
                // $this->sendSMS($smsData);

                // foreach ($getPhoneNumbers as $number) {
                //     $callData = array(
                //         "to" => $number,
                //     );
                //     $this->createCall($callData);
                // }

                $output = $this->printData("error", array("message" => "Code Stroke Sent!"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function stopClockManually($request, $response, $args)
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
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $getUserCenterId = $this->ci->db->get("users", array("center_id"), array("user_id" => $header_userId[0]));

                $getCenterInfo = $this->ci->db->get("centers", array("center_name", "is_hub"), array("id" => $getUserCenterId['center_id']));

                $clockStoppedStatusID = 0;
                if ($getCenterInfo['is_hub'] == "yes") {
                    $clockStoppedStatusID = 16;
                }
                if ($getCenterInfo['is_center'] == "yes") {
                    $clockStoppedStatusID = 25;
                } else {
                    $clockStoppedStatusID = 17;
                }
                $this->ci->db->insert("transition_statuses", array(
                    "patient_id" => $data['patient_id'],
                    "center_id" => $getUserCenterId['center_id'],
                    "status_id" => $clockStoppedStatusID,
                    "user_id" => $header_userId[0],
                    "created" => date("Y-m-d H:i:s"),
                ));
                $output = $this->printData("error", array("message" => "Clocks stopped have been stopped"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

    public function postCustomPushNotficationsAndSMSs($request, $response, $args)
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
            if (!isset($data['message_type']) || $data['message_type'] == "") {
                $errors[] = "message_type is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {

                // Insert the new statuses into the database
                $this->ci->db->insert("custom_status_updates", array(
                    "user_id" => $header_userId[0],
                    "patient_id" => $data['patient_id'],
                    "status_type" => $data['message_type'],
                    "created" => date("Y-m-d H:i:s"),
                ));

                // Get the patient name
                $getPatientNameCode = $this->ci->db->get("patients", array("name", "patient_code"), array("id" => $data['patient_id']));

                // Create Push Notfication and SMS Messages
                $pushTitle = "";
                $pushMessage = "";
                $smsMessage = "";
                switch ($data['message_type']) {
                    case "scans_completed":
                        $pushTitle = "Scans Completed";
                        $pushMessage = "Scans completed for the patient. (" . $getPatientNameCode['name'] . ")";
                        $smsMessage = "Scans completed for the patient. (" . $getPatientNameCode['name'] . ") snetchd://strokenetchandigarh.com/patient_detail/" . $data['patient_id'];
                        break;
                    case "scans_uploaded":
                        $pushTitle = "Scans Uploaded";
                        $pushMessage = "Scans Uploaded for the patient. (" . $getPatientNameCode['name'] . ")";
                        $smsMessage = "Scans uploaded for the patient. (" . $getPatientNameCode['name'] . ") snetchd://strokenetchandigarh.com/patient_detail/" . $data['patient_id'];
                        break;
                    default:
                        break;
                }
                if ($data['message_type'] == "scans_completed" || $data['message_type'] = "scans_uploaded") {
                    // Get the Users onesignal and phone Numbers based on the patient detail.
                    $getPushIDs = $this->getOneSignalIdsOfTheUsers($data['patient_id']);
                    if (count($getPushIDs['pushIDs']) > 0) {
                        // Create Push Data and Send Notficiation
                        $pushData = array();
                        $pushData['title'] = $pushTitle;
                        $pushData['message'] = "Patient (" . $getPatientNameCode['name'] . ") is being shifted to CT.";
                        $pushData['url'] = 'snetchd://strokenetchandigarh.com/patient_detail/' . $data['patient_id'];
                        $pushData['devices'] = $getPushIDs['pushIDs'];
                        $this->sendPush($pushData);

                        // SMSs shut down 
                        // Create SMS data and send SMS.
                        // $phoneNumbers = array();
                        // foreach ($getPushIDs['mobileNumbers'] as $phoneNumber) {
                        //     $phoneNumbers[] = "+91" . $phoneNumber;
                        // }
                        // $smsData = array(
                        //     "to" => implode("<", $phoneRNumbers),
                        //     "message" => $smsMessage,
                        // );
                        //Send SMS
                        // $this->sendSMS($smsData);
                    }
                }
                $output = $this->printData("success", array("message" => "SENT"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }


    // List all the spoke hospital under the main hub for this current center.
    public function getHubSpokeCenters($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $patientid = $args['patientId'];

            // get the current hosptial of the patient
            $getPatientCenter = $this->ci->db->get("patients", array("center_id", "id"), array("id" => $patientid));
            if (isset($getPatientCenter['id']) && $getPatientCenter['id']) {

                // get the main hub from the center
                $getHubIdFromCenter = $this->ci->db->get("centers", array("main_hub", "id"), array("id" => $getPatientCenter['center_id']));
                if (isset($getHubIdFromCenter['id']) && $getHubIdFromCenter['id']) {

                    if ($getHubIdFromCenter['main_hub'] !== null) {

                        // Get the list of all the spoke centers where hub is found
                        $getAllSpokesFromHub = $this->ci->db->select("centers", "*", array("AND" => array(
                            "main_hub" => $getHubIdFromCenter['main_hub'],
                            "is_spoke" => "yes"
                        )));

                        // Get Hub as wlel

                        $getAllSpokesFromHub[] = $this->ci->db->get("centers", "*", array("id" => $getHubIdFromCenter['main_hub']));

                        $output = $this->printData("success", $getAllSpokesFromHub);
                        return $response->withJson($output, 200);
                    } else {
                        $output = $this->printData("error", array("message" => "Center doesnt have a main hub."));
                        return $response->withJson($output, 403);
                    }
                } else {
                    $output = $this->printData("error", array("message" => "No such center found"));
                    return $response->withJson($output, 404);
                }
            } else {
                $output = $this->printData("error", array("message" => "No such patient found"));
                return $response->withJson($output, 404);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }
}