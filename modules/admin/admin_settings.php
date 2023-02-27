<?php
$tpl =  new bQuickTpl();
$tpl->page_title = "Admin Area Settings";
include(getcwd()."/modules/admin/common.php");
if(isset($_POST['required']) and $_POST['required'])
{
//	pr($_POST['required']);
	if(isset($_POST['required']['fields']) and !empty($_POST['required']['fields']))
	{
		foreach($_POST['required']['fields'] as $k => $v)
		{
			$database->insert("fields_mapping",array("main_table"=>$_POST['required']['table'],"main_field"=>$v,"type"=>"required_fields"));
		}
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['ckeditor']) and $_POST['ckeditor'])
{
//	pr($_POST['ckeditor']);
	if(isset($_POST['ckeditor']['fields']) and !empty($_POST['ckeditor']['fields']))
	{
		foreach($_POST['ckeditor']['fields'] as $k => $v)
		{
			$database->insert("fields_mapping",array("main_table"=>$_POST['ckeditor']['table'],"main_field"=>$v,"type"=>"ckeditor_fields"));
		}
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['hidden']) and $_POST['hidden'])
{
//	pr($_POST['hidden']);
	if(isset($_POST['hidden']['fields']) and !empty($_POST['hidden']['fields']))
	{
		foreach($_POST['hidden']['fields'] as $k => $v)
		{
			$database->insert("fields_mapping",array("main_table"=>$_POST['hidden']['table'],"main_field"=>$v,"type"=>"hidden_fields"));
		}
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['date']) and $_POST['date'])
{
	if(isset($_POST['date']['fields']) and !empty($_POST['date']['fields']))
	{
		foreach($_POST['date']['fields'] as $k => $v)
		{
			$database->insert("fields_mapping",array("main_table"=>$_POST['date']['table'],"main_field"=>$v,"type"=>"date_fields"));
		}
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['file'])  and ($_POST['file']))
{
//	pr($_POST,'$_POST');return;
	extract($_POST['file']);
	if(isset($table) and isset($field) and isset($type) and isset($path) and isset($allowed_exts) and ($table) and ($field) and ($type) and ($path) and !empty($allowed_exts))
	{
		$allowed_exts=serialize($allowed_exts);
		$database->insert("fields_mapping",array("main_table"=>$table,"main_field"=>$field,"type"=>"file_fields",'upload_type'=>$type,'file_upload_path'=>$path,"allowed_exts"=>$allowed_exts));
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['image']) and $_POST['image'])
{
	if(isset($_POST['image']['fields']) and isset($_POST['image']['table']) and !empty($_POST['image']['fields']) and $_POST['image']['table'])
	{
		foreach($_POST['image']['fields'] as $k => $v)
		{
			$database->insert("fields_mapping",array("main_table"=>$_POST['image']['table'],"main_field"=>$v,"type"=>"image_fields"));
		}
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['slug']) and $_POST['slug'])
{
//	pr($_POST['slug']);
	if(isset($_POST['slug']['main']) and isset($_POST['slug']['secondary']) and $_POST['slug']['main'] and $_POST['slug']['secondary'])
	{
		$database->insert("fields_mapping",array("main_table"=>$_POST['slug']['table'],"main_field"=>$_POST['slug']['main'],"type"=>"slug_fields","secondary_field"=>$_POST['slug']['secondary']));
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
if(isset($_POST['rel']) and $_POST['rel'])
{
	if(isset($_POST['rel']['main_table']) and isset($_POST['rel']['main_field']) and isset($_POST['rel']['secondary_table']) and isset($_POST['rel']['secondary_field']) and isset($_POST['rel']['select_field']) and $_POST['rel']['main_table'] and $_POST['rel']['main_field'] and $_POST['rel']['secondary_table'] and $_POST['rel']['secondary_field'] and $_POST['rel']['select_field'])
	{
		if(!isset($_POST['rel']['is_multiple'])) {
			$_POST['rel']['is_multiple']=0;
		} else {
			$table = $_POST['rel']['main_table']."_".$_POST['rel']['secondary_table'];
			$query = "CREATE TABLE IF NOT EXISTS `".$table."` (`id` int(250) NOT NULL AUTO_INCREMENT,`".$_POST['rel']['main_table']."_id` int(250) NOT NULL,`".$_POST['rel']['secondary_table']."_id` int(250) NOT NULL, PRIMARY KEY (`id`)) AUTO_INCREMENT=1";
			$database->query($query);
		}
		$database->insert("fields_mapping",array("main_table"=>$_POST['rel']['main_table'],"main_field"=>$_POST['rel']['main_field'],"type"=>"get_another_data","secondary_field"=>$_POST['rel']['secondary_field'],"secondary_table"=>$_POST['rel']['secondary_table'],'value'=>$_POST['rel']['select_field'],'is_multiple'=>$_POST['rel']['is_multiple']));
		header("location:"._admin_url."/admin_settings/success");
	}
	else
	{
		header("location:"._admin_url."/admin_settings/error");
	}
}
//pass all the admin settings
echo $tpl->render("themes/admin/html/admin_settings.php");
