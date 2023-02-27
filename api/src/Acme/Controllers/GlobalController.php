<?php
namespace Acme\Controllers;

class GlobalController extends BaseController
{

    // Get a single content page
    public function getSinglePage($request, $response, $args)
    {
        $getSinglePage = $this->ci->db->get("pages", "*", array("id" => $args['pageId']));
        $output = $this->printData("success", $getSinglePage);
        return $response->withJson($output, 200);
    }

    // Get a single content page
    public function getPages($request, $response, $args)
    {
        $pages = $this->ci->db->select("pages", "*", array("ORDER" => array("id" => "ASC")));
        $output = $this->printData("success", $pages);
        return $response->withJson($output, 200);
    }

    public function getCenters($request, $response, $args)
    {
        $get_centers = $this->ci->db->select("centers", "*", array("status" => 1));
        $output = $this->printData("success", $get_centers);
        return $response->withJson($output, 200);
    }    

    public function getHubs($request, $response, $args)
    {
        $get_hubs = $this->ci->db->select("centers", "*", array("AND" => array("status" => 1, "is_hub" => "yes")));
        $output = $this->printData("success", $get_hubs);
        return $response->withJson($output, 200);
    }

    public function getComorbidities($request, $response, $args)
    {
        $get_comorbidities = $this->ci->db->select("comorbidities", "*", array("ORDER" => array("id" => "DESC")));
        $output = $this->printData("success", $get_comorbidities);
        return $response->withJson($output, 200);
    }

    public function globalSettings($request, $response, $args)
    {
        $globalSettings = array();
        $globalSettings['departments'] = array(
            array(
                "name" => "Neurology",
                "value" => "neurology",
                "roles" => array(
                    array(
                        "name" => "Physician",
                        "value" => "physician",
                    ),
                    array(
                        "name" => "Senior Resident",
                        "value" => "senior_resident",
                    ),
                    array(
                        "name" => "Emergency Resident",
                        "value" => "emergency_resident",
                    ),
                    array(
                        "name" => "Stroke Nurse",
                        "value" => "stroke_nurse",
                    ),
                )
            ),
            array(
                "name" => "Emergency",
                "value" => "emergency",
                "roles" => array(
                    array(
                        "name" => "Physician",
                        "value" => "physician",
                    ),
                    array(
                        "name" => "Senior Resident",
                        "value" => "senior_resident",
                    ),
                    array(
                        "name" => "Emergency Resident",
                        "value" => "emergency_resident",
                    ),
                )
            ),
            array(
                "name" => "Radio Diagnosis",
                "value" => "radio_diagnosis",
                "roles" => array(
                    array(
                        "name" => "Physician",
                        "value" => "physician",
                    ),
                    array(
                        "name" => "Senior Resident",
                        "value" => "senior_resident",
                    ),
                    array(
                        "name" => "Emergency Resident",
                        "value" => "emergency_resident",
                    ),
                )
            ),
            array(
                "name" => "Internal Medicine",
                "value" => "internal_medicine",
                "roles" => array(
                    array(
                        "name" => "Physician",
                        "value" => "physician",
                    ),
                    array(
                        "name" => "Senior Resident",
                        "value" => "senior_resident",
                    ),
                    array(
                        "name" => "Emergency Resident",
                        "value" => "emergency_resident",
                    ),
                )
            ),
            array(
                "name" => "COVID",
                "value" => "covid",
                "roles" => array(
                    array(
                        "name" => "Consultant",
                        "value" => "consultant",
                    ),
                    array(
                        "name" => "Senior Resident",
                        "value" => "senior_resident",
                    ),
                    array(
                        "name" => "Junior Resident",
                        "value" => "junior_resident",
                    ),
                )
            ),
            array(
                "name" => "PRM",
                "value" => "prm",
                "roles" => array(
                    array(
                        "name" => "Physiatrist",
                        "value" => "physiatrist",
                    ),
                    array(
                        "name" => "Physiotherapist",
                        "value" => "physiotherapist",
                    ),
                    array(
                        "name" => "Occupational Therapist",
                        "value" => "occupational_therapist",
                    ),
                    array(
                        "name" => "Student / Resident",
                        "value" => "student/resident",
                    ), 
                )
            ),
        );
        // $globalSettings['roles'] = array(
        //     array(
        //         "name" => "Physician",
        //         "value" => "physician",
        //     ),
        //     array(
        //         "name" => "Senior Resident",
        //         "value" => "senior_resident",
        //     ),
        //     array(
        //         "name" => "Emergency Resident",
        //         "value" => "emergency_resident",
        //     ),
        //     array(
        //         "name" => "Stroke Nurse",
        //         "value" => "stroke_nurse",
        //     ),
        //     array(
        //         "name" => "Consultant",
        //         "value" => "consultant",
        //     ),
        //     array(
        //         "name" => "Junior Resident",
        //         "value" => "junior_resident",
        //     ),
        //     array(
        //         "name" => "Physiatrist",
        //         "value" => "physiatrist",
        //     ),
        //     array(
        //         "name" => "Physiotherapist",
        //         "value" => "physiotherapist",
        //     ),
        //     array(
        //         "name" => "Occupational Therapist",
        //         "value" => "occupational_therapist",
        //     ),
        //     array(
        //         "name" => "Student / Resident",
        //         "value" => "student/resident",
        //     ),

        // );
        $output = $this->printData("success", $globalSettings);
        return $response->withJson($output, 200);
    }

    public function getPhotoGallery($request, $response, $args)
    {
        $get_photo_gallery = $this->ci->db->select("photo_gallery", "*", array("status" => 1, "ORDER" => array("id" => "DESC")));

        foreach ($get_photo_gallery as $key => $val) {
            $val['image'] = str_replace('/uploads', '', $val['image']);

            $get_photo_gallery[$key]['image_thumb'] = uploads_url . '/thumb/compress.php?src=' . $val['image'] . '&w=200&h=200&zc=1';
            $get_photo_gallery[$key]['image'] = uploads_url . $val['image'];
        }

        $output = $this->printData("success", $get_photo_gallery);
        return $response->withJson($output, 200);
    }

    public function contactUs($request, $response, $args)
    {
        $header_userId = $request->getHeader('userId');
        $header_userToken = $request->getHeader('userToken');
        if ($this->validateUser($header_userId[0], $header_userToken[0])) {
            $data = $request->getParsedBody();
            foreach ($data as $key => $val) {
                $data[$key] = $this->cleanValue($val);
            }
            $errors = array();
            if (!isset($data['name']) || $data['name'] == "") {
                $errors[] = "Name is required";
            }
            if (!isset($data['email']) || $data['email'] == "") {
                $errors[] = "Email address is required";
            }
            if (!isset($data['phone']) || $data['phone'] == "") {
                $errors[] = "Phone/Mobile number is required";
            }
            if (!isset($data['message']) || $data['message'] == "") {
                $errors[] = "Message is required";
            }
            if ($errors) {
                $output = $this->printData("error", array("message" => $errors[0]));
                return $response->withJson($output, 403);
            } else {
                $output = $this->printData("error", array("message" => "request_sent"));
                return $response->withJson($output, 200);
            }
        } else {
            $output = $this->printData("error", array("message" => "INVALID_CREDENTIALS"));
            return $response->withJson($output, 403);
        }
    }

}
