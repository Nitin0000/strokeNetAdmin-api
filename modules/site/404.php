<?php
$tpl =  new bQuickTpl();
include(getcwd()."/modules/site/common.php");

// Send SEO Data
$tpl->page_title = ERROR;
$tpl->page_description =  site_seo_description;
$tpl->keywords =  site_seo_keywords;
$tpl->page_image       = main_url.website_logo;


header("Location: ".main_url);
// Send SEO Data
echo $tpl->render("themes/site/".theme_name."/html/404.php");
  