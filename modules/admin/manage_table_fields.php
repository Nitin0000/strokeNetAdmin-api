<?php
$tpl =  new bQuickTpl();
$tpl->page_title = "Manage Fields Section";
/* Fetch Tables from Database*/
$gettables = $database->query("SHOW TABLES FROM ".db_name)->fetchAll();
$manage_fields = array();
$database->query("CREATE TABLE IF NOT EXISTS `fields_admin`(`id` int(250) NOT NULL AUTO_INCREMENT,`Table_name` varchar(250) NOT NULL,`Table_Fields` text NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
$get_fields_admin = $database->select("fields_admin","*");
$newarray = array();
foreach($get_fields_admin as $fields){
	$newarray[$fields['Table_name']] = $fields['Table_Fields'];
}
$i = 0;
foreach($gettables as $tableslist){
	$dbfields = $database->getColumns($tableslist[0]);	
	$manage_fields[$tableslist['0']] = $dbfields;
	$i++;
}
$tpl->manage_fields = $manage_fields;
$tpl->db_fields = $newarray;
include(getcwd()."/modules/admin/common.php");
echo $tpl->render("themes/admin/html/managefields.php");
