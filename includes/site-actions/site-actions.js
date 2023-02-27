/*
 * jquery is required to be included before
 */
$(document).ready(function(){

    function disable(e){e.prop('disabled',true);}

    function enable(e){e.prop('disabled',false);}

    function cl(e){console.log(e);}

    function error(e){console.log(e);}

    //codebasket like dislike js
    $('[data-id="codebasket_like_dislike"]').each(function(){
        //get required data
        var module_type = $(this).attr("data-module_type");
        var module_id = $(this).attr("data-module_id");
        var main_url = $(this).attr("data-main_url");

        //get optional data
        var show_text = $(this).attr('data-show_text');
        var button_size = $(this).attr('data-button_size');
        var enable_working = $(this).attr("data-enable");
        var style = $(this).attr('data-style');
        
        //check if required data received
        if(module_type && module_id && main_url){
            //required data received

            var like_dislike_div = $('[data-id="codebasket_like_dislike"][data-module_type="'+module_type+'"][data-module_id="'+module_id+'"]');
            //show please wait
            like_dislike_div.html("<small>Loading Buttons...</small>");
            $.post(main_url+'/common', {action : 'get_like_information', module_type : module_type, module_id : module_id}, function(data){
                data = $.parseJSON(data);
                //make like_num_span
                var like_num_span = ' (<span data-id="codebasket_num_likes">'+data['total_likes']+'</span>)';

                //make dislike_num_span
                var dislike_num_span = ' (<span data-id="codebasket_num_dislikes">'+data['total_dislikes']+'</span>)';
                //check for button size
                var button_class = "btn-xs";
                var or_class = "or-xs";
                if($.isNumeric(button_size) && button_size<=4 && button_size != '0'){
                    if(button_size == '1'){
                        button_class="btn-xs ";
                        or_class = "or-xs";
                    }
                    if(button_size == '2'){
                        button_class="btn-sm ";
                        or_class = "or-sm";
                    }
                    if(button_size == '3'){
                        button_class="";
                        or_class = "";
                    }
                    if(button_size == '4'){
                        button_class="btn-lg ";
                        or_class = "or-lg";
                    }
                }
                var like_text = '';
                var dislike_text = '';
                //if show text is enable???
                if(show_text === 'true'){
                    like_text = " Like";
                    dislike_text = " Dislike";
                }
                
                var style_html = '<button data-id="codebasket_like" class="btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                if($.isNumeric(style) && style != '0'){
                    if(style == '2'){
                        style_html='<style>.btn-outlined{border-radius:0;-webkit-transition:all 0.3s;-moz-transition: all 0.3s;transition: all 0.3s;}.btn-outlined.btn-primary{background: none;border: 3px solid #428bca;color: #428bca;}.btn-outlined.btn-primary:hover,.btn-outlined.btn-primary:active {color: #FFF;background: #428bca;border-color: #428bca:}.btn-outlined.btn-success {background: none;border: 3px solid #5cb85c;color: #5cb85c;}.btn-outlined.btn-success:hover,.btn-outlined.btn-success:active {color: #FFF;background: #47a447;}.btn-outlined.btn-info {background: none;border: 3px solid #5bc0de;color: #5bc0de;}.btn-outlined.btn-info:hover,.btn-outlined.btn-info:active {color: #FFF;background: #39b3d7;}.btn-outlined.btn-warning {background: none;border: 3px solid #f0ad4e;color: #f0ad4e;}.btn-outlined.btn-warning:hover,.btn-outlined.btn-warning:active {color: #FFF;background: #ed9c28;}.btn-outlined.btn-danger {background: none;border: 3px solid #d9534f;color: #d9534f;}.btn-outlined.btn-danger:hover,.btn-outlined.btn-danger:active {color: #FFF;background: #d2322d;}</style><button data-id="codebasket_like" class="btn-outlined btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn-outlined btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '3'){
                        style_html='<style>.btn-outline{background-color:transparent;color:inherit;transition: all .5s}button[disabled].btn-outline{color:#fff}.btn-primary.btn-outline{color:#428bca}.btn-success.btn-outline{color:#5cb85c}.btn-info.btn-outline{color:#5bc0de}.btn-warning.btn-outline{color:#f0ad4e}.btn-danger.btn-outline{color:#d9534f}.btn-primary.btn-outline:hover,.btn-success.btn-outline:hover,.btn-info.btn-outline:hover,.btn-warning.btn-outline:hover,.btn-danger.btn-outline:hover{color:#fff}</style><button data-id="codebasket_like" class="btn-outline btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn-outline btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '4'){
                        style_html = '<style>.btn3d{-webkit-box-shadow:0px 3px 0px rgba(0, 0, 0, 0.3);-moz-box-shadow:0px 3px 0px rgba(0, 0, 0, 0.3);box-shadow:0px 3px 0px rgba(0, 0, 0, 0.3);}.btn3d:active{margin-top:3px;margin-bottom:-3px;}</style><button data-id="codebasket_like" class="btn3d btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn3d btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '5'){
                        style_html = '<style>.btn3d {transition:all .08s linear;position:relative;outline:medium none;-moz-outline-style:none;border:0px;margin-right:10px;margin-top:15px;}.btn3d:focus {outline:medium none;-moz-outline-style:none;}.btn3d:active {top:9px;}.btn-success.btn3d{box-shadow:0 0 0 1px #5cb85c inset, 0 0 0 2px rgba(255,255,255,0.15) inset, 0 8px 0 0 #4cae4c, 0 8px 0 1px rgba(0,0,0,0.4), 0 8px 8px 1px rgba(0,0,0,0.5);background-color:#5cb85c;}.btn-danger.btn3d{box-shadow:0 0 0 1px #c63702 inset, 0 0 0 2px rgba(255,255,255,0.15) inset, 0 8px 0 0 #C24032, 0 8px 0 1px rgba(0,0,0,0.4), 0 8px 8px 1px rgba(0,0,0,0.5);background-color:#c63702;}</style><button data-id="codebasket_like" class="btn3d btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn3d btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '6'){
                        
                        style_html = '<style>.or{position:relative;float:left;width:.3em;height:1.3em;z-index:3;font-size:12px}.or:before{position:absolute;top:50%;left:50%;content:"or";background-color:#5a5a5a;margin-top:-.1em;margin-left:-.9em;width:1.8em;height:1.8em;line-height:1.55;color:#fff;font-style:normal;font-weight:400;text-align:center;border-radius:500px;-webkit-box-shadow:0 0 0 1px rgba(0,0,0,0.1);box-shadow:0 0 0 1px rgba(0,0,0,0.1);-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box}.or:after{position:absolute;top:0;left:0;content:" ";width:.3em;height:2.84em;background-color:rgba(0,0,0,0);border-top:.6em solid #5a5a5a;border-bottom:.6em solid #5a5a5a}.or.or-lg{height:1.3em;font-size:16px}.or.or-lg:after{height:2.85em}.or.or-sm{height:1em}.or.or-sm:after{height:2.5em}.or.or-xs{height:.25em}.or.or-xs:after{height:1.84em;z-index:-1000}.btngrp{float:left;border-radius:0}.btngrp:first-child{margin-left:0;border-top-left-radius:.25em;border-bottom-left-radius:.25em;padding-right:15px}.btngrp:last-child{border-top-right-radius:.25em;border-bottom-right-radius:.25em;padding-left:15px}</style><button data-id="codebasket_like" class="btngrp btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button><div class="or '+or_class+'"></div><button data-id="codebasket_dislike" class="btngrp btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '7'){
                        style_html = '<style>.btn-circle{border-radius:50%}</style><button data-id="codebasket_like" class="btn-circle btn btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn-circle btn btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                    if(style == '8'){
                        style_html = '<style>.btn-cool{background-color:#fff;border:#bdc3c7 solid 1px;color:#333;cursor:pointer;font-size:.875em;display:inline-block;margin-bottom:0;outline:0;text-decoration:none;white-space:nowrap;text-transform:uppercase;text-align:center;text-shadow:none;background-image:none;font-family:"Helvetica Neue",helvetica,arial,verdana,sans-serif;font-weight:700;-moz-border-radius:5px;-ms-border-radius:5px;border-radius:5px;-moz-box-shadow:none;-webkit-box-shadow:none;box-shadow:none;-moz-transition:background .1s linear;-webkit-transition:background .1s linear;transition:background .1s linear}.btn-cool:hover,.btn-cool:active{background:#ecf0f1;color:#333;text-decoration:none;-moz-transition:background .1s linear;-webkit-transition:background .1s linear;transition:background .1s linear}.btn-cool.btn-success{border:1px solid #2ecc71;color:#000}.btn-cool.btn-danger{border:1px solid #e74c3c;color:#000}.btn-cool.btn-danger:hover,.btn-danger:active{border:1px solid #c0392b;color:#fff;background:#c0392b}</style><button data-id="codebasket_like" class="btn-cool btn-success '+button_class+'"><i class="fa fa-thumbs-up"></i>'+like_text+like_num_span+'</button> <button data-id="codebasket_dislike" class="btn-cool btn-danger '+button_class+'"><i class="fa fa-thumbs-down"></i>'+dislike_text+dislike_num_span+'</button>';
                    }
                }
                like_dislike_div.html(style_html);
                var like_button = like_dislike_div.find('button[data-id="codebasket_like"]');
                var dislike_button = like_dislike_div.find('button[data-id="codebasket_dislike"]');
                if(enable_working === 'true'){
                    if(data['user_liked'] == '1'){
                        disable(like_button);
                    }
                    if(data['user_disliked'] == '1'){
                        disable(dislike_button);
                    }
                    like_button.on('click', function(){
                        //disable current button
                        disable(like_button);
                        //attempt to like that module
                        $.post(main_url+'/common',{action : 'like_module', module_type : module_type, module_id : module_id},function(data){
                            //convert json to array
                            data = $.parseJSON(data);
                            //check if like is successfull
                            if(data['status'] == 'success'){
                                //if like success
                                enable(dislike_button);
                                like_button.find('span[data-id="codebasket_num_likes"]').text(data['total_likes']);
                                dislike_button.find('span[data-id="codebasket_num_dislikes"]').text(data['total_dislikes']);
                            }
                            if(data['status'] == 'no login'){
                                enable(like_button);
                                alert("Public Liking is disabled temporarily\nPlease Login to Like...");
                            }
                            if(data['status'] == 'already liked'){
                                enable(dislike_button);
                                alert("You had already liked this...");
                            }
                        });
                    });
                    dislike_button.on('click', function(){
                        //disable current button
                        disable(dislike_button);
                        //attempt to dislike that module
                        $.post(main_url+'/common',{action : 'dislike_module', module_type : module_type, module_id : module_id},function(data){
                            //convert json to array
                            data = $.parseJSON(data);
                            //check if dislike is successfull
                            if(data['status'] == 'success'){
                                //if dislike success
                                enable(like_button);
                                like_button.find('span[data-id="codebasket_num_likes"]').text(data['total_likes']);
                                dislike_button.find('span[data-id="codebasket_num_dislikes"]').text(data['total_dislikes']);
                            }
                            if(data['status'] == 'no login'){
                                enable(dislike_button);
                                alert("Public Disliking is disabled temporarily\nPlease Login to Dislike...");
                            }
                            if(data['status'] == 'already disliked'){
                                enable(like_button);
                                alert("You had already disliked this...");
                            }
                        });
                    });
                }
            });
        } else {
            $(this).html('<div class="alert-danger text-success" style="padding:10px;border:solid 1px #b94a48;border-radius:5px;font-size:12px">Error: These arguments are Required<br />data-module_type, data-module_id, data-main_url</div>');
        }
    });
    //codebasket like dislike js ends
    
    //codebaket ratings js
    $('[data-id="codebasket_rating"]').each(function(){
        //get required data
        var module_type = $(this).attr("data-module_type");
        var module_id = $(this).attr("data-module_id");
        var main_url = $(this).attr("data-main_url");

        //get optional data
        var show_bigger = $(this).attr("data-bigger_stars");
        var enable_rating = $(this).attr("data-enable");
        var show_text = $(this).attr('data-show_text');
        var text_size = $(this).attr('data-text_size');

        //check if required data received
        if(module_type && module_id && main_url){
            //required data received
            //show please wait message
            $(this).html("<small>Loading Ratings...</small>");
            //fetch ratings design and data
            $.post(main_url+'/common', {action : 'get_rating_information', module_type : module_type, module_id : module_id, show_bigger : show_bigger}, function(data){
                var ratings_div = $('[data-id="codebasket_rating"][data-module_type="'+module_type+'"][data-module_id="'+module_id+'"]');
                data = $.parseJSON(data);
                //save got data into variables
                var avg_rating = data['avg_rating'];
                var tot_ratings = data['tot_ratings'];
                var ratings_html = data['ratings_html'];

                //set text to show after ratings to ratings
                var ratings_text = 'votes';
                //check if total ratings is 1 ? set text to rating
                if(tot_ratings == 1)ratings_text = "vote";
                //make ratings stars html using these values
                var rating_stars_html = '<div class="rating" id="module_rating" style="margin-left:20px;color:#FC0">'+ratings_html+'<span id="rating_wait" style="margin-left:10px;color:green;font-size:12px;position:absolute;margin-top:5px" hidden><em>Thanks for your Rating.</em></span>';
                if(show_text === 'true'){
                    var font_size = 2;
                    if($.isNumeric(text_size)){
                        if(text_size<=7)font_size = text_size;
                        else if(text_size>7)font_size = 7;
                    }
                    rating_stars_html += '<br /><font size="'+font_size+'" style="color:#000" id="rating_info">'+avg_rating+'/5 ('+tot_ratings+' '+ratings_text+')</font>';
                }
                rating_stars_html += '</div>';
                //assign this html to present ratings div
                ratings_div.html(rating_stars_html).attr('data-old_rating',avg_rating);
                //check if user enables ratings
                if(enable_rating === 'true'){
                    //if user has enabled ratings
                    //make ratings stars hover fill stars
                    ratings_div.find('i#codebasket_rating_star').mouseenter(function(){
                        $(this).css('cursor','pointer');
                        var star_number = $(this).attr('star-number');
                        if(star_number){
                            fill_stars(ratings_div, star_number, show_bigger);
                        }
                    }).mouseleave(function(){
                        $(this).css('cursor','default');
                        var old_rating = ratings_div.attr('data-old_rating');
                        fill_stars(ratings_div, old_rating, show_bigger, 'f');
                    }).on('click', function(){
                        var ratings_div = $('[data-id="codebasket_rating"][data-module_type="'+module_type+'"][data-module_id="'+module_id+'"]');
                        //get the value of star which is clicked
                        var star_number = $(this).attr('star-number');
                        $.post(main_url+'/common',{action:'save_rating', value:star_number, module_type:module_type, module_id:module_id},function(data){
                            data = $.parseJSON(data);
                            if(data['status'] == 'success'){
                                var avg_rating = data['avg_rating'];
                                var num_ratings = data['num_ratings'];
                                var string = data['string'];
                                ratings_div.attr('data-old_rating',avg_rating);
                                var show_text = ratings_div.attr('data-show_text');
                                if(show_text === 'true'){
                                    ratings_div.find('#rating_info').text(string);
                                }
                                ratings_div.find('i#codebasket_rating_star').mouseleave();
                            }
                            if(data['status'] == 'already rated'){
                                alert('You had already given Rating...');
                            }
                            if(data['status'] == 'no login'){
                                alert('Public Rating is disabled temporarily\nPlease Login to Rate...');
                            }
                        });
                    });
                }
            });
            
        } else {
            //required data not received
            $(this).html('<div class="alert-danger text-success" style="padding:10px;border:solid 1px #b94a48;border-radius:5px;font-size:12px">Error: These arguments are Required<br />data-module_type, data-module_id, data-main_url</div>');
        }
    });
    // codebasket ratings js ends

    //codebasket comments js
    $('[data-id="codebasket_comments"]').each(function(){
        //get required data
        var module_type = $(this).attr("data-module_type");
        var module_id = $(this).attr("data-module_id");
        var main_url = $(this).attr("data-main_url");

        //get optional data
        var limit = $(this).attr('data-comments');
        var max = limit;
        if(limit && !$.isNumeric(limit)){limit = limit.split(',');max = limit[1];}
        //if(!$.isNumeric(comments_to_show) || comments_to_show <= 0){comments_to_show = 4;}

        //check if required information is provided
        if(module_type && module_id && main_url) {
            //if required data is provided
            //make ajax url variable for ajax calls
            var ajax_url = main_url+'/common';
            //make a variable for current div
                var comment_div = $('[data-id="codebasket_comments"][data-module_type="'+module_type+'"][data-module_id="'+module_id+'"]');
            //show please wait
            comment_div.html("Loading Comments...");
            $.post(ajax_url,{action:'get_comments_information', module_type:module_type, module_id:module_id, limit:limit},function(data){
                //cl(data);return;
                data = $.parseJSON(data);

                var comments_div_html = '<div class="input-group col-md-12" style="margin-top:20px;margin-bottom:5px">';
                if(data['user_status'] === 1){
                    comments_div_html += '<textarea data-id="codebasket_comment_textarea" rows="5" class="form-control" placeholder="Type your comment here..."></textarea><span class="input-group pull-right"><span data-id="codebasket_comments_send_wait" class="text-info" style="top:10px;position:absolute;right:58px;display:none">Sending... </span><button data-id="codebasket_comment_send_button" style="margin-top:5px" class="btn btn-success btn-lg btn-block" id="btn-chat">Submit Comment</button></span>';
                } else {
                    comments_div_html += '<div class="alert alert-danger"><strong>Please login to comment!</strong></a>';
                }  
                comments_div_html += '</div><div style="margin-top:20px;"><span style="margin-bottom:20px;" data-id="codebasket_total_comments">'+data['total_comments']+'</span> Comments</span> <hr><div data-id="codebasket_comments_list" class="qa-message-list"></div><a data-id="codebasket_comments_load_button" href="javascript:void(0);" style="display:none" class="btn btn-primary btn-xs btn-block" role="button"><span class="fa fa-refresh"></span> More</a></div><span class="label label-info pull-right" style="margin-top:5px"></div>';
                comment_div.html(comments_div_html);
                var load_button = comment_div.find('[data-id="codebasket_comments_load_button"]');
                var send_button = comment_div.find('[data-id="codebasket_comment_send_button"]');
                var waiting_div = comment_div.find('[data-id="codebasket_comments_send_wait"]');
                disable(send_button);
                var comment_textarea = comment_div.find('[data-id="codebasket_comment_textarea"]');
                comment_textarea.keyup(function(){
                    disable(send_button);
                    if(comment_textarea.val().length){
                        enable(send_button);
                    }
                });

                //send button coding
                send_button.click(function(){
                    disable(send_button);waiting_div.show(100);
                    var comment = comment_textarea.val();
                    if(comment){
                        comment_textarea.val('');
                        $.post(ajax_url, {action : 'post_comment', module_type : module_type, module_id : module_id, comment : comment}, function(data){
                            if(data === 'no login'){
                                waiting_div.hide(100);
                                alert('Please Login to Post a Comment...!');
                            }
                            if(data === 'no message'){
                                waiting_div.hide(100);
                                alert("Enter some text to post...!");
                            }
                            if(data === 'unable to post comment'){
                                waiting_div.hide(100);
                                alert("There was some error, Please try again!");
                            }
                            if(data === "success"){
                                $.post(ajax_url,{action:'get_comments_information', module_type:module_type, module_id:module_id, limit:limit},function(data){
                                    waiting_div.hide(100);
                                    data = $.parseJSON(data);
                                    comment_div.find('span[data-id="codebasket_total_comments"]').text(data['total_comments']);
                                    var comments_list = comment_div.find('[data-id="codebasket_comments_list"]');
                                    comments_list.html('');
                                    put_data_in_comments_list(comment_div, data);
                                });
                            }
                        });
                    }
                    //cl(module_type+" | "+module_id+" | ");
                });

                put_data_in_comments_list(comment_div, data);
                load_button.click(function(){
                    $(this).hide();
                    var next_comment = $(this).attr("data-next_comment");
                    limit = next_comment+","+max;
                    limit = limit.split(",");

                    $.post(ajax_url,{action:'get_comments_information', module_type:module_type, module_id:module_id, limit:limit},function(data){
                        data = $.parseJSON(data);
                        put_data_in_comments_list(comment_div, data);
                    });
                });
            });
        } else {
            //required data not received
            $(this).html('<div class="alert-danger text-success" style="padding:10px;border:solid 1px #b94a48;border-radius:5px;font-size:12px">Error: These arguments are Required<br />data-module_type, data-module_id, data-main_url</div>');
        }
    });
    //codebasket_comments js ends

    //function to show stars
    function fill_stars(ratings_div, stars,show_bigger,force_stop){
        if(!force_stop){
            force_stop = 't';
        }
        var bigger_star = "";
        if(show_bigger != 'true'){
            show_bigger = 'false';
        } else {
            bigger_star = " fa-2x";
        }
        var show_half = false;
        if((stars.indexOf('.'))>0 && force_stop == 'f'){
            var temp = stars.split('.');
            temp = temp[1];
            if(temp<5){stars = Math.floor(stars);}
            if(temp>5){stars = Math.ceil(stars);}
            if(temp=5){stars = Math.floor(stars); show_half = true;}
        }
        ratings_div.find('i#codebasket_rating_star').each(function(){
            var star_number = $(this).attr('star-number');
            if(star_number<=stars){
                if(star_number==stars)
                    $(this).attr('data-original-title',star_number+'/5').tooltip();
                else
                    $(this).prop('data-toggle','').prop('title','');
                $(this).prop('class','fa'+bigger_star+' fa-star');
            }
            else{
                $(this).prop('class','fa'+bigger_star+' fa-star-o');
            }
            if(show_half == true){
                if(star_number == (stars+1)){
                    $(this).prop('class','fa'+bigger_star+' fa-star-half-o');
                }
            }
        });
    }


    function put_data_in_comments_list(comment_div, data, prepend){
        var comments_list = comment_div.find('[data-id="codebasket_comments_list"]');
        var load_button = comment_div.find('[data-id="codebasket_comments_load_button"]');
        if(data['comments'] != 0){
            $.each(data['comments'], function(k,v){
                if(prepend == true){
                    comments_list.prepend('<div class="message-item" id="m16"><div class="message-inner"><div class="message-head clearfix"><div class="avatar pull-left"><img src="'+data['user_images'][v['user_id']]+'" class="img img-circle img-responsive" alt="No Image" style="margin-top:0px;" /></div><div class="user-detail"><h5 class="handle">'+data['user_names'][v['user_id']]+'</h5><div class="post-meta"><div class="asker-meta"><span class="qa-message-what"></span><span class="qa-message-when"><span class="qa-message-when-data">'+data['comment_dates'][v['id']]+' at '+data['comment_times'][v['id']]+'</span></span></div></div></div></div><div class="qa-message-content">'+v.comment+'</div></div></div>');
                } else {
                    comments_list.append('<div class="message-item" id="m16"><div class="message-inner"><div class="message-head clearfix"><div class="avatar pull-left"><img src="'+data['user_images'][v['user_id']]+'" class="img img-circle img-responsive" alt="No Image" style="margin-top:0px;" /></div><div class="user-detail"><h5 class="handle">'+data['user_names'][v['user_id']]+'</h5><div class="post-meta"><div class="asker-meta"><span class="qa-message-what"></span><span class="qa-message-when"><span class="qa-message-when-data">'+data['comment_dates'][v['id']]+' at '+data['comment_times'][v['id']]+'</span></span></div></div></div></div><div class="qa-message-content">'+v.comment+'</div></div></div>');
                }
            });
            var this_comments_num = comments_list.find(".message-item").size();

            if(this_comments_num < data['total_comments']){
                load_button.fadeIn(300).attr("data-next_comment",this_comments_num);
            } else{
                load_button.fadeOut(300).attr("data-next_comment",this_comments_num);
            }
            //cl(this_comments_num);
        } else {
            var this_comments_num = comments_list.find(".message-item").size();
            if(this_comments_num == 0){
                comments_list.html('<div style="padding:10px" class="alert alert-warning"><strong><i class="fa fa-wheelchair"></i> No Comments Found...</strong></div>');
            }
        }
    }
});