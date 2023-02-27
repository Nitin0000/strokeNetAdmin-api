<?php
$tpl = new bQuickTpl();
$tpl->page_title = "Admin Panel";
include getcwd() . "/modules/admin/common.php";

header("Location: " . _admin_url . "/table/users");

echo $tpl->render("themes/admin/html/index.php");
