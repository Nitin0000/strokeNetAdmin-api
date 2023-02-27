<?php
if(isset($_SESSION['admin_user_id'])){
	unset($_SESSION['admin_user_id']);
	unset($_SESSION['admin_name']);
	unset($_SESSION['name']);
	unset($_SESSION['sortby']);
	session_destroy();
	header("Location: "._admin_url."/login");
	exit();
}
