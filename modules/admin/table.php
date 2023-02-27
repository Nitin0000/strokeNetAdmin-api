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
if (isset($vars[3]) && $vars[3]) {
    $page_no_var = $vars[3];
}
if (isset($vars[4]) && $vars[4]) {
    $perpage_param = $vars[4];
}
if (isset($vars[5]) && $vars[5]) {
    $sort_by_param = $vars[5];
} else {
    $sort_by_param = "sortby:" . $database->getPKID($table_name) . ":DESC";
}
// Per Page Content
if (isset($perpage_param) && $perpage_param) {
    $_SESSION['perpage'] = str_replace("perpage:", "", $perpage_param);
}
if (isset($_SESSION['perpage']) && $_SESSION['perpage']) {
    $perpage = $_SESSION['perpage'];
} else {
    $perpage = 10;
}
//Sorting Table Content
if (isset($sort_by_param) && $sort_by_param) {
    $_SESSION['sortby'] = explode(":", $sort_by_param);
}
if (isset($_SESSION['sortby']) && $_SESSION['sortby']) {
    $sortby_field = $_SESSION['sortby'][1];
    $sortby_method = $_SESSION['sortby'][2];
} else {
    $_SESSION['sortby'][0] = $database->getPKID($table_name);
    $_SESSION['sortby'][0] = "DESC";
    $sortby_field = $database->getPKID($table_name);
    $sortby_method = "DESC";
}
$next_number = "0";
if (isset($page_no_var) && $page_no_var) {
    $page_no = str_replace("p:", "", $page_no_var);
    if ($page_no == "" || $page_no == "p" || $page_no == "p:") {
        $page_no = 1;
        if ($page_no == 1 || $page_no == 0) {
            $next_number = 0;
        } else {
            $next_number = $page_no * $perpage - $perpage;
        }
    } else {
        $page_no = $page_no;
        if ($page_no == 1 || $page_no == 0) {
            $next_number = 0;
        } else {
            $next_number = $page_no * $perpage - $perpage;
        }
    }
} else {
    $page_no = 1;
    if ($page_no == 1 || $page_no == 0) {
        $next_number = 0;
    } else {
        $next_number = $page_no * $perpage - $perpage;
    }
}
//Stuff for Pagination Ends
//Get Columns from Fields_Admin Table
$column_names = array();
$get_table_headers = $database->select('fields_admin', "Table_Fields", array("Table_name" => $table_name));
if (empty($get_table_headers)) {
    $getcolumns = $database->getColumns($table_name);
    foreach ($getcolumns as $column) {
        $column_names[] = $column['Field'];
    }
} else {
    $column_names = unserialize($get_table_headers[0]);
    $column_names = $column_names[0];
    if (empty($column_names)) {
        $getcolumns = $database->getColumns($table_name);
        foreach ($getcolumns as $column) {
            $column_names[] = $column['Field'];
        }
    }
}
$custom_data = array();
foreach ($get_another_data as $stuff) {
    if ($stuff['main_table'] == $table_name) {
        $query_complete = $database->select($stuff['secondary_table'], "*");
        $custom_data[$stuff['main_field']]['data'] = $query_complete;
        $custom_data[$stuff['main_field']]['attributes']['seconday_field'] = $stuff['secondary_field'];
        $custom_data[$stuff['main_field']]['attributes']['value'] = $stuff['value'];
        $custom_data[$stuff['main_field']]['attributes']['secondary_table'] = $stuff['secondary_table'];
    }
}





//Get Records
if ($perpage == "all") {
        $get_records = $database->select($table_name, $column_names,array("ORDER" => $sortby_field." ".$sortby_method ));
} else {
      $get_records = $database->select($table_name, $column_names, array("LIMIT" => array($next_number,$perpage),"ORDER" => $sortby_field." ".$sortby_method));
}

$count_records = $database->count($table_name);




if ($perpage == "all") {
    $total_pages = 1; // Total Pages
} else {
    $total_pages = ceil($count_records / $perpage); // Total Pages
}
$token = NoCSRF::generate('csrf_token');
$tpl->formtoken = $token;
$tpl->currentpage = $page_no;
$tpl->current_records = count($get_records);
$tpl->total_pages = $total_pages;
$tpl->total_records = $count_records;
$tpl->table = $table_name;
$tpl->records = $get_records;
$tpl->perpage = $perpage;
$tpl->table_headers = $column_names;
$tpl->current_order = $sortby_method;
$tpl->custom_data = $custom_data;
$tpl->tb_primaryid = $database->getPKID($table_name);
$tpl->page_title = format_names($table_name);
echo $tpl->render("themes/admin/html/table.php");
