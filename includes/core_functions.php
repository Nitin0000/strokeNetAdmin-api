<?php

function get_countries(){
    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
    return $countries;
}


function current_url(){
	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	return $url;
        
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
function randomNumericPassword() {
    $alphabet = "0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function vd($val){
  echo "<pre>";
  var_dump($val);
  echo "</pre>";}

function pr($val,$var_name=NULL){
  if(!empty($val)){
	  echo "<pre>";
	  if(isset($var_name))
	  {
		  echo "<div style='color:lightgreen;background-color:black;font-size:18px'>Data for '<font color=red>".$var_name."</font>' as [key] => value</div><br>";
	  }
	  print_r($val);
	  echo "</pre>";
  }}

function verification_code() {
  $alphabet = "0123456789";
  $pass = array(); //remember to declare $pass as an array
  $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
  for ($i = 0; $i < 8; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
  }
  return implode($pass); //turn the array into a string
}
function clean($str) {
	$str = @trim($str);
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	$str = strip_tags($str);
	 return $str;}

function format_names($value){
        $explode_txt = explode("_", $value);
        $newtext = join(" ", $explode_txt);
	return ucwords($newtext);
}

function text_limit($string, $length){
	$final_output = (strlen($string) > $length) ? substr($string,0,$length).'...' : $string;
	return $final_output;}

function format_date($date){
	$final_date = date("l, F d , Y", strtotime($date));
	return $final_date;
        
}

function create_slug($value){
  $value = preg_replace("/@/", ' at ', $value);
  $value = preg_replace("/£/", ' pound ', $value);
  $value = preg_replace("/#/", ' hash ', $value);
  $value = preg_replace("/[\-+]/", ' ', $value);
  $value = preg_replace("/[\s+]/", ' ', $value);
  $value = preg_replace("/[\.+]/", '', $value);
  $value = preg_replace("/[^A-Za-z0-9\.\s]/", '', $value);
  $value = preg_replace("/[\s]/", '-', $value);
  $value = preg_replace("/\-\-+/", '-', $value);
  
  $value = strtolower($value);
  
  if (substr($value, -1) == "-")
	  {
	  $value = substr($value, 0, -1);
	  }
  if (substr($value, 0, 1) == "-")
	  {
	  $value = substr($value, 1);
	  }
  
  return $value;
  }

function file_get_ext($file){
	$ext=explode(".",$file);
	return end($ext);}

function file_name_details($FileName){
	$ext=file_get_ext($FileName);
	$f_name=str_replace(".".$ext,"",$FileName);
	$f_details['name']=$f_name;
	$f_details['ext']=$ext;
	return $f_details;}

function upload_img($insert_id, $file, $folder_name){
  if ($folder_name == "")
	  {
	  $current_dir = getcwd();
	  $folder_url  = $current_dir . '/uploads/';
	  }
  else
	  {
	  $folder       = $insert_id;
	  $current_dir  = getcwd();
	  $projects_dir = $current_dir . $folder_name . '/';
	  //echo $projects_dir;
	  
	  if (!is_dir($projects_dir))
		  {
		  mkdir($projects_dir, 777);
		  
		  }
	  
	  $folder_url = $projects_dir . $folder;
	  if (!is_dir($folder_url))
		  {
		  mkdir($folder_url, 777);
		  }
	  
	  }
  
  //foreach($formdata as $file) {
  // replace spaces with underscores
	if (isset($file['name']) and $file['name'])
	{
		$file_details=file_name_details($file['name']);
		$filename = md5($file_details['name']).".".$file_details['ext'];;
	}
  // assume filetype is false
  $typeOK = true;
  // check filetype is ok
  // if file type ok upload the file
  if ($typeOK)
	  {
	  // switch based on error code
	  switch ($file['error'])
	  {
		  case 0:
			  // check filename already exists
			  if (!file_exists($folder_url . '/' . $filename))
				  {
				  // create full filename
				  $full_url  = $folder_url . '/' . $filename;
				  $url       = $folder . '/' . $filename;
				  $full_name = $filename;
				  // upload the file
				  $success   = move_uploaded_file($file['tmp_name'], $full_url);
				  }
			  else
				  {
				  // create unique filename and upload file
				  //ini_set('date.timezone', 'Europe/London');
				  //$now = date('Y-m-d-His');
				  $now       = time();
				  $now       = $now . "-";
				  $full_url  = $folder_url . '/' . $now . $filename;
				  $full_name = $now . $filename;
				  $url       = $folder . '/' . $now . $filename;
				  $success   = move_uploaded_file($file['tmp_name'], $full_url);
				  }
			  // if upload was successful
			  if ($success)
				  {
				  // save the url of the file
				  $result['urls'][] = $folder_name."/".$full_name;
				  }
			  else
				  {
				  $result['errors'][] = "Error uploaded " . $filename . " Please try again.";
				  }
			  break;
		  case 3:
			  // an error occured
			  $result['errors'][] = "Error uploading " . $filename . " Please try again.";
			  break;
		  default:
			  // an error occured
			  $result['errors'][] = "System error uploading " . $filename . " Contact webmaster.";
			  break;
	  }
	  }
  elseif ($file['error'] == 4)
	  {
	  // no file was selected for upload
	  $result['nofiles'][] = "No file Selected";
	  }
  else
	  {
	  // unacceptable file type
	  $result['errors'][] = "$filename cannot be uploaded. Acceptable file types: gif, jpg, png.";
	  }
  //}
  return $result;

  }

function upload_img2($insert_id, $file, $folder_name){
  if ($folder_name == "")
	  {
	  $current_dir = getcwd();
	  $folder_url  = $current_dir . '/uploads/';
	  }
  else
	  {
	  $folder       = $insert_id;
	  $current_dir  = getcwd();
	  $projects_dir = $current_dir . '/uploads/' . $folder_name . '/';
	  //echo $projects_dir;
	  
	  if (!is_dir($projects_dir))
		  {
		  mkdir($projects_dir, 777);
		  }
	  
	  $folder_url = $projects_dir . $folder;
	  if (!is_dir($folder_url))
		  {
		  mkdir($folder_url, 777);
		  }
	  
	  }
  
  //foreach($formdata as $file) {
  // replace spaces with underscores
  if (isset($file['name']) and $file['name'])
	  {
		$filename = $file['name'];
	  }
  // assume filetype is false
  $typeOK = true;
  // check filetype is ok
  // if file type ok upload the file
  if ($typeOK)
	  {
	  // switch based on error code
	  switch ($file['error'])
	  {
		  case 0:
			  // check filename already exists
			  if (!file_exists($folder_url . '/' . $filename))
				  {
				  // create full filename
				  $full_url  = $folder_url . '/' . $filename;
				  $url       = $folder . '/' . $filename;
				  $full_name = $filename;
				  // upload the file
				  $success   = move_uploaded_file($file['tmp_name'], $full_url);
				  }
			  else
				  {
				  // create unique filename and upload file
				  //ini_set('date.timezone', 'Europe/London');
				  //$now = date('Y-m-d-His');
				  $now       = time();
				  $now       = $now . "-";
				  $full_url  = $folder_url . '/' . $now . $filename;
				  $full_name = $now . $filename;
				  $url       = $folder . '/' . $now . $filename;
				  $success   = move_uploaded_file($file['tmp_name'], $full_url);
				  }
			  // if upload was successful
			  if ($success)
				  {
				  // save the url of the file
				  $result['urls'][] = $full_name;
				  }
			  else
				  {
				  $result['errors'][] = "Error uploaded " . $filename . " Please try again.";
				  }
			  break;
		  case 3:
			  // an error occured
			  $result['errors'][] = "Error uploading " . $filename . " Please try again.";
			  break;
		  default:
			  // an error occured
			  $result['errors'][] = "System error uploading " . $filename . " Contact webmaster.";
			  break;
	  }
	  }
  elseif ($file['error'] == 4)
	  {
	  // no file was selected for upload
	  $result['nofiles'][] = "No file Selected";
	  }
  else
	  {
	  // unacceptable file type
	  $result['errors'][] = "$filename cannot be uploaded. Acceptable file types: gif, jpg, png.";
	  }
  //}
  return $result;

  }

function mysql_get_prim_key($table) {
		$getPKID = "";
        $sql = "SHOW COLUMNS FROM `" . strtolower($table) . "`";
        $ts  = mysql_query($sql);
        $cts = mysql_num_rows($ts);
        while ($row = mysql_fetch_array($ts)) {
			if($row['Key'] == "PRI" && $row['Extra'] == "auto_increment"){
				$getPKID =  $row['Field'];
			}
	   }
	   //echo $getPKID;
	   //exit;
	   return $getPKID ; 
    }

function _getTables($DataBase,$db_obj){
	$Tables = $db_obj->query("SHOW TABLES FROM ".$DataBase)->fetchAll();
	foreach($Tables as $k => $info)
	{
		$tbl_array[] = $info[0];
	}
	return $tbl_array;}

function _getAttributes($table) {
        $sql = "SHOW FIELDS FROM `" . strtolower($table) . "`";
        $ts  = mysql_query($sql);
        $cts = mysql_num_rows($ts);
        while ($ats = mysql_fetch_array($ts)) {
            $attr_array[] = $ats['Field'];
        }
        if (isset($attr_array) and ($attr_array)) {
            return $attr_array;
        } else {
            return false;
        }}

function _getAttrs($Table , $db_obj){
	$attrs = $db_obj->query("SHOW FIELDS FROM `" . strtolower($Table) . "`")->fetchAll();
	return $attrs;}

function slug($value){
  $value = preg_replace("/@/", ' at ', $value);
  $value = preg_replace("/£/", ' pound ', $value);
  $value = preg_replace("/#/", ' hash ', $value);
  $value = preg_replace("/[\-+]/", ' ', $value);
  $value = preg_replace("/[\s+]/", ' ', $value);
  $value = preg_replace("/[\.+]/", '', $value);
  $value = preg_replace("/[^A-Za-z0-9\.\s]/", '', $value);
  $value = preg_replace("/[\s]/", '-', $value);
  $value = preg_replace("/\-\-+/", '-', $value);
  
  $value = strtolower($value);
  
  if (substr($value, -1) == "-")
	  {
	  $value = substr($value, 0, -1);
	  }
  if (substr($value, 0, 1) == "-")
	  {
	  $value = substr($value, 1);
	  }
  
  return $value;
  }

function copyright(){
	return '<footer>
<p>Powered by <a href="#" target="_blank"><strong>CodeCrab Studios</strong></a>.</p>
</footer>';
}


function timeago($time, $timezone){
    date_default_timezone_set($timezone);
	$time = strtotime($time);
 $time_difference = time() - $time;
 $seconds = $time_difference ;
 $minutes = round($time_difference / 60);
 $hours = round($time_difference / 3600);
 $days = round($time_difference / 86400);
 $weeks = round($time_difference / 604800);
 $months = round($time_difference / 2419200);
 $years = round($time_difference / 29030400);

 if($seconds <= 60)
 {
  return "$seconds seconds ago";
 }
 elseif($minutes <= 60)
 {
  if($minutes==1)
  { return "one minute ago"; }
  else
  { return "$minutes minutes ago"; }
 }
 elseif($hours <= 24)
 {
  if($hours==1)
  { return "one hour ago"; }
  else
  { return "$hours hours ago"; }
 }
 elseif($days <=7)
 {
  if($days==1)
  { return "one day ago"; }
  else
  { return "$days days ago"; }
 }
 elseif($weeks <=4)
 {
  if($weeks==1)
  { return "one week ago"; }
  else
  { return "$weeks weeks ago"; }
 }
 elseif($months <=12)
 {
  if($months==1)
  { return "one month ago"; }
  else
  { return "$months months ago"; }
 }
 else
 {
  if($years==1)
  { return "one year ago"; }
  else
  { return "$years years ago"; }
 }}

function get_multi_img_upload_array($files){
	$post_key_arr = array_keys($files['name']);
	foreach($post_key_arr as $k=>$post_key)
	{
		foreach($files as $files_key=>$files_value)
		{
			$upload_files_array[$post_key][$files_key] = $files_value[$post_key];
		}
	}
	return $upload_files_array;}

function multi_img_upload($upload_files_array){
	foreach($upload_files_array as $post_key => $upload_array)
	{
		if($upload_array['error'] != 4)
		{
			$location = get_upload_file_location($post_key,$file_fields[$table_name]);
			$image_path = upload_img("", $upload_array,$location);
			$post_data[$post_key] = "/uploads/album_images/".$image_path['urls'][0];
		}
	}
	return $post_data;}

function multi_img_upload2($upload_files_array){
	foreach($upload_files_array as $post_key => $upload_array)
	{
		if($upload_array['error'] != 4)
		{
			if($post_key =="picture")
			{
				$image_path = upload_img2("", $upload_array, "picture_poems");
				$post_data[$post_key] = "/uploads/picture_poems/".$image_path['urls'][0];
			}
			else
			{
				$image_path = upload_img2("", $upload_array, "misc");
				$post_data[$post_key] = "/uploads/misc/".$image_path['urls'][0];
			}
		}
	}
	return $post_data;}

function db_mapping_fields($fields_mappings,$type){
	$fields_array=array();
	foreach($fields_mappings as $mappings)
	{
		if($mappings['type']==$type)
		{
			$fields_array[$mappings['main_table']][$mappings['id']]=$mappings['main_field'];
		}
	}
	return $fields_array;}

function db_slug_fields($fields_mappings){
	$fields_array=array();
	foreach($fields_mappings as $mappings)
	{
		if($mappings["type"]=="slug_fields")
		$fields_array[$mappings['main_table']][$mappings['id']]=array("main"=>$mappings['main_field'],"secondary"=>$mappings['secondary_field']);
	}
	return $fields_array;}

function db_get_another_data_fields($fields_mappings){
	$fields_array=array();
	foreach($fields_mappings as $mappings)
	{
		if($mappings["type"]=="get_another_data")
		$fields_array[$mappings['id']]=array("main_table"=>$mappings["main_table"],'main_field'=>$mappings['main_field'],"secondary_table"=>$mappings['secondary_table'],"secondary_field"=>$mappings["secondary_field"],"value"=>$mappings["value"],"is_multiple"=>$mappings["is_multiple"]);
	}
	return $fields_array;}

function db_file_fields($fields_mappings){
	$file_fields=array();
	foreach($fields_mappings as $mappings)
	{
		if($mappings["type"]=="file_fields")
		{
			$file_fields[$mappings['main_table']][$mappings['id']]=array('field'=>$mappings['main_field'],'type'=>$mappings['upload_type'],'path'=>$mappings['file_upload_path'],'allowed_exts'=>$mappings['allowed_exts']);
		}
	}
	return $file_fields;}

function sendEmail($to, $subj, $msg, $shortcodes = null, $from = null,$mail) {
     $mail->ClearAddresses(); 
     $mail->ClearAllRecipients();
    //pr($from);
    
    if (is_array($shortcodes)) {
        foreach ($shortcodes as $code => $value)
            $msg = str_replace('{{' . $code . '}}', $value, $msg);
    }
    $msg = '<body style="margin: 10px;">
        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
        <div>' . $msg . '</div></body>';
   
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
    //$mail->SMTPSecure = 'ssl';                                       // 1 = errors and messages
    // 2 = messages only
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth  = true;                  // enable SMTP authentication
    $mail->Host      = mail_host; // sets the SMTP server     mail_host
    $mail->Port      = mail_port;                    // set the SMTP port for the GMAIL server   mail_post
    $mail->Username  = mail_username; // SMTP account username    mail_username
    $mail->Password  = mail_password;        // SMTP account password   mail_password
    
    if (count($from) > 0) {
        $name = $from[0];
        $email = $from[1];
        $mail->SetFrom($email,$name);
        $mail->AddReplyTo($email,$name);
    }
    else {
        $mail->SetFrom(send_mail_from, site_name);
        $mail->AddReplyTo(reply_mail_to, site_name);
    }
 
    $mail->Subject = $subj;
    $mail->MsgHTML($msg);
    $mail->AddAddress($to,"");

    if (!$mail->Send()) {
        return false;
     }
    else {
        return true;
       $mail->ClearAddresses(); 
       $mail->ClearAllRecipients();
    }
    
}

/** GET REAL IP **/
function getRealIp() {
    $ip = getenv('HTTP_CLIENT_IP')? :
            getenv('HTTP_X_FORWARDED_FOR')? :
                    getenv('HTTP_X_FORWARDED')? :
                            getenv('HTTP_FORWARDED_FOR')? :
                                    getenv('HTTP_FORWARDED')? :
                                            getenv('REMOTE_ADDR');
    
    return $ip;
}
/** END FUNC GET REAL IP **/

function get_directory_list($Path){
	$Directories=array();
	$od=opendir($Path."/");
	while($content=readdir($od))
	{
		if($content!='.' and $content !='..')
		{
			if(is_dir($Path.'/'.$content))
			{
                            if($content !== "thumb"){
				$Directories[]=$content;
                            }
			}
		}
	}
	return $Directories;}

function get_upload_file_location($Field_Name,$File_Fields){
	$loc='';
	if(!empty($File_Fields)){
		foreach($File_Fields as $db_key=>$field_info){
			if($Field_Name == $field_info['field'])
			{
				$loc=$field_info['path'];
			}
		}
		return $loc;
	}
	return;}

function rrmdir($dir) {
	if (is_dir($dir))
	{
		$objects = scandir($dir);
		foreach ($objects as $object)
		{
			if ($object != "." && $object != "..")
			{
				if (filetype($dir."/".$object) == "dir")rrmdir($dir."/".$object);
				else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}}

function remove_empty_locations($Array){
	if(is_array($Array))
	{
		$new_array=array();
		foreach($Array as $k=>$v)
		{
			if(is_array($v)){
			$v=remove_empty_locations($v);
			$new_array[$k]=$v;
			}
			elseif($v==""){
				unset($Array);
			}
			else{
				$new_array[$k]=$v;
			}
		}
		return $new_array;
	}
	return;}

function funcs(){
	$a = get_defined_functions();
	$user_defined = $a['user'];
	sort($user_defined);
	pr($user_defined);}

function export($db_obj , $skip_data = false){
	$export_string = "";
	$db_name = mysql_current_db($db_obj);
	$Tables = _getTables($db_name,$db_obj);
	foreach($Tables as $k=>$tbl_name){
		$tbl_info[$tbl_name] = _getAttrs($tbl_name , $db_obj);
	}
	foreach($tbl_info as $tbl_name=>$tbl_info2)
	{
		$show_primary = false;
		$export_string.="
CREATE TABLE IF NOT EXISTS `$tbl_name` (";
		$ins_fields=array();
		foreach($tbl_info2 as $k=>$field_info)
		{
			$field_name=$field_info['Field'];
			$field_type=" ".$field_info['Type'];
			if($field_info['Null'] == "NO")
			$null_status = " NOT NULL";
			else
			$null_status = " NULL";
			if($field_info['Extra'] == 'auto_increment'){
				$auto_inc = ' AUTO_INCREMENT';
				$field_info['Extra']='';
			}
			else{
				$auto_inc = '';
				if($field_info['Extra']){
					$field_info['Extra'] = " ".$field_info['Extra'];
					$field_info['Extra'] = strtoupper($field_info['Extra']);
				}
			}
			if($field_info['Default'] == "0")
			$field_info['Default'] = " DEFAULT '0'";
			elseif($field_info['Default']){
				if(is_numeric($field_info['Default']))
				$field_info['Default'] = " DEFAULT '".$field_info['Default']."'";
				else
				$field_info['Default'] = " DEFAULT ".$field_info['Default'];
			}else
			$field_info['Default'] = "";
			if($field_info['Key'] == "PRI"){
				$show_primary = true;
				$primary = "
PRIMARY KEY (`".$field_info['Field']."`)";
			}
			else{
				$ins_fields[]=$field_info['Field'];
			}
			$export_string.="
`".$field_name."`".$field_type.$null_status.$auto_inc.$field_info['Default'].$field_info['Extra'].",";
		}
		if($show_primary == true)
		$export_string.=$primary;
		$export_string.="
);
";
		if($skip_data == false){
			$ins_query='';
	//echo $db_obj->count($tbl_name);
			if($db_obj->count($tbl_name))
			{
				$ins_fields_query = implode(",",$ins_fields);
				$tbl_data = $db_obj->select($tbl_name,$ins_fields);
				foreach($tbl_data as $tbl_data_key => $tbl_data_val){
					$tbl_data_val_array = array();
	//echo "&gt;";
					foreach($tbl_data_val as $tbl_data_val_val){
						//$tbl_data_val_val = htmlspecialchars($tbl_data_val_val);
						$tbl_data_val_array[]=$tbl_data_val_val;
					}
					$tbl_data_val_string = implode("','",$tbl_data_val_array);
					$tbl_data_val_string = '\''.$tbl_data_val_string.'\'';
					$ins_query.='
	INSERT INTO '.$tbl_name.'('.$ins_fields_query.') values ('.$tbl_data_val_string.');
	';
				}
			}
			if($ins_query){
				$export_string.=$ins_query;
			}
		}
	}
//	pr($tbl_info);exit;
//	return $tbl_info;
	$a['file_name'] = $db_name.".sql";
	$a['file_contents'] = $export_string;
	return $a;}

function mysql_current_db($db_obj){
	$r = $db_obj->query("SELECT DATABASE()")->fetchAll();
	return $r[0][0];}

function directory_contents($Dir_Path , $ext = NULL){
	$dir_data = scandir($Dir_Path);
	$new_dir_data = array();
	foreach($dir_data as $k=>$object){
		if($object !="." && $object !=".."){
			$f_type = filetype($Dir_Path."/".$object);
			if(isset($ext)){
				if($ext == $f_type){
					$new_dir_data[] = $object;
				}
				elseif(file_get_ext($object) == $ext){
					$new_dir_data[] = $object;
				}
			}
			else{
				$new_dir_data[$f_type][] = $object;
			}
		}
	}
	return $new_dir_data;}

function backup_db($db_obj){
	$db_name = mysql_current_db($db_obj);
	$aa = export($db_obj,true);
	$dir_date_path = getcwd().'\config\backup_restore\\'.date('d-M-Y');
	$dir_time_path = $dir_date_path."\backup-".time();
	if(!is_dir($dir_date_path)){
		mkdir($dir_date_path, 777);
	}
	if(!is_dir($dir_time_path)){
		mkdir($dir_time_path, 777);
	}
	$file = @fopen($dir_time_path."\\".$aa['file_name'],'w');
	fwrite($file,$aa['file_contents']);
	@fclose($file);
	$Tables = _getTables($db_name,$db_obj);
	foreach($Tables as $k=>$tbl_name){
		$file_path = $dir_time_path."\\".$tbl_name.'.txt';
		$file = @fopen($file_path,'w');
		$sql = "SELECT * FROM ".$tbl_name;
		$res = $db_obj->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$res = serialize($res);
		fwrite($file,$res);
		@fclose($file);
	}
	return true;}

function restore_db($db_obj , $Restore_Path){

	$temp ='';
	$db_name = mysql_current_db($db_obj);
	$sql_path = getcwd()."\config\backup_restore\\".$Restore_Path."\\".$db_name.".sql";
	$temp.= '$sql_path- '.$sql_path."\n";
	$temp.= '$Restore_Path- '.$Restore_Path."\n";
	if(!file_exists($sql_path)){
		return false;
	}
	run_sql_file($sql_path,$db_obj);
	$Tables = _getTables($db_name,$db_obj);

	foreach($Tables as $k=>$tbl_name){
		$file_path = getcwd().'\config\backup_restore\\'.$Restore_Path.'\\'.$tbl_name.'.txt';
		$file_contents = file_get_contents($file_path);
		$data = unserialize($file_contents);
		$sql = "TRUNCATE TABLE $tbl_name";
		$db_obj->query($sql);
		foreach($data as $tbl_data){
			$db_obj->insert($tbl_name,$tbl_data);
			$temp.='$tbl_data- '.serialize($tbl_data)."<br>";
		}
	}
	$log_file_path = getcwd().'\config\restore_log.txt';

//	$file = @fopen($log_file_path,'w');
//	@fwrite($file,$temp);
//	@fclose($file);

	return true;}

function run_sql_file($location , $db_obj){
    //load file
    $commands = file_get_contents($location);
	$temp = '$location- '.$location.'

';
    //delete comments
    $lines = explode("\n",$commands);
    $commands = '';
    foreach($lines as $line){
        $line = trim($line);
        if( $line && !startsWith($line,'--') ){
            $commands .= $line . "\n";
        }
    }

    //convert to array
    $commands = explode(";", $commands);

    //run commands
    $total = $success = 0;
    foreach($commands as $command){
        if(trim($command)){
			$temp.= $command.'

';
			$success += (@$db_obj->query($command) == false ? 0 : 1);
            $total += 1;
        }
    }

//	$file = @fopen(getcwd().'\config\runsql_log.txt','w');
//	@fwrite($file,$temp);
//	@fclose($file);

    //return number of successful queries and total number of queries found
    return array(
        "success" => $success,
        "total" => $total
    );}

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);}

function get_backup_dates(){
	$restore_dates = array();
	$dir_path = getcwd()."\config\backup_restore";
	$dir_contents = directory_contents($dir_path , "dir");
	if(!empty($dir_contents)){
		$restore_dates = $dir_contents;
	}
	return $restore_dates;}

function get_restore_pts($got_date){
	$restore_pts = array();
	$dir_path = getcwd()."\config\backup_restore\\".$got_date;
	$dir_contents = directory_contents($dir_path , "dir");
	if(!empty($dir_contents)){
		$restore_pts = $dir_contents;
	}
	return $restore_pts;}
        
        
// Pagination set on page //   
        function paginate($perpage,$page_no_var){
        
        if (isset($page_no_var) && $page_no_var) {
        $page_no = str_replace("p:", "", $page_no_var);
        if ($page_no == "" || $page_no == "p" || $page_no == "p:") {
            $page_no = 1;
            if ($page_no == 1 || $page_no == 0) {
                $next_number = 0;
                
            } else {
                $next_number = $page_no * $perpage - $perpage;
            }
        } else {
            $page_no = $page_no;
            if ($page_no == 1 || $page_no == 0) {
                $next_number = 0;
            } else {
                $next_number = $page_no * $perpage - $perpage;
            }
        }
    } else {
        $page_no = 1;
        if ($page_no == 1 || $page_no == 0) {
            $next_number = 0;
        } else {
            echo $next_number = $page_no * $perpage - $perpage;
        }
    }
            
            $page_array = array();
            $page_array['next_number'] = $next_number;
            $page_array['perpage'] = $perpage;
            $page_array['page_no'] = $page_no;
            return $page_array;
        
    }  
    
    function sendPush($sendto = "user", $data_push) {
                if ($sendto == "user") {
                    $appId = USER_APP_ID;
                    $restKey = USER_APP_RESTKEY;
                } else {
                    $appId = MERCHANT_APP_ID;
                    $restKey = MERCHANT_APP_RESTKEY;
                }
                
                $launch_url = "zingura://zingura.com/";
                if(isset($data_push['action_type']) && $data_push['action_type']){
                    $launch_url = "zingura://zingura.com/".$data_push['action_type'];
                }
                if(isset($data_push['action_type']) && $data_push['action_type'] && isset($data_push['action_id']) && $data_push['action_id']){
                    $launch_url = "zingura://zingura.com/".$data_push['action_type']."/".$data_push['action_id'];
                }
                
                $fields = array(
                    'app_id' => $appId,
                    'data' => array(
                        "action_type" => $data_push['action_type'],
                        "action_id" => $data_push['action_id']
                    ),
                    'contents' => array(
                        "en" => $data_push['message']
                    ),
                    'include_player_ids' => $data_push['devices'],
                    'headings' => array(
                        "en" => $data_push['title']
                    ),
                    'url' => $launch_url
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                    'Authorization: Basic ' . $restKey));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);
                return $response;
            }
            
            
function getCountData($database, $type = "today", $date_range = array()){
    switch ($type) {
        case "today":
            $query = get_count_data_by_date('DATE(created) = CURDATE()');
            break;
        case "yesterday":
            $query = get_count_data_by_date('DATE(created) = CURDATE() - 1');
            break;
        case "this_week":
            $query = get_count_data_by_date('WEEKOFYEAR(created) = WEEKOFYEAR(NOW())');
            break;
        case "this_month":
            $query = get_count_data_by_date('MONTH(created) = MONTH(CURDATE())');
            break;
        case "last_month":
            $query = get_count_data_by_date('MONTH(created) = MONTH(CURDATE() - INTERVAL 1 MONTH)');
            break;
        case "this_year":
            $query = get_count_data_by_date('YEAR(created) = YEAR(CURDATE())');
            break;
        case "last_year":
            $query = get_count_data_by_date('YEAR(created) = YEAR(CURDATE() - INTERVAL 1 YEAR)');
            break;
        case "date_range":
            $query = "SELECT
            (SELECT COUNT(*) FROM users WHERE (DATE(created) BETWEEN DATE('".$date_range[0]."') AND DATE('".$date_range[1]."'))) as total_users,
            (SELECT COUNT(*) FROM events WHERE (DATE(created) BETWEEN DATE('".$date_range[0]."') AND DATE('".$date_range[1]."'))) as total_events,
            (SELECT COUNT(*) FROM user_interests WHERE (DATE(created) BETWEEN DATE('".$date_range[0]."') AND DATE('".$date_range[1]."'))) as total_user_interests";
            break;
        default:
            $query = "SELECT
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM events) as total_events,
            (SELECT COUNT(*) FROM user_interests) as total_user_interests";

    }
    
    $get_counts = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $get_counts;
}
function get_count_data_by_date($date){
    $query = "SELECT
            (SELECT COUNT(*) FROM users WHERE ".$date.") as total_users,
            (SELECT COUNT(*) FROM events WHERE ".$date.") as total_events,
            (SELECT COUNT(*) FROM user_interests WHERE ".$date.") as total_user_interests";
    return $query;
}

function get_months($date1, $date2) { 
    $time1  = strtotime($date1); 
    $time2  = strtotime($date2); 
    $my     = date('mY', $time2); 

    $months = array(date('F Y', $time1)); 

    while($time1 < $time2) { 
       $time1 = strtotime(date('Y-m-d', $time1).' +1 month'); 
       if(date('mY', $time1) != $my && ($time1 < $time2)) 
          $months[] = date('F Y', $time1); 
    } 

    $months[] = date('F Y', $time2); 
    return array_reverse($months); 
 }



?>