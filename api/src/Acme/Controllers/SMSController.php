<?php

namespace Acme\Controllers;

class SMSController extends BaseController
{

    public function verifyOTP($request, $response, $args)
    {
        $data = $request->getParsedBody();
        foreach ($data as $key => $val) {
            $data[$key] = $this->cleanValue($val);
        }
        $errors = array();
        if (!isset($data['phone_number']) || $data['phone_number'] == '') {
            $errors[] = 'Mobile Number is required';
        }
        if (!isset($data['otp_code']) || $data['otp_code'] == '') {
            $errors[] = 'OTP is required';
        }
        if (!isset($data['message_api_code']) || $data['message_api_code'] == '') {
            $errors[] = 'Message code is required';
        }
        if ($errors) {
            $output = $this->printData('error', array("message" => $errors[0]));
            return $response->withJson($output, 400);
        } else {
            $verifyOTP = $this->findAndVerifyOTP($data);
            if ($verifyOTP == "1") {
                $getUser = $this->ci->db->get("users", array("user_id"), array("phone_number" => $data['phone_number']));
                $finalData = array();
                if (isset($getUser['user_id']) && $getUser['user_id']) {
                    $finalData['new_user'] = false;
                    if (isset($data['onesignal_userid']) && $data['onesignal_userid']) {
                        $this->ci->db->update("users", array("onesignal_userid" => $data['onesignal_userid']), array("user_id" => $getUser['user_id']));
                    }
                    $this->ci->db->update("users", array(
                        "last_login" => date('Y-m-d H:i:s'),
                        "ip" => $this->getUserIP(),
                        "token" => bin2hex(openssl_random_pseudo_bytes(16)),
                        "online_status" => "1",
                        "token_expire" => date('Y-m-d H:i:s', strtotime('+30 days')),
                    ), array("user_id" => $getUser['user_id']));

                    $user = $this->getUserDetails($getUser['user_id']);
                    $finalData['user_data'] = $user;
                } else {
                    $finalData['new_user'] = true;
                }
                $output = $this->printData('error', $finalData);
                return $response->withJson($output, 200);
            } else {
                $output = $this->printData('error', array("message" => "Invalid OTP. Please try again."));
                return $response->withJson($output, 400);
            }
        }
    }

    public function sendOTPCode($request, $response, $args)
    {
        $data = $request->getParsedBody();
        foreach ($data as $key => $val) {
            $data[$key] = $this->cleanValue($val);
        }
        $errors = array();
        if (!isset($data['phone_number']) || $data['phone_number'] == '') {
            $errors[] = 'Mobile Number is required';
        }
        if (isset($data['phone_number']) && !$this->validateMobileNumber($data['phone_number'])) {
            $errors[] = 'Invalid mobile number';
        }
        if ($errors) {
            $output = $this->printData('error', array("message" => $errors[0]));
            return $response->withJson($output, 400);
        } else {
            if ($data['phone_number'] == "9876543210") { // Testing mobile number
                $generateCode = 1234;
            } else {
                $generateCode = rand(1001, 9998);
            }
            $md5OTPCode = md5($generateCode);

            $smsData = array();
            $smsData['to'] = "+91" . $data['phone_number'];
            $smsData['message'] = $generateCode . ' is your one time password for StrokeNetChandigarh app.';

            $smsreturn = $this->sendSMS($smsData);
            $encodedData = json_decode($smsreturn, true);
            if (isset($encodedData['message_uuid']) && count($encodedData['message_uuid']) > 0) {
                $insert_otp = $this->ci->db->insert("sms_verifications", array(
                    "phone_number" => $data['phone_number'],
                    "otp_code" => $md5OTPCode,
                    "message_api_code" => $encodedData['message_uuid'][0],
                    "created" => date("Y-m-d : H:i:s"),
                ));
                if ($insert_otp) {
                    $output = $this->printData('success', array('message' => 'sms_sent', 'otp_code' => $md5OTPCode, 'message_data' => $encodedData['message_uuid'][0]));
                    return $response->withJson($output, 200);
                }
            } else {
                $output = $this->printData('error', array('message' => 'There was some problem sending OTP. Please try again.', 'message_data' => $encodedData['message']));
                return $response->withJson($output, 400);
            }
        }
    }
}
