<?php

if (isset($_SESSION['uid'])) {
    $user_id = $_SESSION['uid'];
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = 0;
}
//echo $user_id;
//pass the database object to template for surety
$tpl->database = $database;

//get all user defined functions in an array
$get_defined_functions = get_defined_functions();
$user_defined_funcs = $get_defined_functions['user'];

//we'll check for all functions if they are not defined earlier then we will define them
if (!empty($user_defined_funcs)) {
    if (!in_array("show_ratings_html", $user_defined_funcs)) {

        function show_ratings_html($module_type, $module_id, $db_obj, $show_bigger = false, $show_avg_rating = false) {
            $ratings = get_rating($module_type, $module_id, $db_obj);
            
            if($ratings){
                $tot_ratings = count($ratings);
                $val_rating = count_rating($module_type, $module_id, $db_obj);
                $avg_rating = round(($val_rating / $tot_ratings), 1);
                if ($avg_rating == '') {
                    $avg_rating = 0;
                }
                if ($show_avg_rating == true) {
                    $data = array();
                    $data['tot_ratings'] = $tot_ratings;
                    $data['avg_rating'] = $avg_rating;
                    echo rating_stars2($avg_rating, $show_bigger, $data);
                } else {
                    echo rating_stars($avg_rating, $show_bigger);
                }
            }else{
                 echo rating_stars(0, $show_bigger);
            }
        }

    }

    if (!in_array("get_ratings_html", $user_defined_funcs)) {

        function get_ratings_html($module_type, $module_id, $db_obj, $show_bigger = 'false') {
            $ratings = get_rating($module_type, $module_id, $db_obj);
            
            if($ratings){
                 $tot_ratings = count($ratings);
                $val_rating = count_rating($module_type, $module_id, $db_obj);
                $avg_rating = round(($val_rating / $tot_ratings), 1);
                if ($avg_rating == '') {
                    $avg_rating = 0;
                }
                return rating_stars($avg_rating, $show_bigger);
            }else{
                return rating_stars(0, $show_bigger);
            }
            
           
        }

    }
   if (!in_array("get_ratings_text", $user_defined_funcs)) {
        function get_ratings_text($module_type, $module_id, $db_obj) {
            $ratings = get_rating($module_type, $module_id, $db_obj);
            if($ratings){
            $tot_ratings = count($ratings);
            $val_rating = count_rating($module_type, $module_id, $db_obj);
            
            $avg_rating = round(($val_rating / $tot_ratings), 1);
            
            if ($avg_rating == '') {
                $avg_rating = 0;
            }
            if($tot_ratings == 1){
                return $avg_rating."/5 (".$tot_ratings." rating)";
            }else{
                return $avg_rating."/5 (".$tot_ratings." ratings)";
            }
            }else{
                return "0/5 (0 ratings)";
            }
        }
    }
    if (!in_array("rating_stars", $user_defined_funcs)) {

        function rating_stars($rating, $show_bigger = 'false') {
            $show_half = false;
            $rating_html = '';
            $five = 5;
            $bigger_icon = '';
            $number = 1;
            if ($show_bigger == 'true')
                $bigger_icon = ' fa-2x';
            if (strpos($rating, '.')) {
                $temp = explode('.', $rating);
                $temp = $temp[1];
                $temp = substr($temp, 0, 1);
                if ($temp > 5) {
                    $rating = ceil($rating);
                }
                if ($temp < 5) {
                    $rating = floor($rating);
                }
                if ($temp == 5) {
                    $show_half = true;
                    $rating = floor($rating);
                }
            }
            for ($i = 0; $i < $rating; $i++) {
                $rating_html .= '<i id="codebasket_rating_star" star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star"></i>';
                $five--;
                $number++;
            }
            if ($show_half == true) {
                $rating_html .= '<i id="codebasket_rating_star" star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star-half-o"></i>';
                $five--;
                $number++;
            }
            for ($i = 1; $i <= $five; $i++) {
                $rating_html .= '<i id="codebasket_rating_star" star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star-o"></i>';
                $number++;
            }
            return $rating_html;
        }

    }

    if (!in_array("rating_stars2", $user_defined_funcs)) {

        function rating_stars2($rating, $show_bigger = 'false', $data = false) {
            $show_half = false;
            $rating_html = '';
            $five = 5;
            $bigger_icon = '';
            $number = 1;
            if ($show_bigger == 'true')
                $bigger_icon = ' fa-2x';
            if (strpos($rating, '.')) {
                $temp = explode('.', $rating);
                $temp = $temp[1];
                $temp = substr($temp, 0, 1);
                if ($temp > 5) {
                    $rating = ceil($rating);
                }
                if ($temp < 5) {
                    $rating = floor($rating);
                }
                if ($temp == 5) {
                    $show_half = true;
                    $rating = floor($rating);
                }
            }
            for ($i = 0; $i < $rating; $i++) {
                $rating_html .= '<i star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star"></i>';
                $five--;
                $number++;
            }
            if ($show_half == true) {
                $rating_html .= '<i star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star-half-o"></i>';
                $five--;
                $number++;
            }
            for ($i = 1; $i <= $five; $i++) {
                $rating_html .= '<i star-number="' . $number . '" class="fa' . $bigger_icon . ' fa-star-o"></i>';
                $number++;
            }
            $string_rating = "";
            if (is_array($data)) {
                $string_rating = "<br><small>" . $data['avg_rating'] . "/5 (" . $data['tot_ratings'] . " votes)</small>";
            }
            return $rating_html . $string_rating;
        }

    }

    if (!in_array("getcomments", $user_defined_funcs)) {

        function getcomments($module_type, $module_id, $database) {
            $getcomment_data = $database->select("comments", "*", array("AND" => array("status" => 1, "module_type" => $module_type, "module_id" => $module_id), 'ORDER' => 'id DESC'));
            return $getcomment_data;
        }

    }

    if (!in_array("get_comments", $user_defined_funcs)) {

        function get_comments($database, $module_type, $module_id, $options/* LIMIT, ORDER */ = NULL) {
            $comments = $database->select('comments', '*', array('AND' => array('status' => 1, 'module_type' => $module_type, 'module_id' => $module_id), 'ORDER' => 'id DESC'));
            if (isset($options['LIMIT']) && $options['LIMIT']) {
                $comments = $database->select('comments', '*', array('AND' => array('status' => 1, 'module_type' => $module_type, 'module_id' => $module_id), 'ORDER' => 'id DESC', 'LIMIT' => $options['LIMIT']));
            }
            if (isset($options['ORDER']) && $options['ORDER'] == 'ASC') {
                $comments = $database->select('comments', '*', array('AND' => array('status' => 1, 'module_type' => $module_type, 'module_id' => $module_id), 'ORDER' => 'id ASC'));
                if (isset($options['LIMIT']) && $options['LIMIT']) {
                    $comments = $database->select('comments', '*', array('AND' => array('status' => 1, 'module_type' => $module_type, 'module_id' => $module_id), 'ORDER' => 'id ASC', 'LIMIT' => $options['LIMIT']));
                }
            }
            return $comments;
        }

    }

    if (!in_array("commentbox", $user_defined_funcs)) {

        function commentbox($comment, $parent_comment_id, $user_id, $module_type, $module_id, $ip, $status, $database) {
            $result = $database->insert('comments', array(
                'comment' => $comment,
                'parent_comment_id' => $parent_comment_id,
                'user_id' => $user_id,
                'module_type' => $module_type,
                'module_id' => $module_id,
                'ip' => $ip,
                'status' => $status,
            ));

            return $result;
        }

    }

    if (!in_array("get_rating", $user_defined_funcs)) {

        function get_rating($module_type, $module_id, $database) {
            return $database->select("ratings", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id)));
        }

    }

    if (!in_array("count_rating", $user_defined_funcs)) {

        function count_rating($module_type, $module_id, $database) {
            return $database->sum("ratings", "value", array("AND" => array("module_type" => $module_type, "module_id" => $module_id)));
        }

    }

    if (!in_array("avg_rating", $user_defined_funcs)) {

        function avg_rating($module_type, $module_id, $database) {
            $get_rating = get_rating($module_type, $module_id, $database);
            
            if($get_rating){
            
            $count_rating = count_rating($module_type, $module_id, $database);
            $tot_get_rating = count($get_rating);
            $avg_rating = round(($count_rating / $tot_get_rating), 1);
            $avg_rating = $avg_rating ? $avg_rating : 0;
            return $avg_rating;
            }else{
                return 0;
            }
        }

    }

    if (!in_array("tot_ratings", $user_defined_funcs)) {

        function tot_ratings($module_type, $module_id, $database) {
            return count(get_rating($module_type, $module_id, $database));
        }

    }

    if (!in_array("get_total_likes", $user_defined_funcs)) {

        function get_total_likes($module_type, $module_id, $database) {
            return $database->count("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "liked" => '1')));
        }

    }

    if (!in_array("get_total_dislikes", $user_defined_funcs)) {

        function get_total_dislikes($module_type, $module_id, $database) {
            return $database->count("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "dislike" => '1')));
        }

    }

    if (!in_array("check_if_voted", $user_defined_funcs)) {

        function check_if_voted($module_type, $module_id, $database, $user_id, $ip) {
            if ($user_id) {
                $result = $database->select("ratings", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id)));
            } elseif ($ip) {
                $result = $database->select("ratings", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip)));
            } else {
                $result = 0;
            }

            return $result;
        }

    }

    if (!in_array("check_if_liked_or_disliked", $user_defined_funcs)) {

        function check_if_liked_or_disliked($module_type, $module_id, $database, $user_id, $ip) {
            if ($user_id) {
                $result = $database->get("like_dislike", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id)));
            } elseif ($ip) {
                $result = $database->get("like_dislike", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip,"user_id"=>null)));
            } else {
                $result = 0;
            }
            return $result;
        }

    }

    if (!in_array("check_if_saved", $user_defined_funcs)) {

        function check_if_saved($module_type, $module_id, $database, $user_id, $ip) {
            if ($user_id) {
                $result = $database->select("saves", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id)));
            } elseif ($ip) {
                $result = $database->select("saves", "*", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip)));
            } else {
                $result = 0;
            }

            return $result;
        }

    }

    if (!in_array("get_most_rated", $user_defined_funcs)) {//to be completed

        function get_most_rated($database, $options/* LIMIT */ = NULL) {
            //initialize return array
            $return_array = array();
            //get all ratings
            $get_all_ratings = $database->select('ratings', '*');
            foreach ($get_all_ratings as $rating) {
                $module_data[$rating['module_type']][] = $rating['module_id'];
            }
            //$return_array['array'] = $get_all_ratings;
            return $return_array;
        }

    }

    if (!in_array("get_most", $user_defined_funcs)) {

        function get_most($database, $type, $max_elements = NULL) {
            if ($type == "rated")
                $table = "ratings";
            if ($type == "liked")
                $table = "like_dislike";

            $most = array();
            $get_all = $database->select($table, "*");
            foreach ($get_all as $all) {
                $all_module_data[$all['module_type']][] = $all['module_id'];
            }
            foreach ($all_module_data as $module_type => $module_data) {
                foreach ($module_data as $module_id) {
                    if (!key_exists($module_id, $most[$module_type])) {
                        $most[$module_type][$module_id] = 1;
                    } else {
                        $most[$module_type][$module_id]++;
                    }
                }
                arsort($most[$module_type]);
                if (is_numeric($max_elements) && $max_elements < count($most[$module_type])) {
                    $new_array = array();
                    foreach ($most[$module_type] as $module_id => $count) {
                        if ($max_elements) {
                            $new_array[$module_id] = $count;
                        }
                        $max_elements--;
                    }
                    $most[$module_type] = $new_array;
                }
            }
            return $most;
        }

    }

    if (!in_array("get_top_rated", $user_defined_funcs)) {

        function get_top_rated($database, $options = null) {
            $get_top_rated = array();
            $start = null;
            $limit = null;
            if (is_numeric($options) && $options != 0) {

            }
            if (isset($options['LIMIT'])) {
                if (is_numeric($options['LIMIT']) && $options['LIMIT'] != 0) {
                    $limit = $options['LIMIT'];
                } else if (is_array($options['LIMIT'])) {
                    if (is_numeric($options['LIMIT'][0])) {
                        $start = $options['LIMIT'][0];
                    }
                    if (is_numeric($options['LIMIT'][1]) && $options['LIMIT'][1] != 0) {
                        $limit = $options['LIMIT'][1];
                    }
                }
            }
            $get_all = $database->select("ratings", "*", array("ORDER" => "id DESC"));
            if (!empty($get_all)) {
                foreach ($get_all as $all) {
                    $avg_rating = avg_rating($all['module_type'], $all['module_id'], $database);
                    $all_module_data[$all['module_type']][$all['module_id']] = $avg_rating;
                }
                //pr($all_module_data);
            }

            //pr($all_module_data);
            
            if(isset($all_module_data) && $all_module_data){
            foreach ($all_module_data as $module_type => $v) {
                arsort($all_module_data[$module_type]);
                //pr($all_module_data[$module_type]);
                $skipped = array();
                $modules = array();
                foreach ($v as $module_id => $rating) {
                    if ($start) {
                        if (count($skipped) < $start) {
                            $skipped[] = $module_id;
                        } else {
                            if ($limit) {
                                if (count($modules) < $limit) {
                                    $modules[] = $module_id;
                                }
                            } else {
                                $modules[] = $module_id;
                            }
                        }
                    } else {
                        if ($limit) {
                            if (count($modules) < $limit) {
                                $modules[] = $module_id;
                            }
                        } else {
                            $modules[] = $module_id;
                        }
                    }
                    //ksort($modules);
                    $get_top_rated[$module_type] = $modules;
                }
            }
            }
            

            //pr($all_module_data);

            if (isset($options['MODULE'])) {
                $new_rated = array();
                if (!is_array($options['MODULE'])) {
                    $required_modules = explode(",", $options['MODULE']);
                } else {
                    $required_modules = $options['MODULE'];
                }
                foreach ($get_top_rated as $module_type => $v) {
                    if (in_array($module_type, $required_modules)) {
                        $new_rated[$module_type] = $v;
                    }
                }
                $get_top_rated = $new_rated;
            }
            return $get_top_rated;
        }

    }
}

function get_user_image($database, $user_id) {
    $user_image = "http://placehold.it/53x50&text=No+Image";
    $get_user = $database->get("users", '*', array('id' => $user_id));
    if (!empty($get_user)) {
        if ($get_user['picture']) {
            $user_image = main_url . $get_user['picture'];
            if(!file_exists($user_image)){
                $user_image = main_url."/uploads/user_pics/".$get_user['picture'];
            }
        }
        if ($get_user['fb_id']) {
            $user_image = "http://graph.facebook.com/" . $get_user['fb_id'] . "/picture";
        }
    }
    return $user_image;
}

function get_user_name($database, $user_id) {
    $user_name = NULL;
    $get_user = $database->get("users", '*', array('id' => $user_id));
    if (!empty($get_user)) {
        if ($get_user['fullname']) {
            $user_name = $get_user['fullname'];
        }
    }
    return $user_name;
}

function magic_fill($database, $main_table, $secondary_table, $multi_field, $valuestofill = 5) {
    $main_prim_key = $database->getPKID($main_table);
    $sec_prim_key = $database->getPKID($secondary_table);

    $get_main = $database->select($main_table, $main_prim_key);
    $get_sec = $database->select($secondary_table, $sec_prim_key);
    $database->query("TRUNCATE TABLE " . $main_table . "_" . $secondary_table);

    foreach ($get_sec as $get_sec_id) {
        for ($i = 0; $i < $valuestofill; $i++) {
            $get_main_id = array_rand($get_main, 1);
            $database->insert($main_table . "_" . $secondary_table, array($main_table . "_id" => $get_main_id, $secondary_table . "_id" => $get_sec_id));
        }
    }
    $database->delete($main_table . "_" . $secondary_table, array('OR' => array($main_table . "_id" => 0, $secondary_table . "_id" => 0)));
}

function magic_update($database, $main_table, $secondary_table, $multi_field) {
    $main_prim_key = $database->getPKID($main_table);
    $sec_prim_key = $database->getPKID($secondary_table);

    $get_main = $database->select($main_table, $main_prim_key);

    foreach ($get_main as $get_main_id) {
        $sec_arr = $database->select($main_table . "_" . $secondary_table, $secondary_table . "_id", array($main_table . "_id" => $get_main_id));
        $ser_sec_arr = serialize($sec_arr);
        $database->update($main_table, array($multi_field => $ser_sec_arr), array($main_prim_key => $get_main_id));
    }
}

function alias_name($database, $module) {
    $get_aliases = $database->select('module_alias', '*');
    if (!empty($get_aliases)) {
        foreach ($get_aliases as $alias)
            $aliases[$alias['alias_name']] = $alias['module_name'];
    }
    $aliases_flip = array_flip($aliases);
    return ($aliases_flip[$module] ? $aliases_flip[$module] : $module);
}

function save_from_serialized($database, $main_table, $secondary_table, $multiple_field) {
    $pk_main = $database->getPKID($main_table);
    $pk_sec = $database->getPKID($secondary_table);

    $database->query("TRUNCATE " . $main_table . "_" . $secondary_table);
    $get_main_data = $database->select($main_table, "*");
    if (!empty($get_main_data)) {
        foreach ($get_main_data as $main_data) {
            $pk_main_id = $main_data[$pk_main];
            $multi_data = $main_data[$multiple_field];
            $multi_ids = unserialize($multi_data);
            if (!empty($multi_ids)) {
                foreach ($multi_ids as $multi_id) {
                    $database->insert($main_table . "_" . $secondary_table, array($main_table . "_id" => $pk_main_id, $secondary_table . "_id" => $multi_id));
                }
            }
        }
    }
}

function get_next_id($database, $table, $present_id) {
    $pk = $database->getPKID($table);
    return $database->get($table, $pk, array($pk . "[>]" => $present_id, "ORDER" => $pk . " ASC"));
}

function get_prev_id($database, $table, $present_id) {
    $pk = $database->getPKID($table);
    return $database->get($table, $pk, array($pk . "[<]" => $present_id, "ORDER" => $pk . " DESC"));
}

if (isset($_POST['action'])) {
    extract($_POST);

    //get_like_information ajax
    if ($action == 'get_like_information') {//$module_id, $module_type
        $ip = getRealIp();
        $is_liked_disliked = check_if_liked_or_disliked($module_type, $module_id, $database, $user_id, $ip);
        //pr($is_liked_disliked);
        $data['total_likes'] = get_total_likes($module_type, $module_id, $database);
        $data['total_dislikes'] = get_total_dislikes($module_type, $module_id, $database);

        if (empty($is_liked_disliked)) {
            $data['user_liked'] = 0;
            $data['user_disliked'] = 0;
        } else {
            //pr($is_liked_disliked);
            if (public_can_like_dislike) {
                if ($is_liked_disliked['ip'] == $ip && $is_liked_disliked['liked'] == '1') {
                    $data['user_liked'] = 1;
                    $data['user_disliked'] = 0;
                } elseif ($is_liked_disliked['ip'] == $ip && $is_liked_disliked['dislike'] == '1') {
                    $data['user_liked'] = 0;
                    $data['user_disliked'] = 1;
                } else {
                    $data['user_liked'] = 0;
                    $data['user_disliked'] = 0;
                }
            } else {
                if ($is_liked_disliked['user_id'] == $user_id && $user_id != 0) {
                    if ($is_liked_disliked['liked'] == '1') {
                        $data['user_liked'] = 1;
                        $data['user_disliked'] = 0;
                    } elseif ($is_liked_disliked['dislike'] == '1') {
                        $data['user_liked'] = 0;
                        $data['user_disliked'] = 1;
                    } else {
                        $data['user_liked'] = 0;
                        $data['user_disliked'] = 0;
                    }
                } else {
                    $data['user_liked'] = 0;
                    $data['user_disliked'] = 0;
                }
            }
        }
        echo json_encode($data);
    }
    //get_like_information ajax ends
    //like_module ajax
    if ($action == 'like_module') {
        //initialize return variable
        $data = array();
        //get user ip
        $ip = getRealIp();

        //check public liking
        if (public_can_like_dislike) {
            //if public liking is enabled
            //check if already liked
            
           if($user_id){
                 $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "liked" => '1',"user_id"=>$user_id)));
           }else{
                 $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "liked" => '1',"user_id"=>null)));
           }
          
            
            
            
            
            if ($chk) {
                //user had already liked the module
                //return responses
                $data['status'] = "already liked";
            } else {
                //if user has not liked the module
                //check if user disliked the module
                
                if($user_id){
                    $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "dislike" => '1',"user_id"=>$user_id)));
                }else{
                   $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "dislike" => '1',"user_id"=>null))); 
                }
                
                
                
                
                if ($chk) {
                    //user had disliked the module
                    //change status of module to liked
                    if($user_id){
                      $database->update("like_dislike", array("liked" => '1', "dislike" => '0'), array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip,"user_id"=>$user_id)));
                    }else{
                        $database->update("like_dislike", array("liked" => '1', "dislike" => '0'), array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip,"user_id"=>null)));
                    }
                    
                    
                } else {
                    //user has also not disliked the module
                    //like the module
                   
                    $data = array();
                    $data['user_id'] = $user_id;
                    $data['module_type'] = $module_type;
                    $data['module_id'] = $module_id;
                    $data['liked'] = '1';
                    $data['ip'] = $ip;
                    
                    if(!$user_id){
                       unset($data['user_id']);
                    }
                      
                    
                    
              
                    $database->insert("like_dislike", $data);
                  
                }
                //return responses
                $data['status'] = "success";
            }
        } else {
            //if public liking is disabled
            //check if user logged in
            if ($user_id != null) {
                //if user is logged in
                //check if already liked
                $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id, "liked" => '1')));
                if ($chk) {
                    //if already liked
                    $data['status'] = "already liked";
                } else {
                    //if not already liked
                    //check if already disliked
                    $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id, "dislike" => '1')));
                    if ($chk) {
                        //if already disliked
                        
                        //change status to liked
                        
                        $database->update("like_dislike", array("liked" => '1', "dislike" => '0'), array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id)));
                        
                        
                        
                    } else {
                        //if not already disliked
                        //like module
                        
                        $data = array();
                        $data['user_id'] = $user_id;
                        $data['module_type'] = $module_type;
                        $data['module_id'] = $module_id;
                        $data['liked'] = '1';
                        $data['ip'] = $ip;

                        if(!$user_id){
                           unset($data['user_id']);
                        }
                        
                    $database->insert("like_dislike", $data);
                         
//                        $database->insert("like_dislike", array("user_id" => $user_id, "module_type" => $module_type, "module_id" => $module_id, "liked" => '1', "ip" => $ip));
                    }
                    $data['status'] = 'success';
                }
            } else {
                //if user is not logged in
                $data["status"] = "no login";
            }
        }
        if ($data['status'] == 'success') {
            $data['total_likes'] = get_total_likes($module_type, $module_id, $database);
            $data['total_dislikes'] = get_total_dislikes($module_type, $module_id, $database);
        }
        echo json_encode($data);
    }
    //like_module ajax ends
    //dislike_module ajax
    if ($action == 'dislike_module') {
        //initialize return variable
        $data = array();
        //get user ip
        $ip = getRealIp();

        //check public liking
        if (public_can_like_dislike) {
            //if public liking is enabled
            //check if already disliked
            if($user_id){
                 $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "dislike" => '1',"user_id"=>$user_id)));
            
            }else{
                 $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "dislike" => '1',"user_id"=>null)));
            
            }
           
            
            if ($chk) {
                //user had already liked the module
                //return responses
                $data['status'] = "already disliked";
            } else {
                //if user has not disliked the module
                //check if user liked the module
                
                if($user_id){
                $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "liked" => '1',"user_id"=>$user_id)));
            
            }else{
                $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip, "liked" => '1',"user_id"=>null)));
            
            }
                
                if ($chk) {
                    //user had liked the module
                    //change status of module to disliked
                    
                    $database->update("like_dislike", array("liked" => '0', "dislike" => '1'), array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "ip" => $ip)));
                } else {
                    //user has also not disliked the module
                    //dislike the module
                      
                        $data = array();
                        $data['user_id'] = $user_id;
                        $data['module_type'] = $module_type;
                        $data['module_id'] = $module_id;
                        $data['dislike'] = '1';
                        $data['ip'] = $ip;

                        if(!$user_id){
                           unset($data['user_id']);
                        }
                        
                    $database->insert("like_dislike", $data);
 
                }
                //return responses
                $data['status'] = "success";
            }
        } else {
            //if public liking is disabled
            //check if user logged in
            if ($user_id != null) {
                //if user is logged in
                //check if already disliked
                $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id, "dislike" => '1')));
                if ($chk) {
                    //if already disliked
                    $data['status'] = "already disliked";
                } else {
                    //if not already disliked
                    //check if already liked
                    $chk = $database->has("like_dislike", array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id, "liked" => '1')));
                    if ($chk) {
                        //if already liked
                        //change status to disliked
                        $database->update("like_dislike", array("liked" => '0', "dislike" => '1'), array("AND" => array("module_type" => $module_type, "module_id" => $module_id, "user_id" => $user_id)));
                    } else {
                        //if not already liked
                        //dislike module
                        $database->insert("like_dislike", array("user_id" => $user_id, "module_type" => $module_type, "module_id" => $module_id, "dislike" => '1', "ip" => $ip));
                    }
                    $data['status'] = 'success';
                }
            } else {
                //if user is not logged in
                $data["status"] = "no login";
            }
        }
        if ($data['status'] == 'success') {
            $data['total_likes'] = get_total_likes($module_type, $module_id, $database);
            $data['total_dislikes'] = get_total_dislikes($module_type, $module_id, $database);
        }
        echo json_encode($data);
    }
    //dislike_module ajax ends
    //get_rating_information ajax
    if ($action == 'get_rating_information') {//module_type, module_id, show_bigger
        //initialise return data
        $data = array();

        //get average rating for current module
        $data['avg_rating'] = avg_rating($module_type, $module_id, $database);
        //get total ratings for current module
        $data['tot_ratings'] = tot_ratings($module_type, $module_id, $database);
        //get ratings html for module
        $data['ratings_html'] = get_ratings_html($module_type, $module_id, $database, $show_bigger);
        //return all data as json
        echo json_encode($data);
    }
    //get_rating_information ajax ends
    //save_rating ajax
    if ($action == "save_rating") {//module_type, module_id, value
        //initiate data array and return array
        $data = array();
        $ret = array();
        
        //save data received in request
        $data['module_type'] = $module_type;
        $data['module_id'] = $module_id;
        $data['value'] = $value;
        $data['ip'] = getRealIp();
        $data['user_id'] = $user_id;
        
       
        
        
        //check if public rating is enabled
        if (public_can_rate == 1) {
            //if public rating is enabled
            //check if rating exists
            
            if($user_id){
                 $chk = $database->has('ratings', array('AND' => array('user_id'=>$user_id,'ip' => $data['ip'], 'module_type' => $module_type, 'module_id' => $module_id)));
            }else{
                 $chk = $database->has('ratings', array('AND' => array('ip' => $data['ip'], 'module_type' => $module_type, 'module_id' => $module_id,'user_id'=> null)));
            }
           
             
            if ($chk) {
                //if rating exits for present module
                $ret['status'] = 'already rated';
            } else {
                //if rating does not exists
                //save rating for present module
                
                if(!$data['user_id']){
                  unset($data['user_id']);
                }
                
                $database->insert('ratings', $data);
                
                
                $ret['status'] = 'success';
            }
        } else {
            //if public liking is disabled
            //check if user logged in
            if ($user_id != 0) {
                //user is logged in
                //check if rating exists for present user
                $chk = $database->has('ratings', array('AND' => array('user_id' => $data['user_id'], 'module_type' => $module_type, 'module_id' => $module_id)));
                if ($chk) {
                    //if rating exists for present user
                    $ret['status'] = 'already rated';
                } else {
                    //if present user rating does not exists
                    //save rating for present module
                    $database->insert('ratings', $data);
                    $ret['status'] = 'success';
                }
            } else {
                //if there is no logged in user
                $ret['status'] = 'no login';
            }
        }
        if ($ret['status'] == 'success') {
            $ret['avg_rating'] = avg_rating($module_type, $module_id, $database);
            $ret['num_ratings'] = count(get_rating($module_type, $module_id, $database));
            $ret['string'] = $ret['avg_rating'] . "/5 (" . $ret['num_ratings'] . " " . (($ret['num_ratings'] == 1) ? "vote" : "votes") . ")";
        }
        echo json_encode($ret);
    }
    //save_rating ajax ends
    //get_comments_information ajax starts
    if ($action == 'get_comments_information') {//module_type, module_id, limit
        //initialize return array
        $return_data = array();
        //set default user status as not logged
        $return_data['user_status'] = 0;
        //check if user logs in
        if ($user_id != 0) {
            $return_data['user_status'] = 1;
        }
        //get comments for current post
        $get_comments = get_comments($database, $module_type, $module_id, array('ORDER' => "id DESC"));
        $return_data['total_comments'] = count($get_comments);
        if (!empty($limit)) {
            $get_comments = get_comments($database, $module_type, $module_id, array('ORDER' => "id DESC", 'LIMIT' => $limit));
        }
        //get pictures of users if any
        if (!empty($get_comments)) {
            $user_images = array();
            $user_names = array();
            $comment_dates = array();
            foreach ($get_comments as $comment) {
                $id = $comment['id'];
                $user_id = $comment['user_id'];
                if (!array_key_exists($user_id, $user_images)) {
                    $user_images[$user_id] = get_user_image($database, $user_id);
                }
                if (!array_key_exists($user_id, $user_names)) {
                    $user_name = get_user_name($database, $user_id);
                    if ($user_name) {
                        $user_names[$user_id] = $user_name;
                    } elseif ($comment['ip']) {
                        $user_names[$user_id] = "(IP: " . $comment['ip'] . ")";
                    } else {
                        $user_names[$user_id] = "Anonymous User";
                    }
                }
                if (!array_key_exists($id, $comment_dates)) {
                    $comment_dates[$id] = date_format(date_create($comment['created']), 'd M Y');
                    $comment_times[$id] = date_format(date_create($comment['created']), 'H:i A');
                }
            }
            $return_data['user_images'] = $user_images;
            $return_data['user_names'] = $user_names;
            $return_data['comment_dates'] = $comment_dates;
            $return_data['comment_times'] = $comment_times;
        }

        $return_data['comments'] = $get_comments;
        echo json_encode($return_data);
    }
    //get_comments_information ajax ends
    //post_comment ajax starts
    if ($action == 'post_comment') {
        //pr($_POST);
        //initialise return data
        $return_data = array();
        //check if user logs in
        if ($user_id != 0) {
            //if user is logged in
            //check if message is received
            if ($comment) {
                //message is received
                $ins = $database->insert("comments", array("comment" => clean($comment), "user_id" => $user_id, "module_type" => $module_type, "module_id" => $module_id, "ip" => getRealIp()));
                if ($ins) {
                    $return_data['status'] = "success";
                } else {
                    $return_data['status'] = "unable to post comment";
                }
            } else {
                //message is not received
                $return_data['status'] = "no message";
            }
        } else {
            //if user is not logged in
            $return_data['status'] = "no login";
        }
        echo $return_data['status'];
    }
    //post_comment ajax starts
}

