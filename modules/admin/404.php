<?php
$tpl =  new bQuickTpl();
include(getcwd()."/modules/admin/common.php");
echo $tpl->render("themes/admin/html/elements/header.php");
echo $tpl->render("themes/admin/html/404.php");
echo $tpl->render("themes/admin/html/elements/footer.php");