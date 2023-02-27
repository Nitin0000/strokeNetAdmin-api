<?php
$tpl =  new bQuickTpl();
include(getcwd()."/modules/admin/common.php");
$errmsg_arr = array();
$errflag = false;
$success_arr = array();
$successflag = false;
if(isset($params[2]) && $params[2] == "error"){
	$tpl->errors = $_SESSION['ERRMSG_ARR'];
}
if(isset($params[2]) && $params[2] == "success"){
	$tpl->success = $_SESSION['SUCCESSMSG_ARR'];
}
if($_POST){
	$old_password = clean($_POST['data']['old_password']);
	$new_password = clean($_POST['data']['new_password']);
	$repeat_password = clean($_POST['data']['repeat_password']);
	$checkpassword = md5($old_password);
	//Input Validations
    if($old_password == '') {
        $errmsg_arr[] = 'Old Password Field Value is missing!';
        $errflag = true;
    }else
    if($new_password == '') {
        $errmsg_arr[] = 'New Password Field Value is missing!';
        $errflag = true;
    }
	else
    if($repeat_password == '') {
        $errmsg_arr[] = 'Repeat Password Field Value is missing!';
        $errflag = true;
    }
	else if($new_password !== $repeat_password){
		 $errmsg_arr[] = 'Repeat Password Field Value doesnot match with New Password';
         $errflag = true;
	}
	else if($new_password == $old_password){
		 $errmsg_arr[] = 'New Password cannot be same as Old Password';
         $errflag = true;
	}else{
            
		$get_user = $database->get("admin_users","password",array("AND"=>array("username"=>$_SESSION['admin_username'],"password"=>$checkpassword)));
                
		if($get_user){
			if($get_user['password'] == md5($new_password)){
				 $errmsg_arr[] = 'New Password cannot be same as Old Password';
				 $errflag = true;
			}
			else{
				$newpass = md5($new_password);
				$updatepassword = $database->update("admin_users",array("password"=>$newpass),array("AND"=>array("username"=>$_SESSION['admin_username'],"password"=>$checkpassword)));		
				if($updatepassword){
					 $success_arr[] = 'Password was successfully Changed!';
					 $successflag = true;
				}	
			}
		}else{
			 $errmsg_arr[] = 'Old Password doesnt match with the Database Entry!';
        	 $errflag = true;
		}
                
                
                
	}//If there are input validations, redirect back to the login form
    if($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        session_write_close();
        header("Location: ".main_url."/admin/changepassword/error");
        exit();
    }
	if($successflag) {
        $_SESSION['SUCCESSMSG_ARR'] = $success_arr;
        session_write_close();
        header("Location: ".main_url."/admin/changepassword/success");
        exit();
    }
}
$tpl->page_title = "Change Password";
echo $tpl->render("themes/admin/html/changepassword.php");
