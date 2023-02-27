<?php
$tpl = new bQuickTpl();
include(getcwd() . "/modules/admin/common.php");
//required ajax start
if (isset($vars[2]) and $vars[2] == "get_required") {
    $required = array();
    if (isset($required_fields[$_POST['table_name']])) {
        $required = $required_fields[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $required)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option selected value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_required") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "required_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_required_field") {
    $table = $_POST['table'];
    $column = $_POST['column'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "required_fields", "main_field" => $column)));
}
//required ajax stop
//ckeditor ajax start
if (isset($vars[2]) and $vars[2] == "get_ckeditor") {
    $ckeditor = array();
    if (isset($ckeditor_fields[$_POST['table_name']])) {
        $ckeditor = $ckeditor_fields[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $ckeditor)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option selected value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_ckeditor") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "ckeditor_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_ckeditor_field") {
    $table = $_POST['table'];
    $column = $_POST['column'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "ckeditor_fields", "main_field" => $column)));
}
//ckeditor ajax stop
//hidden ajax start
if (isset($vars[2]) and $vars[2] == "get_hidden") {
    $hidden = array();
    if (isset($hidden_fields[$_POST['table_name']])) {
        $hidden = $hidden_fields[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $hidden)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option selected value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_hidden") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "hidden_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_hidden_field") {
    $table = $_POST['table'];
    $column = $_POST['column'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "hidden_fields", "main_field" => $column)));
}
//hidden ajax stop
//image ajax start
if (isset($vars[2]) and $vars[2] == "get_image_fields") {
    $image = array();
    if (isset($image_fields[$_POST['table_name']])) {
        $image = $image_fields[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $image)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_image_fields") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "image_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_image_field") {
    $table = $_POST['table'];
    $column = $_POST['column'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "image_fields", "main_field" => $column)));
}
//image ajax stop
//file ajax start
if (isset($vars[2]) and $vars[2] == "get_file_fields") {
    $file = array();
    if (isset($file_fields[$_POST['table_name']])) {
        $files = $file_fields[$_POST['table_name']];
        foreach ($files as $db_key => $file_data) {
            $file[$db_key] = $file_data['field'];
        }
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $file)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == 'get_directory_list') {
    $root = $_POST['root'];
    $dirs = get_directory_list($root);
    echo json_encode($dirs);
}
if (isset($vars[2]) and $vars[2] == 'file_create_folder') {
    $root = $_POST['root'];
    $folder = $_POST['folder_name'];
    $folder_path = $root . "/" . $folder;
    if (!is_dir($folder_path) and $folder != "") {
        @mkdir($folder_path);
    }
}
if (isset($vars[2]) and $vars[2] == 'file_del_folder') {
    $root = $_POST['root'];
    $folder = $_POST['folder_name'];
    $folder_path = $root . "/" . $folder;
    if (is_dir($folder_path) and $folder != "") {
        rrmdir($folder_path);
    }
}
if (isset($vars[2]) and $vars[2] == "del_file_fields") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "file_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_file_field") {
    $db_key = $_POST['db_key'];
    $database->delete("fields_mapping", array("id" => $db_key));
}
//file ajax stop
//multiple ajax start
if (isset($vars[2]) and $vars[2] == "get_multiple") {
    $multiple = array();
    if (isset($multiple_selects[$_POST['table_name']])) {
        $multiple = $multiple_selects[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    foreach ($columns as $k => $field) {
        if (!in_array($field['Field'], $multiple)) {
            $primaryid = $database->getPKID($_POST['table_name']);
            if ($field['Field'] != $primaryid) {
                echo '<option>' . $field['Field'] . '</option>';
            }
        } else {
            echo '<option selected value="" style="background-color:darkred;color:white" title="Already Selected" disabled>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_multiple") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "date_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_multiple_field") {
    $table = $_POST['table'];
    $column = $_POST['column'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "date_fields", "main_field" => $column)));
}
//multiple ajax stop
//slug ajax start
if (isset($vars[2]) and $vars[2] == "get_primary_slug") {
    $columns = $database->getColumns($_POST['table_name']);
    echo '<option value="" hidden>Select Main Field for ' . $_POST['table_name'] . '...</option>';
    foreach ($columns as $k => $field) {
        $primaryid = $database->getPKID($_POST['table_name']);
        if ($field['Field'] != $primaryid) {
            echo '<option>' . $field['Field'] . '</option>';
        }
    }
}
if (isset($vars[2]) and $vars[2] == "get_secondary_slug") {
    $selected_slug_field = $_POST['slug_field'];
    $slug = array();
    if (isset($slug_fields[$_POST['table_name']])) {
        $slug = $slug_fields[$_POST['table_name']];
    }
    $columns = $database->getColumns($_POST['table_name']);
    $primaryid = $database->getPKID($_POST['table_name']);
    echo '<option value="" hidden>Select Secondary Field of ' . $_POST['table_name'] . '...</option>';
    foreach ($columns as $k => $field) {
        if ($field['Field'] != $primaryid and $field['Field'] != $selected_slug_field) {
            $show = 1;
            foreach ($slug as $k => $v) {
                if ($selected_slug_field == $v['main'] and $field['Field'] == $v['secondary']) {
                    $show = 0;
                }
            }
            if ($show == 1) {
                echo '<option>' . $field['Field'] . '</option>';
            } else {
                echo '<option disabled style="color:red">Selected: ' . $field['Field'] . '</option>';
            }
        }
    }
}
if (isset($vars[2]) and $vars[2] == "del_slug") {
    $table = $_POST['table'];
    $database->delete("fields_mapping", array("AND" => array("main_table" => $table, "type" => "slug_fields")));
}
if (isset($vars[2]) and $vars[2] == "del_slug_field") {
    $del_id = $_POST['del_id'];
    $database->delete("fields_mapping", array("id" => $del_id));
}
//Slug ajax stop
//Relationship ajax Start
if (isset($vars[2]) and $vars[2] == "get_rel_main_fields") {
    $rel_main_table = $_POST['rel_main_table'];
    $columns = $database->getColumns($rel_main_table);
    echo '<option value="" hidden>Select Main Field for ' . $rel_main_table . '...</option>';
    $get_data = array();
    foreach ($get_another_data as $db_key => $data) {
        $get_data[$data['main_table']][$data['main_field']] = array('secondary_table' => $data['secondary_table'], 'secondary_field' => $data['secondary_field'], 'value' => $data['value']);
    }
    $rel_main_fields = array();
    if (isset($get_data[$rel_main_table])) {
        $rel_main_fields = array_keys($get_data[$rel_main_table]);
    }
    foreach ($columns as $k => $field) {
        $primaryid = $database->getPKID($rel_main_table);
        if ($field['Field'] != $primaryid) {
            if (!in_array($field['Field'], $rel_main_fields)) {
                echo '<option>' . $field['Field'] . '</option>';
            } else {
                echo '<option disabled style="color:red">Relationship Exists: ' . $field['Field'] . '</option>';
            }
        }
    }
}
if (isset($vars[2]) and $vars[2] == "get_rel_secondary_tables") {
    $rel_main_table = $_POST['rel_main_table'];
    echo '<option value="" hidden>Select Secondary Table</option>';
    foreach ($tables as $k => $table_name) {
        //if (!in_array($table_name, $skipped_tables)) { //and $table_name!=$rel_main_table)
            echo '<option>' . $table_name . '</option>';
        //}
    }
}
if (isset($vars[2]) and $vars[2] == "get_rel_secondary_fields") {
    $rel_secondary_table = $_POST['rel_secondary_table'];
    $columns = $database->getColumns($rel_secondary_table);
    echo '<option value="" hidden>Select Field...</option>';
    foreach ($columns as $k => $field) {
        echo '<option>' . $field['Field'] . '</option>';
    }
}
if (isset($vars[2]) and $vars[2] == "del_all_rel") {
    $del_main_table = $_POST['main_table'];
    $database->delete('fields_mapping', array("AND" => array('main_table' => $del_main_table, 'type' => 'get_another_data')));
}
if (isset($vars[2]) and $vars[2] == "del_col_rel") {
    $rel_del_id = $_POST['rel_del_id'];
    $database->delete('fields_mapping', array('id' => $rel_del_id));
}
//Relationship ajax Stop
//BackUp and Restore ajax Starts
if (isset($_POST['action']) and $_POST['action'] == "make_backup") {
    $backup = backup_db($database);
    if ($backup == true) {
        echo 1;
    } else {
        echo 0;
    }
}
if (isset($_POST['action']) and $_POST['action'] == "restore_db") {
    $res_path = $_POST['res_path'];
    $backup = restore_db($database, $res_path);
    if ($backup == true) {
        echo 1;
    } else {
        echo 0;
    }
}
if (isset($_POST['action']) and $_POST['action'] == "get_restore_dates") {
    $no_backups = true;
    $backup_points = get_backup_dates();
    if (count($backup_points) > 0) {
        $no_backups = false;
    }
    if ($no_backups == true) {
        echo '<div class="text-danger" style="font-size:16px">
		Sorry!! Currently No Backup Dates Are Available<br />Click on Backup Now!! to Create a Database Backup
		</div>';
    } else {
        $count = 0;
        echo '<div class="row">
		  		<div class="col-lg-12">
				<table class="table table-condensed table-hover">
		  		<thead>
				<tr class="success">
				<th>S.No</th>
				<th>Restore Point Date</th>
				</tr>
				</thead>
				<tbody id="tbody_restore_pts">';
        foreach ($backup_points as $backup_pt) {
            $count++;
            echo '<tr>
			  		<td>' . $count . '</td>
					<td><a href="javascript:void(0);" style="text-decoration:none;" id="view_restore_points"><input type="hidden" value="' . $backup_pt . '" /><div class="col-lg-10" title="Click to View Restoration Points for this date">' . $backup_pt . '</div></a><span class="col-lg-2"><input type="hidden" value="' . $backup_pt . '" /><button id="del_backup_date" class="btn btn-danger btn-xs">Delete!!</button></span></td>
					</tr>';
        }
        echo '</tbody>
		  		</table>
				</div>
				</div>';
    }
}
if (isset($_POST['action']) and $_POST['action'] == "get_restore_points") {
    $got_date = $_POST['got_date'];
    $no_restores = true;
    $restore_points = get_restore_pts($got_date);
    if (count($restore_points) > 0) {
        $no_restores = false;
    }
    if ($no_restores == true) {
        echo '<div class="text-danger" style="font-size:16px">
		Sorry!! Currently No Restore Points Are Available for ' . $got_date . '<br />Click on Backup Now!! to Create a Database Backup
		</div>';
    } else {
        $count = 0;
        echo '<div class="row">
		  		<div class="col-lg-12">
				<table class="table table-condensed table-hover">
		  		<thead>
				<tr class="success">
				<th>S.No</th>
				<th>Restore Point Date</th>
				<th width="40%">Action</th>
				</tr>
				</thead>
				<tbody id="tbody_restore_pts">';
        foreach ($restore_points as $restore_pt) {
            $count++;
            echo '<tr>
			  		<td>' . $count . '</td>
					<td><input type="hidden" value="' . $restore_pt . '">' . $restore_pt . '</td>
					<td><input type="hidden" value="' . $got_date . '\\' . $restore_pt . '" /><button class="btn btn-success btn-xs" id="btn_restore">Restore!!</button>&nbsp;&nbsp;<button class="btn btn-xs btn-danger" id="del_restore_point">Delete Backup</button><input type="hidden" value="' . $got_date . '" /></td>
					</tr>';
        }
        echo '</tbody>
		  		</table>
				</div>
				</div>';
    }
}
if (isset($_POST['action']) and $_POST['action'] == "del_restore_point") {
    $del_path = $_POST['del_path'];
    $del_path = str_replace('\\', '/', $del_path);
    $full_del_path = 'config/backup_restore/' . $del_path;
    rrmdir($full_del_path);
}
if (isset($_POST['action']) and $_POST['action'] == "update_alias") {
    $ins = $database->update('module_alias', array('alias_name' => $_POST['alias_val']), array('id' => $_POST['db_id']));
    if ($ins)
        echo $_POST['alias_val'];
}