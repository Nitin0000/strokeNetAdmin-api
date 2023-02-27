<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/javascript; charset=UTF-8');

// echo phpinfo();
// exit;

function printData($status, $errors, $data) {
    $result = array();
    $result['success'] = $status;
    $result['errors'] = $errors;
    $result['data'] = $data;
    return $result;
}

if($_POST){
    header('Content-Type: text/javascript; charset=UTF-8');
    $directory = dirname(__FILE__);

    if (isset($_POST['module_type']) && $_POST['module_type'] == "") {
        echo json_encode(printData("0", "1", "type is required"));
        exit;
    } 
    if(!$_FILES['file']['tmp_name']){
        echo json_encode(printData("0", "1", "file is required"));
        exit;
    }
    else {
        $type = $_POST['module_type'];
        $target_path = $directory . "/" . $type . "s/";
        
        $ext = end((explode(".", $_FILES['file']['name'])));
        $fileName = md5(time()) .".". $ext;
        
        //$fileName = md5(time()) . '.jpg';
        $save_path = "/" . $type . "s/" . $fileName;
        $target_path = $target_path . $fileName;

        //Upload File
        move_uploaded_file($_FILES['file']['tmp_name'], $target_path);

        $data_array = array();
        $data_array['image_url'] = "http://$_SERVER[HTTP_HOST]/uploads/thumb/compress.php?src=" . $type . "s/" . $fileName."&w=200&h=200&zc=1";               
        $data_array['save_path'] = $save_path;
        $data_array['full_image_url'] = "http://$_SERVER[HTTP_HOST]/uploads".$save_path;
        $data_array['file_type'] = $ext;
        echo json_encode(printData("1", "0", $data_array));
        exit;
    }
}else{
    echo "Direct Access is not allowed!";
}
?>