<?php

if (isset($_GET['action'])) {
    $tpl = new bQuickTpl();
    $vars = explode("/", $_GET['action']);
    $tpl->params = $vars;
}
// Include Site Functions
include(getcwd() . "/includes/site_functions.php");
// Include Site Actions
require_once(getcwd() . "/includes/site-actions/site-actions.php");
//require_once(getcwd() . "/core/Facebook/facebook.php");

//aliases
$tpl->aliases = $aliases;
//aliases_flip
$aliases_flip = array_flip($aliases);
$tpl->aliases_flip = $aliases_flip;

$pages = $database->select("pages",array("id","page_title"));
$tpl->pages = $pages;

?>
