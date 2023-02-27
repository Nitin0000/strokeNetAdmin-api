<?php echo $this->render("themes/admin/html/elements/header.php") ?>
<?php
$file_fields_array = array('image' => array(), 'file' => array());
if (array_key_exists($this->table, $this->file_fields)) {
    $file_fields = $this->file_fields[$this->table];
    foreach ($file_fields as $db_key => $field_info) {
        $file_fields_array[$field_info['type']][$db_key] = $field_info['field'];
    }
} else {
    $file_fields = $this->file_fields;
}

$rel_main_fields_array = array();
$rel_simple_fields_array = array();
foreach ($this->get_another_data as $db_id => $rel_info) {
    if ($rel_info['is_multiple'] == 1 and $rel_info['main_table'] == $this->table) {
        $rel_main_fields_array[] = $rel_info['main_field'];
    } else {
        $rel_simple_fields_array[] = $rel_info['main_field'];
    }
}
?>


<section class="row">
    <div class="col-sm-12">
        <section class="row">

            <div class="col-md-12 col-lg-12">
                <?php if (!in_array($this->vars[2], $this->skipped_add_button)) {?>
                    <a class="btn btn-danger pull-right mt-2" style="margin-top:0px !important;" href="<?php echo _admin_url; ?>/add/<?php echo $this->vars[2]; ?>"><b>+</b> Add</a>
                <?php }?>


                <?php if ($this->vars[2] == "blogs") {?>
                    <a class="btn btn-success pull-right mt-2" style="margin-top:0px !important; margin-right: 10px;" href="<?php echo _admin_url; ?>/table/blog_categories"> Manage Blog Categories</a>
                <?php }?>

                <?php if ($this->vars[2] == "faqs") {?>
                    <a class="btn btn-success pull-right mt-2" style="margin-top:0px !important; margin-right: 10px;" href="<?php echo _admin_url; ?>/table/faqs_categories"> Manage Faqs Categories</a>
                <?php }?>


                <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'><?php echo format_names($this->vars[2]); ?></h1>

                <hr>
            </div>

            <div class="col-md-12 col-lg-12">

                <div class='card card-block jumbotron' style='padding: 20px; padding-bottom: 20px; margin-bottom: 0px;'>
                    <div class="row">
                        <div class="col-12 col-lg-6  col-sm-6 col-md-6">

                            <form action="<?php echo _admin_url; ?>/search/<?php echo $this->vars[2]; ?>"  name="csrf_form" method="post" style="padding:0px;">
                                <input type="hidden" name="csrf_token" value="<?php echo $this->formtoken; ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" placeholder="Search for a Record...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">Search</button>
                                    </span> </div>
                                <!-- /input-group -->
                            </form>

                        </div>

                        <div class="col-6 col-lg-3  col-sm-3 col-md-3">
                            <div class="row">
                                <div class="col-6 col-lg-8 hidden-sm-down  col-sm-8 col-md-8 text-right" style="padding-top: 5px; padding-right: 0px;">
                                    Records Per Page:
                                </div>

                                <div class="col-6 col-lg-4  col-sm-4 col-md-4" style="padding-right: 0px;">
                                    <select class="show-per-page form-control">
                                        <option value="10" <?php
if ($this->perpage == 10) {
    echo "selected='selected'";
}
?>>10</option>
                                        <option value="25" <?php
if ($this->perpage == 25) {
    echo "selected='selected'";
}
?>>25</option>
                                        <option value="50" <?php
if ($this->perpage == 50) {
    echo "selected='selected'";
}
?>>50</option>
                                        <option value="100" <?php
if ($this->perpage == 100) {
    echo "selected='selected'";
}
?>>100</option>
                                        <option value="250" <?php
if ($this->perpage == 250) {
    echo "selected='selected'";
}
?>>250</option>
                                        <option value="all" <?php
if ($this->perpage == 'all') {
    echo "selected='selected'";
}
?>>all</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3  col-sm-3 col-md-3">

                            <?php if ($_SESSION['admin_type'] == "super_admin") {?>
                                <div class="btn-group pull-right" style="margin-left:10px;">
                                    <button type="button" class="btn btn-primary">Bulk Actions</button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <!-- <li><a class="dropdown-item" href="javascript:void(0);" id="publishrecords">Publish</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" id="unpublishrecords">Unpublish</a></li> -->
                                        <li><a class="dropdown-item" href="javascript:void(0);" id="deleteselectedrecords"><i class="fa fa-remove"></i> Delete Selected</a></li>
                                    </ul>
                                </div>
                            <?php }?>




                        </div>

                    </div>


                </div>



                <div class="col-md-12 col-lg-12" style="margin-top:0px; padding:0px; padding-top: 20px;">


                    <?php
if ($this->total_records == 0) {
    ?>
                        <div class="alert alert-primary" style="padding-top: 60px; text-align: center">
                            <i style="color:#ccc; margin-bottom:20px; font-size: 100px;" class="fa fa-3x fa-archive"></i>
                            <h4 class="text-dark">Oh snap! Nothing found.</h4>
                            <p>Let's wait for some data to come :)</p>
                            <?php if (!in_array($this->vars[2], $this->skipped_add_button)) {?>
                                <p> <a href="<?php echo _admin_url; ?>/add/<?php echo $this->vars[2]; ?>" class="btn btn-danger btn-lg"><i class="fa fa-plus"></i> Add a Record</a> </p>
                            <?php } ?>
                        </div>
                    <?php
} else {
        ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="padding:10px;" id="myTable">
                                <thead>
                                    <tr >
                                        <th><center>
                                    <i class="fa fa-check checkall"></i>
                                </center></th>
                                <?php
foreach ($this->table_headers as $header) {
            ?>
                                    <th><a href="javascript:void(0);" class="sortbyASC" fieldname="<?php echo $header; ?>" method="<?php
if ($this->current_order == "ASC") {
                echo "DESC";
            } else {
                echo "ASC";
            } ?>"><?php echo format_names($header); ?>
                                               <?php if ($this->current_order == "ASC") {?>
                                                <i class="fa fa-sort-by-attributes"></i>
                                            <?php } else {?>
                                                <i class="fa fa-sort-by-attributes-alt"></i>
                                            <?php } ?>
                                        </a>
                                    </th>
                                <?php
        } ?>

                                <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
foreach ($this->records as $record => $value) {
            $id = $value[$this->tb_primaryid];
            $patient_user_id = $value['user_id']; ?>
                                        <tr record_id="<?php echo $id; ?>" id="row_<?php echo $id; ?>">
                                            <td width="40"><center>
                                        <input type="checkbox" class="checkrecord" id="<?php echo $id; ?>" />
                                    </center></td>
                                    <?php
foreach ($value as $key => $val) {
                if (in_array($key, $file_fields_array['file'])) {
                    $cur_dir = getcwd();
                    if (file_exists($cur_dir . $val)) {
                        $val = '<a class="btn btn-xs btn-primary" href="' . main_url . $val . '" target="_blank">Preview</a>';
                    } else {
                        $val = '<span style="cursor:pointer" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="File not Found">&nbsp;&nbsp;&nbsp;Error&nbsp;&nbsp;&nbsp;</span>';
                    }
                }

                if (in_array($key, $file_fields_array['image'])) {
                    $cur_dir = getcwd();
                    if (file_exists($cur_dir . $val) and $val) {
                        $val = '<a href="' . main_url . $val . '" target="_blank"><img class="img-thumbnail" src="' . main_url . $val . '" width="100" /></a>';
                    } else {
                        //$val = '<span style="cursor:pointer" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="File not Found">&nbsp;&nbsp;&nbsp;Error&nbsp;&nbsp;&nbsp;</span>';
                        $val = '<img class="img-thumbnail" src="http://placehold.it/270x200&text=No Image" width="100" />';
                    }
                }
                if (in_array($key, $rel_main_fields_array)) {
                    $got_db_result = unserialize($val);
                    $content = $value;
                    $val = '';
                    $val = '<div class="">';
                    foreach ($this->custom_data[$key]['data'] as $key_option => $val_option) {
                        if (in_array($val_option[$this->custom_data[$key]['attributes']['value']], $got_db_result)) {
                            //pr($this->custom_data[$key]);exit;
                            $val .= '<a class="" href="' . _admin_url . "/detail/" . $this->custom_data[$key]['attributes']['secondary_table'] . '/rec:' . $val_option[$this->custom_data[$key]['attributes']['value']] . '">' . $val_option[$this->custom_data[$key]['attributes']['seconday_field']] . '</a>, ';
                        }
                    }
                    $val .= "</div>";
                } elseif (in_array($key, $rel_simple_fields_array)) {
                    $content = $value;
                    if (isset($this->custom_data[$key]) && $this->custom_data[$key]) {
                        foreach ($this->custom_data[$key]['data'] as $key_option => $val_option) {
                            if ($val_option[$this->custom_data[$key]['attributes']['value']] == $content[$key]) {
                                $val = '<a href="' . _admin_url . "/detail/" . $this->custom_data[$key]['attributes']['secondary_table'] . '/rec:' . $val_option[$this->custom_data[$key]['attributes']['value']] . '">' . $val_option[$this->custom_data[$key]['attributes']['seconday_field']] . '</a>';
                            }
                        }
                    }
                }

                if ($key == "user_department" || $key == "user_role") {
                    $val = format_names($val);
                }

                if ($key == "Status" && $val == "1") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="publish_' . $id . '" action="publish" class="badge badge-success publish" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Inactive">Active</span>';
                } elseif ($key == "Status" && $val == "0") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="unpublish_' . $id . '" action="unpublish" class="badge badge-danger unpublish" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Active">Inactive</span>';
                }

                if ($key == "status" && $val == "1") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="publish_' . $id . '" action="publish" class="badge badge-success publish" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Inactive">Active</span>';
                } elseif ($key == "status" && $val == "0") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="unpublish_' . $id . '" action="unpublish" class="badge badge-danger unpublish" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Active">Inactive</span>';
                } elseif ($key == "featured" && $val == "1") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="featured_' . $id . '" action="featured" class="label label-success featured" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make UnFeatured">Featured</span>';
                } elseif ($key == "featured" && $val == "0") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="unfeatured_' . $id . '" action="unfeatured" class="label label-danger unfeatured" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Featured">Unfeatured</span>';
                } elseif ($key == "Popular" && $val == "1") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="featured_' . $id . '" action="featured" class="label label-success featured" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make UnFeatured">Active</span>';
                } elseif ($key == "Popular" && $val == "0") {
                    $val = '<span style="cursor:pointer" record_id="' . $id . '" id="unfeatured_' . $id . '" action="unfeatured" class="label label-danger unfeatured" data-toggle="tooltip" data-placement="top" title="" data-original-title="Make Featured">Inactive</span>';
                } ?>
                                        <td><?php echo $val; ?></td>

                                    <?php
            } ?>



                                    <td width="175">
                                        <div class="btn-group btn-group-sm">



                                                <a href="<?php echo _admin_url; ?>/edit/<?php echo $this->vars[2]; ?>/rec:<?php echo $id; ?>" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Record"><i class="fa fa-edit"></i></a>


                                                <a href="<?php echo _admin_url; ?>/detail/<?php echo $this->vars[2]; ?>/rec:<?php echo $id; ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Record Details"><i class="fa fa-list-alt"></i></a>



                                            <?php
if ($this->vars[2] == "admin_users") {
                // delete button hide for admin_users if record is equal to superadmin
                if ($id !== "1") {
                    echo '<a href="javascript:void(0);" class="btn btn-danger deletebtn" record_id="' . $id . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record"><i class="fa fa-remove"></i></a>';
                }
            } else {

            // delete button for all others table
                echo '<a href="javascript:void(0);" class="btn btn-danger deletebtn" record_id="' . $id . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Record"><i class="fa fa-remove"></i></a>'; ?>

        <?php
            } ?>



                                        </div>
                                    </td>
                                    </tr>
    <?php
        } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">

                            <div class="col-md-6 col-lg-6">
    <?php if ($this->total_records > 0) {
            ?>
                                    <h4 style="font-size:16px; font-weight: bold;">
                                        Showing <?php echo $this->current_records; ?> of <?php echo $this->total_records; ?> records
                                    </h4>
                                <?php
        } ?>
                            </div>

                            <div class="col-md-6 col-lg-6">
    <?php if ($this->total_pages > 1) {?>
                                    <div id="pagination_top"  style="float:right;"></div>
    <?php } ?>
                            </div>
                        </div>


<?php
    }?>

                </div>

            </div>

        </section>
    </div>
</section>
<script>
    $(document).ready(function () {

        /*Pagination Coding Starts*/
        var options = {
            currentPage: <?php echo $this->currentpage; ?>,
            totalPages: <?php echo $this->total_pages; ?>,
            numberOfPages: 8,
            alignment: "right",
            pageUrl: function (type, page, current) {
                return "<?php echo _admin_url; ?>/table/<?php echo $this->vars[2]; ?>/p:" + page;
            },
            useBootstrapTooltip: true,
            itemContainerClass: function (type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            tooltipTitles: function (type, page, current) {
                switch (type) {
                    case "first":
                        return "Go to First Page";
                    case "prev":
                        return "Go to Previous Page";
                    case "next":
                        return "Go to Next Page";
                    case "last":
                        return "Go to Last Page";
                    case "page":
                        return "Go to page " + page;
                }
            }
        }
        $('#pagination_top, #pagination_bottom').bootstrapPaginator(options);
        /*Pagination Coding Ends*/

        /* Select Which page to go*/
        $('.show-per-page').change(function () {
            var noofitems = $(this).val();

            document.location.href = '<?php echo _admin_url; ?>/table/<?php echo $this->vars[2]; ?>/p:1/perpage:' + noofitems;

        });

        $('.sortbyASC').on('click', function () {
            var fieldname = $(this).attr("fieldname");
            var method = $(this).attr("method");
            document.location.href = '<?php echo _admin_url; ?>/table/<?php echo $this->vars[2]; ?>/p:1/perpage:<?php echo $this->perpage; ?>/sortby:' + fieldname + ':' + method;
        });

        $('.sortbyDESC').on('click', function () {
            var fieldname = $(this).attr("fieldname");
            var method = $(this).attr("method");
            document.location.href = '<?php echo _admin_url; ?>/table/<?php echo $this->vars[2]; ?>/p:1/perpage:<?php echo $this->perpage; ?>/sortby:' + fieldname + ':' + method;
        });

        /* Check All*/

        var array = [];

        $('.checkall').on('click', function () {
            $('#myTable input[type="checkbox"]').trigger('click');
            $(this).addClass("removeCheckall");
            $(this).removeClass("checkall");
            console.log(array);

        });

        $('.removeCheckall').on('click', function () {

            $('#myTable input[type="checkbox"]').trigger('click');
            array = [];
            $(this).addClass("checkall");
            $(this).removeClass("removeCheckall");
            console.log(array);
        });

        $('#myTable input[type="checkbox"]').click(function () {
            var value = $(this).attr("id");
            $(this).attr("checked", this.checked);
            array.push(value);
        });



        // send push button
        $(".sendPushButton").on('click', function () {
            var event_type = $(this).attr("event_type");
            var postId = $(this).attr("postId");

            $.post('<?php echo _admin_url; ?>/common/actions', {method: "send_push", event_type: event_type, postId: postId}, function (data) {
                alert("Push Sent!")
            });
        });


        // publish post
        $(".publishButton").on('click', function () {
            var postId = $(this).attr("postId");

            $.post('<?php echo _admin_url; ?>/common/actions', {method: "publishPost", postId: postId}, function (data) {
                alert("Updated");
                setTimeout(function () {
                    location.reload();
                }, 1000);

            });
        });



        //deleteselectedrecords
        $("#deleteselectedrecords").on('click', function () {
            console.log(array);
            var blkstr = [];

            $.each(array, function (key, val) {
                var str = val;
                blkstr.push(str);
            });

            var record_ids = blkstr.join(",");
            var table_name = "<?php echo $this->vars[2] ?>";
            $(".contentloader").show();
            var alertconfirm = confirm("Are you sure you want to delete this record?");
            if (alertconfirm) {
                $.post('<?php echo _admin_url; ?>/common/actions', {method: "deletearecord", table: table_name, records: record_ids}, function (data) {
                    if (data == 1) {
                        location.reload();
                    } else {
                        alert("Record was not deleted. Please check again!");
                        $(".contentloader").hide();
                    }
                });
            }
            else {
                $(".contentloader").hide();
                return false;
            }
        });





        //Delete Button


        $(".deletebtn").on('click', function () {
            var record_ids = $(this).attr('record_id');
            var table_name = "<?php echo $this->vars[2] ?>";
            $(".contentloader").show();
            var alertconfirm = confirm("Are you sure you want to delete this record?");
            if (alertconfirm) {
                $.post('<?php echo _admin_url; ?>/common/actions', {method: "deletearecord", table: table_name, records: record_ids}, function (data) {
                    if (data == 1) {
                        //alert("Record was succesfully deleted!");
                        $("#myTable #row_" + record_ids + " td").css("background-color", "#f2dede");
                        $("#myTable #row_" + record_ids + " td").css("border", "1px solid #eed3d7");
                        $("#myTable #row_" + record_ids).fadeOut(900);
                        $(".contentloader").hide();
                        //location.reload();
                    } else {
                        alert("Record was not deleted. Please check again!");
                        $(".contentloader").hide();
                    }
                });
            }
            else {
                $(".contentloader").hide();
                return false;
            }
        });


                                                                                                                                                        //Publish Record
                                                                                                                                                        $('body').on('click', 'span.publish', function () {
                                                                                                                                                            var table_name = "<?php echo $this->vars[2] ?>";
                                                                                                                                                            var record_ids = $(this).attr('record_id');

                                                                                                                                                            $.post('<?php echo _admin_url; ?>/common/actions', {method: "unpublish", table: table_name, records: record_ids}, function (data) {

                                                                                                                                                                $('#publish_' + record_ids).removeClass('badge-success');
                                                                                                                                                                $('#publish_' + record_ids).addClass('badge-danger');
                                                                                                                                                                $('#publish_' + record_ids).addClass('unpublish');
                                                                                                                                                                $('#publish_' + record_ids).text('Inactive');
                                                                                                                                                                $('#publish_' + record_ids).attr('id', 'unpublish_' + record_ids);
                                                                                                                                                                $('#publish_' + record_ids).removeClass('publish');

                                                                                                                                                            });
                                                                                                                                                        });
                                                                                                                                                        //Unpublish Record
                                                                                                                                                        $('body').on('click', 'span.unpublish', function () {
                                                                                                                                                            //$('.unpublish').on('click', function () {
                                                                                                                                                            var table_name = "<?php echo $this->vars[2] ?>";
                                                                                                                                                            var record_ids = $(this).attr('record_id');

                                                                                                                                                            $.post('<?php echo _admin_url; ?>/common/actions', {method: "publish", table: table_name, records: record_ids}, function (data) {

                                                                                                                                                                $('#unpublish_' + record_ids).removeClass('badge-danger');
                                                                                                                                                                $('#unpublish_' + record_ids).addClass('badge-success');
                                                                                                                                                                $('#unpublish_' + record_ids).addClass('publish');
                                                                                                                                                                $('#unpublish_' + record_ids).text('Active');
                                                                                                                                                                $('#unpublish_' + record_ids).attr('id', 'publish_' + record_ids);
                                                                                                                                                                $('#unpublish_' + record_ids).removeClass('unpublish');


                                                                                                                                                            });
                                                                                                                                                        });

$('#publishrecords').on('click', function () {

                                                                                                                                                            console.log(array);
                                                                                                                                                            var blkstr = [];

                                                                                                                                                            $.each(array, function (key, val) {
                                                                                                                                                                var str = val;
                                                                                                                                                                blkstr.push(str);
                                                                                                                                                            });


                                                                                                                                                            var table_name = "<?php echo $this->vars[2] ?>";
                                                                                                                                                            var record_ids = blkstr.join(",");

                                                                                                                                                            $.post('<?php echo _admin_url; ?>/common/actions', {method: "publish", table: table_name, records: record_ids}, function (data) {
                                                                                                                                                                location.reload();
                                                                                                                                                            });
                                                                                                                                                        });

                                                                                                                                                        //Unpublish Record
                                                                                                                                                        $('#unpublishrecords').on('click', function () {

                                                                                                                                                            console.log(array);
                                                                                                                                                            var blkstr = [];

                                                                                                                                                            $.each(array, function (key, val) {
                                                                                                                                                                var str = val;
                                                                                                                                                                blkstr.push(str);
                                                                                                                                                            });

                                                                                                                                                            var table_name = "<?php echo $this->vars[2] ?>";
                                                                                                                                                            var record_ids = blkstr.join(",");

                                                                                                                                                            $.post('<?php echo _admin_url; ?>/common/actions', {method: "unpublish", table: table_name, records: record_ids}, function (data) {
                                                                                                                                                                location.reload();
                                                                                                                                                            });
                                                                                                                                                        });

                                                                                                                                                        //Delete All Records

                                                                                                                                                        $(".deleteallrecords").click(function () {
                                                                                                                                                            $(".contentloader").show();
                                                                                                                                                            var table_name = "<?php echo $this->vars[2] ?>";

                                                                                                                                                            var alertconfirm = confirm("Caution! This will delete all records from Database and this action is irrevocable. Are you sure you want to play with this Action?");

                                                                                                                                                            if (alertconfirm) {

                                                                                                                                                                $.post('<?php echo _admin_url; ?>/common/actions', {method: "deleteallrecords", table: table_name}, function (data) {
                                                                                                                                                                    //$('.result').html(data);
                                                                                                                                                                    if (data == 1) {
                                                                                                                                                                        alert("Data was succesfully deleted!");
                                                                                                                                                                        $(".contentloader").hide();
                                                                                                                                                                        location.reload();
                                                                                                                                                                    } else {
                                                                                                                                                                        alert("Data was not deleted. Please check again!");
                                                                                                                                                                        $(".contentloader").hide();
                                                                                                                                                                    }
                                                                                                                                                                });

                                                                                                                                                            }
                                                                                                                                                            else {
                                                                                                                                                                $(".contentloader").hide();
                                                                                                                                                                return false;
                                                                                                                                                            }
                                                                                                                                                        });                                                                                                                });
</script>
<?php echo $this->render("themes/admin/html/elements/footer.php") ?>