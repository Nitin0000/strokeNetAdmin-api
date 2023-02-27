<?php
function sendSMS($data)
{
    $src = '+13648889207';
    $dst = $data['to'];
    $text = $data['message'];
    $url = 'https://api.plivo.com/v1/Account/' . plivo_auth_id . '/Message/';
    $data = array("src" => $src, "dst" => $dst, "text" => $text);
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_USERPWD, plivo_auth_id . ":" . plivo_auth_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

if (isset($_SESSION['admin_user_id'])) {
    //Skipped Tables
    $skipped_tables = array("fields_admin", "fields_mapping", "module_alias", "settings", "table_icons", "notification_settings", "notifications", "user_interests", "faqs_categories", "events_all", "user_interests_all", "faqs", "reported_content", "feedback_videos", "followup_schedules", "patient_nihss", "patient_mrs", "patient_files", "patient_brief_history", "patient_basic_tests", "user_patients", "patient_advises", "patient_basic_details", "patient_presentation", "transition_statuses", "comments", "online_users", "transition_statuses_view", "conclusion_outcomes_view", "sms_verifications", "conversations", "blocked_users","conclusion_types","conclusion_outcomes","patient_complications","patient_contradictions","patient_ivt_medications","patient_last_viewed","patient_scan_times","patient_updates","custom_status_updates","comorbidities");
    $skipped_add_button = array("notification_settings", "user_interests", "user_interests_all", "events_all", "faqs", "reported_content", "feedback_videos", "followup_schedules", "caregivers", "patient_nihss", "patient_mrs", "patient_files", "patient_brief_history", "patient_basic_tests", "patients", "user_patients", "sms_verifications", "conversations", "blocked_users");

    //Fields Mappings
    $get_fields_mappings = $database->select("fields_mapping", "*");
    $hidden_fields = db_mapping_fields($get_fields_mappings, "hidden_fields");
    $required_fields = db_mapping_fields($get_fields_mappings, "required_fields");
    $ckeditor_fields = db_mapping_fields($get_fields_mappings, "ckeditor_fields");
    $date_fields = db_mapping_fields($get_fields_mappings, "date_fields");
    $slug_fields = db_slug_fields($get_fields_mappings);
    $get_another_data = db_get_another_data_fields($get_fields_mappings);
    $file_fields = db_file_fields($get_fields_mappings);

    //pr($get_another_data);
    /* Get URL parameters */
    $vars = explode("/", $_GET['action']);
    /* Fetch Tables from Database */
    $gettables = $database->query("SHOW TABLES FROM " . db_name)->fetchAll();

    $tables = array();
    foreach ($gettables as $tableslist) {
        $tables[] = $tableslist['0'];
    }
    //SETTINGS
    $get_settings = $database->select("settings", "*", array("ORDER" => "id ASC"));
    $new_array = array();
    foreach ($get_settings as $key => $value) {
        $new_array[$value['type']][] = $value;
    }
    //TABLE ICONS
    //$tbl_icon['table_name']='icon_class';
    $database->query("CREATE TABLE IF NOT EXISTS `table_icons`(`id` int(11) NOT NULL AUTO_INCREMENT,`table_name` varchar(100) NOT NULL,`icon_class` varchar(250) NOT NULL DEFAULT 'fa-table',`is_changable` enum('1','0') NOT NULL DEFAULT '1',PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
    $table_icons = $database->select('table_icons', '*');
    foreach ($table_icons as $k => $v) {
        $icon_tables[] = $v['table_name'];
    }
    if (is_array($table_icons) and !empty($table_icons)) {
        foreach ($tables as $k => $table_name) {
            if (!in_array($table_name, $skipped_tables)) {
                if (!in_array($table_name, $icon_tables)) {
                    $last_id = $database->insert('table_icons', array('table_name' => $table_name));
                }
            }
        }
        $table_icons = $database->select('table_icons', '*');
        foreach ($table_icons as $k => $v) {
            $tbl_icon[$v['table_name']] = $v['icon_class'];
        }
    } else {
        foreach ($tables as $k => $table_name) {
            if (!in_array($table_name, $skipped_tables)) {
                $last_id = $database->insert('table_icons', array('table_name' => $table_name));
                $tbl_icon[$table_name] = 'fa-table';
            }
        }
    }
    $icon_tables = array_keys($tbl_icon);
    $chk = 0;
    foreach ($icon_tables as $k => $table_name) {
        if (!in_array($table_name, $tables) or in_array($table_name, $skipped_tables)) {
            $database->delete('table_icons', array('table_name' => $table_name));
            $chk = 1;
        }
    }
    if ($chk == 1) {
        header("Location: " . _admin_url . "/index");
        exit();
    }
    ksort($tbl_icon);
    // Manage Fields Sections
    if (isset($vars[2]) && $vars[2] == "manage_fields") {
        if (isset($_POST['table_name']) && $_POST['table_name']) {
            $table_name = $_POST['table_name'];
        }
        if (isset($_POST['fields']) && $_POST['fields']) {
            $fields = $_POST['fields'];
        }
        //fields_admin table
        if ($database->count("fields_admin", array("Table_name" => $table_name)) == 0) {
            $lastid = $database->insert("fields_admin", array(
                "Table_name" => $table_name,
                "Table_Fields" => array($fields),
            ));
            if ($lastid) {
                echo 1;
            } else {
                echo 0;
            }
            header("Location: " . _admin_url . "/index");
        } else {
            $lastid = $database->update("fields_admin", array(
                "Table_Fields" => array($fields),
            ), array(
                "Table_name" => $table_name,
            ));
            if ($lastid) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }

//GET Table Columns
    if (isset($vars[2]) && $vars[2] == "actions") {

        if (isset($_POST['method']) && ($_POST['method'])) {
            $method = $_POST['method'];
        }
        if (isset($_POST['table']) && ($_POST['table'])) {
            $table = $_POST['table'];
        }
        if (isset($_POST['records']) && ($_POST['records'])) {
            $records = $_POST['records'];
        }

        if ($method == "deletearecord") {
            $records = explode(",", $records);
            $primaryid = $database->getPKID($table);
            foreach ($records as $record) {
                $deleterecord = $database->delete($table, array($primaryid => $record));
            }
            echo "1";
        } else if ($method == "deleteallrecords") {
            $query = "TRUNCATE TABLE " . $table;
            $deleteallrecords = $database->query($query);
            if ($deleteallrecords) {
                echo 1;
            } else {
                echo 0;
            }
        } else if ($method == "send_push") {
            $datapush = array();
            if (isset($_POST['postId']) && $_POST['postId'] !== "") {
                $promotion = $database->get("promotions", "*", array("id" => $_POST['postId']));

                $datapush = $promotion;
                if (isset($promotion['segment_user_ids']) && $promotion['segment_user_ids'] !== "") {
                    $datapush['segment_user_ids'] = unserialize($promotion['segment_user_ids']);
                }
                $devices = array();
                foreach ($datapush['segment_user_ids'] as $device) {
                    $devices[] = $device['onesignal_userid'];
                }
                $data_push = array(
                    "action_type" => $datapush['object_type'],
                    "action_id" => $datapush['object_id'],
                    "devices" => $devices,
                    "title" => $datapush['title'],
                    "message" => $datapush['description']);
                $sendpush = sendPush("user", $data_push);
                $data = json_decode($sendpush);
                //pr($data->id);
                //exit;
                //                if($sendpush){
                //                    $database->update("promotions", array("push_id" => $data->id), array("id" => $_POST['postId']));
                //                    $database->update("promotions", array("status" => "Completed"), array("id" => $_POST['postId']));
                //                    echo "1";
                //                }
            }

        } else if ($method == "publish") {
            $records = explode(",", $records);
            $primaryid = $database->getPKID($table);
            foreach ($records as $record) {
                if ($table == "users") {
                    // Here we will send an SMS or Push Notification to the user about the account verification
                    $getUserData = $database->get($table, array("first_name", "phone_number", "onesignal_userid"), array($primaryid => $record));
                    $smsData = array(
                        "to" => "+91" . $getUserData['phone_number'],
                        "message" => "Hello " . $getUserData['first_name'] . ", Your account has been approved on StrokeNetChandigarh App. You can login and use the app now.",
                    );
                    $sendSMS = sendSMS($smsData); // Send SMS to the user
                    // pr($sendSMS);
                    // $data_push = array(
                    //     "action_type" => "",
                    //     "action_id" => "",
                    //     "devices" => array($getUserData['onesignal_userid']),
                    //     "title" => "Account Approved",
                    //     "message" => "Hello ".$getUserData['first_name'].", Your account has been approved on StrokeNetChandigarh App. You can login and use the app now."
                    // );
                    // $sendpush = sendPush("user", $data_push);

                }
                $publish = $database->update($table, array("status" => 1), array($primaryid => $record));
            }
            echo 1;
        } elseif ($method == "unpublish") {
            $records = explode(",", $records);
            $primaryid = $database->getPKID($table);
            foreach ($records as $record) {
                $unpublish = $database->update($table, array("status" => 0), array($primaryid => $record));
            }
            echo 1;
        }
    }
    // Date Range Types
    $daterangertype = array(
        "today",
        "yesterday",
        "this_week",
        "this_month",
        "last_month",
        "this_year",
        "last_year",
        "all_time",
        "date_range",
    );
    $tpl->daterangetypes = $daterangertype;

    //passing all variables to Template Class
    $tpl->vars = $vars;
    $tpl->skipped_add_button = $skipped_add_button;
    $tpl->skipped_tables = $skipped_tables;
    $tpl->table_icons = $tbl_icon;
    $tpl->setting_array = $new_array;
    $tpl->hidden_fields = $hidden_fields;
    $tpl->required_fields = $required_fields;
    $tpl->ckeditor_fields = $ckeditor_fields;
    $tpl->date_fields = $date_fields;
    $tpl->slug_fields = $slug_fields;
    $tpl->get_another_data = $get_another_data;
    $tpl->tables = $tables;
    $tpl->file_fields = $file_fields;

    $tpl->database = $database;

    foreach ($tables as $k => $table_name) {
        $columns = $database->getColumns($table_name);
        foreach ($columns as $k => $v) {
            $table_columns[$table_name][] = $v[0];
        }
    }

    $tpl->table_columns = $table_columns;
} else {
    header("Location: " . _admin_url . "/login");
    exit();
}
