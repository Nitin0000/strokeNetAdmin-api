<?php

namespace Acme\Controllers;

class AuthController extends BaseController
{

    public function signup($request, $response, $args)
    {

        $data = $request->getParsedBody();
        foreach ($data as $key => $val) {
            $data[$key] = $this->cleanValue($val);
        }

        $errors = array();

        if (!isset($data['first_name']) || $data['first_name'] == "") {
            $errors[] = "First name is required";
        }
        if (!isset($data['last_name']) || $data['last_name'] == "") {
            $errors[] = "Last name is required";
        }

        if (!isset($data['email_address']) || $data['email_address'] == "") {
            $errors[] = "Email address is required";
        }
        if ($data['email_address'] && !$this->validateEmail($data['email_address'], "email")) {
            $errors[] = "Invalid email address";
        }

        $checkEmail = $this->ci->db->get("users", array("user_id"), array("email_address" => $data['email_address']));
        if ($checkEmail['user_id']) {
            $errors[] = "An email address already exists";
        }

        if (!isset($data['phone_number']) || $data['phone_number'] == "") {
            $errors[] = "Please enter your mobile number.";
        }

        if (!isset($data['password']) || $data['password'] == "") {
            $errors[] = "Password is required";
        }

        if ($data['password']) {
            if (strlen(trim($data['password'])) < 6) {
                $errors[] = "Password needs to be of atleast 6 characters";
            }
        }

        if (!isset($data['center_id']) || $data['center_id'] == "") {
            $errors[] = "Please select a center";
        }

        if (!isset($data['user_department']) || $data['user_department'] == "") {
            $errors[] = "Please choose a department";
        }

        if (!isset($data['user_role']) || $data['user_role'] == "") {
            $errors[] = "Please choose a role";
        }

        if ($errors) {
            $output = $this->printData("error", array("message" => $errors[0]));
            return $response->withJson($output, 403);
        } else {
            $insert_user = array();
            $insert_user['first_name'] = $this->cleanValue($data['first_name']);
            $insert_user['last_name'] = $this->cleanValue($data['last_name']);
            $insert_user['fullname'] = $this->cleanValue($data['first_name'] . " " . $data['last_name']);

            $insert_user['password'] = md5(trim($data['password']));
            $insert_user['email_address'] = $this->cleanValue($data['email_address']);
            $insert_user['center_id'] = $this->cleanValue($data['center_id']);

            $insert_user['phone_number'] = $this->cleanValue($data['phone_number']);
            $insert_user['phone_number_verified'] = "1";

            if (isset($data['user_role']) && $data['user_role']) {
                $insert_user['user_role'] = $this->cleanValue($data['user_role']);
            }

            if (isset($data['user_department']) && $data['user_department']) {
                $insert_user['user_department'] = $this->cleanValue($data['user_department']);
            }

            $insert_user['ip'] = $this->getUserIP();
            $insert_user['last_login'] = date('Y-m-d H:i:s');
            $insert_user['token'] = bin2hex(openssl_random_pseudo_bytes(16));
            $insert_user['token_expire'] = date('Y-m-d H:i:s', strtotime('+30 days'));
            $insert_user["online_status"] = "0";
            $insert_user["status"] = "0";

            if (isset($data['onesignal_userid']) && $data['onesignal_userid']) {
                $insert_user['onesignal_userid'] = $this->cleanValue($data['onesignal_userid']);
            }

            $user_signup_id = $this->ci->db->insert("users", $insert_user);

            if ($user_signup_id) {

                // Send an SMS to the Admin
                $adminSMSNumbers = array(
                    "9815066990",
                    "9888948964",
                );
                foreach ($adminSMSNumbers as $phoneNumber) {
                    $adminSMSData = array(
                        "to" => "+91" . $phoneNumber,
                        "message" => "A new user has registered, please accept/reject the request from admin panel. https://strokenetchandigarh.com/admin/table/users",
                    );
                    $this->sendSMS($adminSMSData);
                }

                // Send an email to the admin
                $adminEmails = array(
                    "strokenetchandigarh@gmail.com", "strokenet.d@gmail.com",
                );

                foreach ($adminEmails as $email) {
                    $adminEmailData = array(
                        "to" => $email,
                        "subject" => "New User on StrokeNetChandigarh",
                        "text" => "A new user has registered, please accept/reject the request from admin panel.",
                        "html" => "A new user has registered, please accept/reject the request from admin panel. Go to <a href='https://strokenetchandigarh.com/admin/table/users'>https://strokenetchandigarh.com/admin/table/users</a> to accept or reject",
                    );
                    $this->sendEmail($adminEmailData);
                }

                $output = $this->printData("success", array("message" => "Your account has been successfully created and is under moderation. A notification or sms will be sent you once its approved."));
                return $response->withJson($output, 200);
            }
        }
    }

    public function login($request, $response, $args)
    {
        $data = $request->getParsedBody();
        foreach ($data as $key => $val) {
            $data[$key] = $this->cleanValue($val);
        }
        $errors = array();

        if (!isset($data['email_address']) || $data['email_address'] == "") {
            $errors[] = "Email address is required";
        }
        if ($data['email_address'] && !$this->validateEmail($data['email_address'], "email")) {
            $errors[] = "Invalid email address";
        }
        if (!isset($data['password']) || $data['password'] == "") {
            $errors[] = "Password is required";
        }
        if ($errors) {
            $output = $this->printData("error", array("message" => $errors[0]));
            return $response->withJson($output, 403);
        } else {
            $user_email = $data['email_address'];
            $password = $data['password'];

            $user_data = $this->ci->db->get(
                "users",
                array("user_id", "status"),
                array(
                    "AND" => array(
                        "email_address" => $user_email,
                        "password" => md5($password),
                    ),
                )
            );

            if ($user_data['user_id']) {
                if (ceil($user_data['status']) == 1) {
                    if (isset($data['onesignal_userid']) && $data['onesignal_userid']) {
                        $this->ci->db->update("users", array("onesignal_userid" => $data['onesignal_userid']), array("user_id" => $user_data['user_id']));
                    }
                    $this->ci->db->update("users", array(
                        "last_login" => date('Y-m-d H:i:s'),
                        "ip" => $this->getUserIP(),
                        "token" => bin2hex(openssl_random_pseudo_bytes(16)),
                        "online_status" => "1",
                        "token_expire" => date('Y-m-d H:i:s', strtotime('+30 days')),
                    ), array("user_id" => $user_data['user_id']));

                    $user = $this->getUserDetails($user_data['user_id']);
                    $output = $this->printData("success", $user);
                    return $response->withJson($output, 200);
                } else {
                    $output = $this->printData("error", array("message" => "Your account is pending verification. Once its approved, you will be able to access your account."));
                    return $response->withJson($output, 403);
                }
            } else {
                $output = $this->printData("error", array("message" => "Invalid Login Credentials"));
                return $response->withJson($output, 403);
            }
        }
    }

    public function forgotPassword($request, $response, $args)
    {
        $data = $request->getParsedBody();
        foreach ($data as $key => $val) {
            $data[$key] = $this->cleanValue($val);
        }
        if (isset($data["email_address"]) && $data['email_address'] !== "") {
            $user_email = $data['email_address'];
            $exist_user = $this->ci->db->get("users", array("user_id"), array("email_address" => $user_email));

            if ($exist_user['user_id']) {
                $pass = $this->randomPassword();
                $data = array();
                $data['password'] = md5($pass);

                $this->ci->db->update("users", $data, array("user_id" => $exist_user['user_id']));

                $emailData = array();
                $emailData['to'] = $user_email;
                $emailData['subject'] = "Forgot Password";
                $emailData['text'] = "";
                $emailData['html'] = "Hi " . $user_email . ", <br><br> We've received a request to reset your password. Your new password is : <b>" . $pass . "</b> <br><br> Thanks,<br> <b>The StrokeNetChandigarh Team</b>";

                $this->sendEmail($emailData);

                // Send a new Password via SMS.

                $output = $this->printData("success", array("message" => "New password was sent to your email address."));
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData("error", array("message" => "Such email address was not found."));
                return $response->withJson($output, 404);
            }
        } else {
            $output = $this->printData("error", array("message" => "Email address is required"));
            return $response->withJson($output, 403);
        }
    }
}