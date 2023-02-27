<?php
include(getcwd() . "/core/nocsrf.php");
$tpl =  new bQuickTpl();
include(getcwd()."/modules/site/common.php");

$id = $vars[1];
$page = $database->get("pages","*",array("id" => $id));

if(isset($page['id']) && $page['id'] !== ""){
    $tpl->page = $page;
    
    // Send SEO Data
    $tpl->page_title = $page['page_title'];
    $tpl->page_description =  $page['seo_title'];
    $tpl->keywords =  $page['seo_description'];
    $tpl->page_image = main_url.website_logo;
    // Send SEO Data
}else{
    header("Location: " . main_url."/404");
    exit();
}



echo $tpl->render("themes/site/".theme_name."/html/page.php");
