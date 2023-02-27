<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/javascript; charset=UTF-8');


function printData($status, $errors, $data) {
    $result = array();
    $result['success'] = $status;
    $result['errors'] = $errors;
    $result['data'] = $data;
    return $result;
}

if($_POST){
    header('Content-Type: text/javascript; charset=UTF-8');
    $directory = $_SERVER['DOCUMENT_ROOT'];
    if (isset($_POST['image_base64']) && $_POST['image_base64']) {
        $module_type = $_POST['type'];
        $image_base64 = $_POST['image_base64'];
        
        list($type, $image_base64) = explode(';', $image_base64);
        list(, $image_base64)      = explode(',', $image_base64);
        $image_base64 = base64_decode($image_base64);
        
        $directory = $_SERVER['DOCUMENT_ROOT'];
        $fileName = md5(uniqid()) . '.jpg';
        
        
        $save_path = $directory."/".$module_type."s/" . $fileName;
        file_put_contents($save_path, $image_base64);

        $data_array = array();
        $data_array['image_url'] = "http://img.zingura.com/".$module_type."s/" . $fileName;
        $data_array['save_path'] = "/".$module_type."s/" . $fileName;
        echo json_encode(printData("1", "0", $data_array));
        exit;
    }
}else{
    echo "Direct Access is not allowed!";
}
?>
