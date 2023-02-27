<?php
ini_set("display_errors", true);
error_reporting(E_ALL);

$tpl = new bQuickTpl();
include(getcwd() . "/modules/admin/common.php");

if (isset($vars[1]) && $vars[1]) {
    // $table_name = $vars[2];           
} else {
    header("Location: " . _admin_url . "/404");
    exit();
}
if (isset($vars[2]) && $vars[2]) {
    $record_id = $vars[2];
} else {
    header("Location: " . _admin_url . "/404");
    exit();
}
$patientID = str_replace("rec:", "", $record_id);
$patient = $database->get("patients", "*", array("id" => $patientID));
// $patient['patient_advises'] = array();

$patient['patient_basic_tests'] = $database->get("patient_basic_tests", array("rbs", "inr", "platelets_count"), array("patient_id" => $patientID));

$patient['patient_brief_history'] = $database->get("patient_brief_history", array("weakness_side", "facial_deviation", "power_of_limbs", "loc", "window_period"), array("patient_id" => $patientID));

$patient_files = $database->select("patient_files", "*", array("patient_id" => $patientID));

$patient['patient_files'] = array(
    "ncct" => array(),
    "cta_ctp" => array(),
    "mra_mri" => array(),
    "consent_form" => array()
);

foreach (@$patient_files as $key => $val) {
    $patient_files[$key]['file_thumb'] = uploads_url . '/thumb/compress.php?src=' . $val['file'] . '&w=300&h=300&zc=1';
    $patient_files[$key]['file'] = uploads_url . $val['file'];
    $patient['patient_files'][$val['scan_type']][] = $patient_files[$key];
}

$patient_nihss = $database->select("patient_nihss", array("nihss_time", "nihss_value", "nihss_options"), array("patient_id" => $patientID));
foreach ($patient_nihss as $key => $val) {
    $patient_nihss[$key]['nihss_options'] = json_decode($val['nihss_options']);
    $patient['patient_nihss'][$val['nihss_time']] = $patient_nihss[$key];
}

$patient_mrs = $database->select("patient_mrs", array("mrs_time", "mrs_options", "mrs_points"), array("patient_id" => $patientID));
foreach ($patient_mrs as $key => $val) {
    $patient['patient_mrs'][$val['mrs_time']] = $patient_mrs[$key];
}

// $patient_advises = $database->select("patient_advises", array("consultant_id", "message", "id"), array("patient_id" => $patientID));
// foreach ($patient_advises as $key => $val) {
//     $consultant_name = $database->get("users", array("fullname"), array("user_id" => $val['consultant_id']));
//     $patient_advises[$key]['consultant_name'] = $consultant_name['fullname'];
// }
// $patient['patient_advises'] = $patient_advises;

$tpl->patientDetails = $patient;


$tpl->page_title = "Patient Details";

echo $tpl->render("themes/admin/html/patient_details.php");
