<?php
$tpl =  new bQuickTpl();
$tpl->page_title = "Admin Panel - Website Settings";
if(isset($_SESSION['admin_user_id']))
{
	 if(isset($_POST) && $_POST)
		 {
			 if(isset($_FILES['data']['name']) and !empty($_FILES['data']['name']))
			 {
				 $upload_file_allow=false;
				 foreach($_FILES['data']['error'] as $post_key=>$status)
				 {
					 if($status!=4)
					 {
						 $upload_file_allow=true;
					 }
				 }
				 if($upload_file_allow==true){
					 $upload_files_array=get_multi_img_upload_array($_FILES['data']);
					 if(isset($upload_files_array) and !empty($upload_files_array))
					 {
						 $post_data=multi_img_upload2($upload_files_array);
						 foreach($post_data as $post_key => $img_path)
						 {
							 $_POST['data'][$post_key]=$img_path;
						 }
					 }
				 }
			 }
		foreach($_POST['data'] as $key=>$value){
			$key_data = array();	
			$key_data['content']['setting_name'] = $key;
			$key_data['content']['setting_value'] = $value;
			$database->update('settings', $key_data['content'],array( 'setting_name' => $key));
		}
		$tpl->update = 1;
	}
}
else
{
	header("Location: "._admin_url."/login");
	exit();
}
include(getcwd()."/modules/admin/common.php");
echo $tpl->render("themes/admin/html/website_settings.php");
