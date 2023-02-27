<?php
include("includes/core_functions.php");
include("config/config.php");

require_once("core/template.php");
date_default_timezone_set(timezone);

include(getcwd() . '/core/phpmailer/class.phpmailer.php');
$mail = new PHPMailer();

//alias list
$aliases = array();
$get_aliases = $database->select('module_alias', '*');
if (!empty($get_aliases)) {
    foreach ($get_aliases as $alias)
        $aliases[$alias['alias_name']] = $alias['module_name'];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $params = explode("/", $_GET['action']);

    if (isset($params[0])) {

        if ($params[0] == "admin") {
            if (!isset($params[1])) {
                $params[1] = 'login';
            } else if ($params[1] == "") {
                $params[1] = 'login';
            }

            $filename  = "modules/admin/" . $params[1] . ".php";
            if (!file_exists($filename)) {
                $filename = "modules/admin/404.php";
            }
            include($filename);
        } else {
            $filename = "modules/site/" . $params[0] . ".php";
            if (!file_exists($filename)) {
                if (array_key_exists($params[0], $aliases)) {
                    $filename = "modules/site/" . $aliases[$params[0]] . ".php";
                    if (!file_exists($filename))
                        $filename = "modules/site/404.php";
                } else {
                    $filename = "modules/site/404.php";
                }
            }
            include($filename);
        }
    } else {
        include("modules/site/index.php");
    }
} else {
    include("modules/site/index.php");
}