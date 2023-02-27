<?php
include getcwd() . "/core/nocsrf.php";
$tpl = new bQuickTpl();
$tpl->page_title = "Admin Panel";

if (isset($_SESSION['admin_user_id'])) {
    header("Location: " . _admin_url . "/index");
}
$errmsg_arr = array();
$errflag = false;

if ($_POST) {
    $_SESSION['form'] = $_POST['data'];

//    try
    //    {
    //        // Run CSRF check, on POST data, in exception mode, for 10 minutes, in one-time mode.
    //        NoCSRF::check( 'csrf_token', $_POST, true, 60*10, false );
    //
    //        $result = 'CSRF check passed. Form parsed.';
    //
    //Clean the input data
    $username = clean($_POST['data']['username']);
    $password = clean($_POST['data']['password']);
    //Input Validations
    if ($username == '') {
        $errmsg_arr[] = 'Username is missing';
        $errflag = true;
    }
    if ($password == '') {
        $errmsg_arr[] = 'Password is missing';
        $errflag = true;
    }
    //Find the user in Database
    $get_user = $database->get("admin_users", "*", array(
        "AND" => array(
            "username" => $username,
            "password" => md5($password),
        ),
    ));

    if ($get_user) {
        if ($get_user['type'] == "user") {
            $errmsg_arr[] = 'You dont have access to this page.';
            $errflag = true;
        } else {
            unset($_SESSION['form']);
            $_SESSION['admin_user_id'] = $get_user['id'];
            //Put name in session
            $_SESSION['admin_name'] = $get_user['fullname'];
            $_SESSION['admin_username'] = $get_user['username'];
            $_SESSION['admin_type'] = $get_user['user_type'];
            //Close session writing
            session_write_close();
            if ($_SESSION['admin_type'] == "onboarder") {
                header("Location: " . _admin_url . "/table/merchant_leads");
                exit();
            } else {
                header("Location: " . _admin_url . "/table/users");
                exit();
            }
        }
    } else {

        $errmsg_arr[] = 'No such User found in Database!';
        $errflag = true;
    }
//    }
    //    catch ( Exception $e )
    //    {
    //
    //        pr($result);
    //        // CSRF attack detected
    //        $result = $e->getMessage() . ' Form ignored.';
    //    }
} else {
    $result = 'No post data yet.';
}
$token = NoCSRF::generate('csrf_token');
$tpl->formtoken = $token;
$tpl->result = $result;
$tpl->errors = $errmsg_arr;

echo $tpl->render("themes/admin/html/login.php");
