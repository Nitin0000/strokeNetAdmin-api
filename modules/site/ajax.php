<?php 
include(getcwd() . "/modules/site/common.php");
//header('Content-Type: application/json');
    if(isset($params[1]) && $params[1] && $params[1] == "check_for_planned_posts"){
            
        $current_date = date("Y-m-d");  
        $current_time = date("h:i A");  
        
        $get_planned_postings = $database->select("planned_postings",array("id", "publish_date", "publish_time"), array("AND"=>array("publish_date" =>  $current_date, "publish_time" =>  $current_time)));
        
        if(count($get_planned_postings) > 0){
            echo "<h1>".$current_date." ".$current_time."</h1>";
            
            foreach($get_planned_postings as $key_planned=>$val_planned){
                $postId = $val_planned['id'];
                $post_data = array();
                $get_post = $database->get("planned_postings", "*", array("id"=>$postId));
                if($get_post['post_type'] == "video"){
                    $post_data['video_title'] = $get_post['title'];
                    $post_data['video_slug'] = $get_post['slug'];
                    $post_data['tags'] = $get_post['tags'];
                    $post_data['cover_image'] = $get_post['cover_image'];
                    $post_data['video_type'] = $get_post['video_type'];
                    $post_data['video_id'] = $get_post['video_id'];
                    $post_data['video_time'] = $get_post['video_time'];
                    $post_data['short_description'] = $get_post['short_description'];
                    $post_data['description'] = $get_post['news_content'];
                    $post_data['added_by'] = $get_post['added_by'];
                    $post_data['status'] = '1';


                    $insert_video = $database->insert("videos", $post_data);

                    $unserialized_data = unserialize($post_data['tags']);
                    foreach($unserialized_data as $id){
                        $data = array();
                        $data['videos_id'] = $insert_video;
                        $data['tags_id'] = $id;
                        $insert_news_tags = $database->insert("videos_tags", $data);
                    }

                    $delete_postings_tags =  $database->delete("planned_postings_tags", array("planned_postings_id" => $postId));
                    $delete_postings =  $database->delete("planned_postings", array("id" => $postId));

                    echo "1";
                }else{
                    $post_data['news_title'] = $get_post['title'];
                    $post_data['news_slug'] = $get_post['slug'];
                    $post_data['tags'] = $get_post['tags'];
                    $post_data['cover_image'] = $get_post['cover_image'];
                    $post_data['layout_style'] = $get_post['layout_style'];
                    $post_data['short_description'] = $get_post['short_description'];
                    $post_data['news_content'] = $get_post['news_content'];
                    $post_data['added_by'] = $get_post['added_by'];
                    $post_data['status'] = '1';

                    $insert_news = $database->insert("news", $post_data);

                    $unserialized_data = unserialize($post_data['tags']);

                    foreach($unserialized_data as $id){
                        $data = array();
                        $data['news_id'] = $insert_news;
                        $data['tags_id'] = $id;
                        $insert_videos_tags = $database->insert("news_tags", $data);
                    }

                    $delete_postings_tags =  $database->delete("planned_postings_tags", array("planned_postings_id" => $postId));
                    $delete_postings =  $database->delete("planned_postings", array("id" => $postId));
                    
                    echo "<li>Post: ".$postId." Published</li>";
                }


            }

        }else{
            echo "<h1>".$current_date." ".$current_time."</h1>";
            echo "no posts to post!";
        }
        
        
        
     }else{
        echo json_encode("Direct Access Not Allowed");
        exit;
    }