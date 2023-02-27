<?php
header('Access-Control-Allow-Origin: *');
$date = date("d-M-Y");
if(isset($_POST['downloads']) && $_POST['downloads'] !== ""){
$files = $_POST['downloads'];

if(count($files) > 1){
   # create new zip opbject
$zip = new ZipArchive();
 
# create a temp file & open it
$tmp_file = tempnam('.','');
$zip->open($tmp_file, ZipArchive::CREATE);
# loop through each file
foreach($files as $file){
    # download file
    $download_file = file_get_contents($file);
    #add it to the zip
    $zip->addFromString(basename($file),$download_file);
}
# close zip
$zip->close();
 
# send the file to the browser as a download
header('Content-disposition: attachment; filename=selfies_'.$date.'.zip');
header('Content-type: application/zip');
readfile($tmp_file); 

}else{
    $file = $files[0];
    header("Expires: 0");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $basename = pathinfo($file, PATHINFO_BASENAME);

    header("Content-type: application/".$ext);
    // tell file size
    header('Content-length: '.filesize($file));
    // set file name
    header("Content-Disposition: attachment; filename=\"$basename\"");
    readfile($file);
    // Exit script. So that no useless data is output.
    exit;
}       
}else{ ?>
   <script>
   alert('Please select atleast one selfie for download');
window.location = "https://manager.zingura.com/selfies";
   </script>
<?php } ?>


