<?php
$tpl = new bQuickTpl();
include(getcwd() . "/modules/admin/common.php");
include(getcwd() . "/core/nocsrf.php");
if (isset($vars[2]) && $vars[2]) {
    $table_name = $vars[2];
} else {
    header("Location: " . _admin_url . "/404");
    exit();
}
if(in_array($vars[2], $skipped_add_button)){
    header("Location: " . _admin_url . "/table/".$vars[2]);
    exit();
}
$custom_data = array();
foreach ($get_another_data as $stuff) {
    if ($stuff['main_table'] == $table_name) {
        $query_complete = $database->select($stuff['secondary_table'], array($stuff['value'], $stuff['secondary_field']));
        $custom_data[$stuff['main_field']]['data'] = $query_complete;
        $custom_data[$stuff['main_field']]['attributes']['seconday_field'] = $stuff['secondary_field'];
        $custom_data[$stuff['main_field']]['attributes']['value'] = $stuff['value'];
    }
}


$allowed_exts = array();
if (isset($file_fields) and ! empty($file_fields[$table_name])) {
    $file_fields_tbl = $file_fields[$table_name];
    foreach ($file_fields_tbl as $k => $file_field_info) {
        $allowed_exts[$file_field_info['field']] = unserialize($file_field_info['allowed_exts']);
    }
}
if (isset($_POST) && $_POST) {
    
//	try
//    {
//        NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
//        $result = 'CSRF check passed. Form parsed.';
    $err_trgr = false;
   
    if (isset($_FILES['data']['name']) and ( $_FILES['data']['name'])) {
        $post_key_arr = array_keys($_FILES['data']['name']);
        foreach ($post_key_arr as $k => $post_key) {
            foreach ($_FILES['data'] as $files_key => $files_value) {
                $upload_files_array[$post_key][$files_key] = $files_value[$post_key];
            }
        }
        if (isset($upload_files_array) and ! empty($upload_files_array)) {
            foreach ($upload_files_array as $post_key => $upload_array) {
                if ($upload_array['error'] != 4) {
                    $f_detail = file_name_details($upload_array['name']);
                    if (in_array($f_detail['ext'], $allowed_exts[$post_key])) {
                        $location = get_upload_file_location($post_key, $file_fields[$table_name]);
                        $image_path = upload_img("", $upload_array, $location);
                        $_POST['data'][$post_key] = $image_path['urls'][0];
                    } else {
                        $er_msg = 'Selected File for <b class="text-danger">' . $post_key . '</b> Field is wrong.<br><div class="well well-sm"><b><i>Allowed File types are</i></b>: <ol>';
                        foreach ($allowed_exts[$post_key] as $allowed_exts_key => $allowed_exts_val) {
                            $er_msg.='<li>' . $allowed_exts_val . '</li>';
                        }
                        $er_msg.="</ol></div>";
                        $error_message[] = $er_msg;
                        $err_trgr = true;
                    }
                }
            }
        }
    }
    foreach ($_POST['data'] as $post_key => $post_val) {
        if (isset($required_fields[$table_name])) {
            if (in_array($post_key, $required_fields[$table_name]) and ! $post_val) {
                $error_message[] = "Field Name <span class=\"text-danger\">'" . ucfirst($post_key) . "'</span> can't be empty";
                $err_trgr = true;
            }
        }
    }
    $rel_main_fields_array = array();
    foreach ($get_another_data as $db_id => $rel_info) {
        if ($rel_info['is_multiple'] == 1)
            $rel_main_fields_array[] = $rel_info['main_field'];
    }
    foreach ($_POST['data'] as $post_key => $post_val) {
        if (in_array($post_key, $rel_main_fields_array)) {
            $_POST['data'][$post_key] = serialize($_POST['data'][$post_key]);
        }
    }
    $_POST = remove_empty_locations($_POST);
    if ($err_trgr == false) {
        
        if($table_name == "admin_users" || $table_name == "users"){
            if(isset($_POST['data']['password']) && $_POST['data']['password']){
                $_POST['data']['password'] =  md5($_POST['data']['password']);
            }
        }    
        
        
        $last_user_id = $database->insert($table_name, $_POST['data']);
        
        if ($last_user_id) {            
            foreach ($get_another_data as $relation) {
                if ($vars[2] == $relation['main_table'] && $relation['is_multiple'] == 1) {
                    $ids = unserialize($_POST['data'][$relation['main_field']]);
                    foreach ($ids as $multiple_id) {
                        $database->insert($relation['main_table'] . "_" . $relation['secondary_table'], array($relation['main_table'] . '_id' => $last_user_id, $relation['secondary_table'] . '_id' => $multiple_id));
                    }
                }
            }
            header("Location: " . _admin_url . "/edit/" . $table_name . "/rec:" . $last_user_id . "/saved");
            exit;
        }
    }
//	}
//	catch ( Exception $e )
//	{
//		// CSRF attack detected
//		$result = $e->getMessage() . ' Form ignored.';
//		header("Location: ".main_url."/admin/add/".$table_name."/error/".$result);
//	}
} else {
    $result = 'No post data yet.';
}
if (isset($error_message)) {
    $tpl->error_message = $error_message;
}
$getcolumns = $database->getColumns($table_name);
$token = NoCSRF::generate('csrf_token');
$tpl->formtoken = $token;
$tpl->result = $result;
$tpl->page_title = "Add a Record : " . format_names($table_name);
$tpl->table = $table_name;
$tpl->tb_primaryid = $database->getPKID($table_name);
$tpl->columns = $getcolumns;
$tpl->custom_data = $custom_data;
echo $tpl->render("themes/admin/html/add.php");
