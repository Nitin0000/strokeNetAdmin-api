<?php echo $this->render("themes/admin/html/elements/header.php") ?>
<link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/libs/jquery-ui/jquery-ui.css">
<script type="text/javascript" src="<?php echo backoffice_url ?>/libs/chosen-jquery/chosen.jquery.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo backoffice_url ?>/libs/chosen-jquery/chosen.css">
<script type="text/javascript" src="<?php echo backoffice_url ?>/libs/jquery-ui/jquery-ui.js"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).ajaxComplete(function (event, XMLHttpRequest, ajaxOptions) {
            $(".form-group").each(function (index, element) {
                $(this).css("overflow", "visible");
            });
        });
        $(".chosen-select").each(function () {
            $(this).chosen();
            var id = $(this).attr("id");
            id = id.replace(/\-/g, '_');
            $("#" + id + "_chzn").css("width", "100%");
        });

        // Javascript to enable link to tab
        var url = document.location.toString();
        if (url.match('#')) {
            //$('.nav-tabs a[href=#' + url.split('#')[1] + ']').tab('show');
        }

        $('.nav-tabs a').on('click', function (e) {
            window.location.hash = e.target.hash;
        })

        // Change hash for page-reload
        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash;
        })

        //------------------------------------------------------------------------------
        // START OF REQUIRED SCRIPT
        //------------------------------------------------------------------------------
        $('select#required-table').on("change", function () {

            $("#required_field_chzn").remove();
            $("select#required-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            var sel_tbl = $(this).val();
            $(this).prop("disabled", true);
            $('div#required-fields').show().find('select').html('<option>Please Wait...</option>').prop("disabled", true);
            console.log(sel_tbl);
            $.post('<?php echo _admin_url ?>/ajax/get_required', {table_name: sel_tbl}, function (data) {
                console.log(data);
                $('select#required-table').prop("disabled", false);
                $('div#required-fields').find('select').html(data).prop("disabled", false).chosen();


            });
        });

        $('button#del-all-required').on("click", function () {
            var del_all_table = $(this).parent().prev().prev().val();
            var confir = confirm("This Will Delete All Required Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_required', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_tot_req_fields = $('input#num_tot_req_fields').val();
                    num_tot_req_fields--;
                    $('input#num_tot_req_fields').val(num_tot_req_fields);
                    if (num_tot_req_fields == 0) {
                        $('tbody#required_shown_table_body').html('<tr class="danger"><th colspan="2">No Required Fields Found</th></tr>').hide().show(300);
                    } else {
                        $('button').prop("disabled", false);
                        $('input[value="' + del_all_table + '"]').parent().parent().hide();
                        $('div#alert_success').show(300);
                        hide_alert();
                    }
                });
            }
        });

        $('button#del-col-required').on("click", function () {
            var table = $(this).parent().prev().val();
            var column = $(this).parent().prev().prev().val();
            var db_id = $(this).parent().next().val();
            var confir = confirm("This will delete " + column + ' from table ' + table + '\n Are You Sure???');
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_required_field', {table: table, column: column}, function (data) {
                    $('button').prop("disabled", false);
                    var num_rows_tbl = $("input#num_rows_" + table).val();
                    num_rows_tbl--;
                    $("input#num_rows_" + table).val(num_rows_tbl);
                    if (num_rows_tbl == 0) {
                        var num_tot_req_fields = $('input#num_tot_req_fields').val();
                        num_tot_req_fields--;
                        $('input#num_tot_req_fields').val(num_tot_req_fields);
                        if (num_tot_req_fields == 0) {
                            $('tbody#required_shown_table_body').html('<tr class="danger"><th colspan="2">No Required Fields Found</th></tr>').hide().show(300);
                            $('div#alert_success').show(300);
                            hide_alert();
                        } else {
                            $('button').prop("disabled", false);
                            $('input[value="' + table + '"]').parent().parent().hide();
                            $('div#alert_success').show(300);
                            hide_alert();
                        }
                    }
                    else {
                        $('input[value="' + db_id + '"]').parent().hide(300);
                        $('div#alert_success').show(300);
                        hide_alert();
                    }
                });
            }
        });

        //------------------------------------------------------------------------------
        // END OF REQUIRED SCRIPT
        //------------------------------------------------------------------------------

        //------------------------------------------------------------------------------
        // START OF CKEDITOR SCRIPT
        //------------------------------------------------------------------------------
        $('select#ckeditor-table').on("change", function () {

            $("#ckeditor_field_chzn").remove();
            $("select#ckeditor-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            var sel_tbl = $(this).val();
            $(this).prop("disabled", true);
            $('div#ckeditor-fields').show().find('select').html("<option>Please Wait...</option>").prop("disabled", true);
            $.post('<?php echo _admin_url ?>/ajax/get_ckeditor', {table_name: sel_tbl}, function (data) {
                $('div#ckeditor-fields').find('select').html(data).prop("disabled", false).chosen();
                $('select#ckeditor-table').prop("disabled", false);
            });
        });

        $('button#del-all-ckeditor').on("click", function () {
            var del_all_table = $(this).parent().prev().prev().val();
            var confir = confirm("This Will Delete All CKEditor Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_ckeditor', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_tot_ck_fields = $('input#num_tot_ck_fields').val();
                    num_tot_ck_fields--;
                    $('input#num_tot_ck_fields').val(num_tot_ck_fields);
                    if (num_tot_ck_fields == 0) {
                        show_empty_ck();
                    } else {
                        hide_table_row(del_all_table);
                    }
                });
            }
        });

        $('button#del-col-ckeditor').on("click", function () {
            var table = $(this).parent().prev().val();
            var column = $(this).parent().prev().prev().val();
            var db_id = $(this).parent().next().val();
            var confir = confirm("This will delete " + column + ' of table ' + table + ' from CKEditor Fields\n Are You Sure???');
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_ckeditor_field', {table: table, column: column}, function (data) {
                    $('button').prop("disabled", false);
                    var tot_tbl_ck_fields = $('input#tot_' + table + '_ck_fields').val();
                    tot_tbl_ck_fields--;
                    $('input#tot_' + table + '_ck_fields').val(tot_tbl_ck_fields);
                    if (tot_tbl_ck_fields == 0) {
                        //hide table
                        var num_tot_ck_fields = $('input#num_tot_ck_fields').val();
                        num_tot_ck_fields--;
                        $('input#num_tot_ck_fields').val(num_tot_ck_fields);
                        if (num_tot_ck_fields == 0) {
                            //show empty ck
                            show_empty_ck();
                        }
                        else {
                            //hide table
                            hide_table_row(table);
                        }
                    } else {
                        //hide row
                        hide_col_div(db_id);
                    }
                });
            }
        });

        //------------------------------------------------------------------------------
        // END OF CKEDITOR SCRIPT
        //------------------------------------------------------------------------------

        //------------------------------------------------------------------------------
        // START OF HIDDEN SCRIPT
        //------------------------------------------------------------------------------
        $('select#hidden-table').on("change", function () {

            $("#hidden_field_chzn").remove();
            $("select#hidden-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            var sel_tbl = $(this).val();
            $('div#hidden-fields').show().find('select').html("<option>Please Wait...</option>").prop("disabled", true);
            $(this).prop("disabled", true);
            $.post('<?php echo _admin_url ?>/ajax/get_hidden', {table_name: sel_tbl}, function (data) {
                $('div#hidden-fields').find('select').html(data).prop("disabled", false).chosen();
                $('select#hidden-table').prop("disabled", false);
            });
        });

        $('button#del-all-hidden').on("click", function () {
            var del_all_table = $(this).parent().prev().prev().val();
            var confir = confirm("This Will Delete All hidden Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_hidden', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_hidden_fields = $('input#num_hidden_fields').val();
                    num_hidden_fields--;
                    $('input#num_hidden_fields').val(num_hidden_fields);
                    if (num_hidden_fields == 0) {
                        show_empty_hidden();
                    } else {
                        hide_table_row(del_all_table);
                    }
                });
            }
        });

        $('button#del-col-hidden').on("click", function () {
            var table = $(this).parent().prev().val();
            console.log("table : " + table);
            var column = $(this).parent().prev().prev().val();
            console.log("col : " + column);
            var db_id = $(this).parent().next().next().val();
            console.log("db_id : " + db_id);
            var confir = confirm("This will delete " + column + ' of table ' + table + ' from hidden Fields\n Are You Sure???');
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_hidden_field', {table: table, column: column}, function (data) {
                    $('button').prop("disabled", false);
                    var table_hidden_fields = $('input#' + table + '_hidden_fields').val();
                    table_hidden_fields--;
                    $('input#' + table + '_hidden_fields').val(table_hidden_fields);
                    if (table_hidden_fields == 0) {
                        //hide table
                        var num_hidden_fields = $('input#num_hidden_fields').val();
                        num_hidden_fields--;
                        $('input#num_hidden_fields').val(num_hidden_fields);
                        if (num_hidden_fields == 0) {
                            //show empty hidden
                            show_empty_hidden();
                        }
                        else {
                            //hide table
                            hide_table_row(table);
                        }
                    } else {
                        //hide row
                        hide_col_div(db_id);
                    }
                });
            }
        });
        //------------------------------------------------------------------------------
        // END OF hidden SCRIPT
        //------------------------------------------------------------------------------

        //------------------------------------------------------------------------------
        // START OF date SCRIPT
        //------------------------------------------------------------------------------
        $('select#multiple-table').on("change", function () {

            $("#multiple_field_chzn").remove();
            $("select#multiple-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            $(this).prop("disabled", true);
            $('div#multiple-fields').show().find('select').html("<option>Please Wait...</option>").prop("disabled", true);
            var sel_tbl = $(this).val();
            $.post('<?php echo _admin_url ?>/ajax/get_multiple', {table_name: sel_tbl}, function (data) {
                $('div#multiple-fields').find('select').html(data).prop("disabled", false).chosen();
                $('select#multiple-table').prop("disabled", false);
            });
        });

        $('button#del-all-multiple').on("click", function () {
            var del_all_table = $(this).parent().prev().prev().val();
            var confir = confirm("This Will Delete All Date Picker Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_multiple', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_date_fields = $('input#num_date_fields').val();
                    num_date_fields--;
                    $('input#num_date_fields').val(num_date_fields);
                    if (num_date_fields == 0) {
                        show_empty_date();
                    } else {
                        hide_table_row(del_all_table);
                    }
                });
            }
        });

        $('button#del-col-multiple').on("click", function () {
            var table = $(this).parent().prev().val();
            var column = $(this).parent().prev().prev().val();
            var db_id = $(this).parent().next().next().val();
            var confir = confirm("This will delete " + column + ' of table ' + table + ' from Date Picker Fields\n Are You Sure???');
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_multiple_field', {table: table, column: column}, function (data) {
                    $('button').prop("disabled", false);
                    var table_date_fields = $('input#' + table + '_date_fields').val();
                    table_date_fields--;
                    $('input#' + table + '_date_fields').val(table_date_fields);
                    if (table_date_fields == 0) {
                        //hide table
                        var num_date_fields = $('input#num_date_fields').val();
                        num_date_fields--;
                        $('input#num_date_fields').val(num_date_fields);
                        if (num_date_fields == 0) {
                            //show empty hidden
                            show_empty_date();
                        }
                        else {
                            //hide table
                            hide_table_row(table);
                        }
                    } else {
                        //hide row
                        hide_col_div(db_id);
                    }
                });
            }
        });
        //------------------------------------------------------------------------------
        // END OF date SCRIPT
        //------------------------------------------------------------------------------

        //------------------------------------------------------------------------------
        // START OF FILES SCRIPT
        //------------------------------------------------------------------------------

        $('select#file-table').on("change", function () {

            $("#file_field_chzn").remove();
            $("select#file-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);


            var sel_tbl = $(this).val();
            $(this).prop("disabled", true);
            $('div#file-fields').show().find('select#file-field').html('<option>Please Wait...</option>').prop("disabled", true);
            $('div#folder-and-type').hide();
            $.post('<?php echo _admin_url ?>/ajax/get_file_fields', {table_name: sel_tbl}, function (data) {
                $('select#file-table').prop("disabled", false);
                $('div#file-fields').find('select#file-field').html(data).prop("disabled", false).chosen();
                $('div#folder-and-type').show(500);
            });
        });

        function get_directory_list(root) {
            var prev = get_prev_dir_name(root);
            $('div#directories_list').html('<h3>Opening, Please Wait...</h3>');
            $.post('<?php echo _admin_url ?>/ajax/get_directory_list', {root: root}, function (data) {
                var arr = $.parseJSON(data);
                var directories = '<a href="javascript:void()" id="add-folder" class="btn btn-success btn-xs" style="margin-bottom:8px">Add New Folder</a><button title="Refresh List" class="btn btn-success btn-xs pull-right" id="file-refresh-list"><i class="fa fa-refresh" /></button><span style="display:none" id="folder-input"><input id="folder-name" type="text" placeholder="Enter Folder Name" class="col-lg-4 input-xs" /><a id="create-folder" href="javascript:void()" class="btn btn-success btn-xs" style="margin-left:5px">Create</a><a id="cancel-create" href="javascript:void()" class="btn btn-danger btn-xs" style="margin-left:5px">Cancel</a></span><br /><br /><table class="table table-hover table-condensed">';
                if (prev) {
                    directories += '<tr class="warning"><th id="prev-dir" style="cursor:pointer"><input id="prev-dir" type="hidden" value="' + prev + '" /><i class="fa fa-arrow-left" /> Back</th></tr>';
                }
                $.each(arr, function (l, v) {
                    directories += '<tr><th id="next-dir" style="cursor:pointer"><input id="next-dir" type="hidden" value="' + v + '" /><i class="fa fa-folder-close" /> ' + v + '<input id="del-folder-path" type="hidden" value="' + v + '" /><button id="file-del-folder" class="btn btn-danger btn-xs pull-right"><i class="fa fa-remove"></i> Delete</button></th></tr>';
                });
                directories += '<tr><th></th></tr></table>';
                $('div#directories_list').html(directories).find('a#add-folder').click(function () {
                    $(this).hide(100).next().next().show(100);
                });
                $('div#directories_list').find('a#cancel-create').click(function () {
                    $(this).parent().hide(100);
                    $('a#add-folder').show(100);
                });
                $('div#directories_list').find('a#create-folder').click(function () {
                    $(this).prop("disabled", true).prev().prop("disabled", true);
                    var folder_name = $(this).prev().val();
                    
                    $.post('<?php echo _admin_url ?>/ajax/file_create_folder', {root: root, folder_name: folder_name}, function (data) {
                        $('a#create-folder').prop("disabled", false).prev().prop("disabled", false).parent().hide(100).prev().show(100);
                        get_directory_list(root);
                    });
                });
                $("[data-toggle=tooltip]").tooltip();
                $('div#breadcrumb').html("/" + root);
                $('th#next-dir').click(function () {
                    var next_dir = $(this).find('input#next-dir').val();
                    get_directory_list(root + '/' + next_dir);
                });
                $('th#prev-dir').click(function () {
                    var prev_dir = $(this).find('input#prev-dir').val();
                    get_directory_list(prev_dir);
                });
                $('div#directories_list').find('button#file-del-folder').click(function () {
                    var confir = confirm('This Will Delete all Files and Subfolder of this folder...\n Are you Sure about this...???');
                    if (confir) {
                        var folder_name = $(this).prev().val();
                        //console.log(root+" : "+folder_name);
                        $.post('<?php echo _admin_url; ?>/ajax/file_del_folder', {root: root, folder_name: folder_name}, function (data) {
                            console.log(data);
                            get_directory_list(root);
                        });
                    }
                });
                $('input#file-path').val("/" + root);
                $('button#file-refresh-list').click(function () {
                    get_directory_list(root);
                });
            });
        }

        function get_prev_dir_name(dir_name) {
            if (str_len == 0)
                return;
            var my_array = dir_name.split('/');
            my_array.pop();
            var prev_dir = '';
            $.each(my_array, function (l, v) {
                prev_dir += v + '/';
            });
            var str_len = prev_dir.length;
            prev_dir = prev_dir.substring(0, str_len - 1);
            return prev_dir;
        }

        get_directory_list('uploads');

        $('button#del-all-file').click(function () {
            var del_all_table = $(this).prev().prev().val();
            var confir = confirm("This Will Delete All File Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_file_fields', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_file_fields = $('input#num_file_fields').val();
                    num_file_fields--;
                    $('input#num_file_fields').val(num_file_fields);
                    if (num_file_fields == 0) {
                        //show_empty_files
                        show_empty_files();
                    } else {
                        //hide_table_row
                        hide_file_main_table(del_all_table);
                    }
                });
            }
        });

        $('button#del-col-file').on("click", function () {
            var db_key = $(this).prev().val();
            var table = $(this).next().next().val();
            var confir = confirm("This will delete selected column from table \n Are You Sure???");
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_file_field', {db_key: db_key}, function (data) {
                    $('button').prop("disabled", false);
                    var table_file_fields = $('input#' + table + '_file_fields').val();
                    table_file_fields--;
                    $('input#' + table + '_file_fields').val(table_file_fields);
                    if (table_file_fields == 0) {
                        var num_file_fields = $('input#num_file_fields').val();
                        num_file_fields--;
                        $('input#num_file_fields').val(num_file_fields);
                        if (num_file_fields == 0) {
                            show_empty_files();
                        }
                        else {
                            hide_file_main_table(table);
                        }
                    } else {
                        hide_file_field(db_key);
                    }
                });
            }
        });

        $('select#file-type').on("change", function () {
            var file_type_value = $(this).val();
            $('div#file-allowed-file-exts').hide();
            $('div#file-allowed-image-exts').hide();
            if (file_type_value == 'image')
                $('div#file-allowed-image-exts').show(300);
            else
                $('div#file-allowed-file-exts').show(300);
        });
        //------------------------------------------------------------------------------
        // END OF FILES SCRIPT
        //------------------------------------------------------------------------------

        //------------------------------------------------------------------------------
        // START OF SLUG SCRIPT
        //------------------------------------------------------------------------------

        $('button#del-all-slug').on("click", function () {
            var del_all_table = $(this).parent().prev().prev().val();
            var confir = confirm("This Will Delete All Slug Fields From '" + del_all_table + "' Table...\n Are You Sure???");
            if (confir) {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_slug', {table: del_all_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_slug_fields = $('input#num_slug_fields').val();
                    num_slug_fields--;
                    $('input#num_slug_fields').val(num_slug_fields);
                    if (num_slug_fields == 0) {
                        //show_empty_files
                        show_empty_slug();
                    } else {
                        //hide_table_row
                        hide_slug_main_table(del_all_table);
                    }
                });
            }
        });

        $('button#del-col-slug').on("click", function () {
            var del_id = $(this).parent().parent().parent().find('input#slug-field-id').val();
            var table = $(this).parent().next().val();
            var confir = confirm("This will delete this Slug Relationship...\n Are You Sure???");
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_slug_field', {del_id: del_id}, function (data) {
                    $('button').prop("disabled", false);
                    var table_slug_fields = $('input#' + table + '_slug_fields').val();
                    table_slug_fields--;
                    $('input#' + table + '_slug_fields').val(table_slug_fields);
                    if (table_slug_fields == 0) {
                        var num_slug_fields = $('input#num_slug_fields').val();
                        num_slug_fields--;
                        $('input#num_slug_fields').val(num_slug_fields);
                        if (num_slug_fields == 0) {
                            show_empty_slug();
                        }
                        else {
                            hide_slug_main_table(table);
                        }
                    } else {
                        hide_slug_field(del_id);
                    }
                });
            }
        });
        $('select#slug-table').on("change", function () {

            $("#slug_main_field_chzn").remove();
            $("select#slug-main-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);


            $('div#slug-secondary-fields').hide();
            $('div#slug-main-fields').show().find('select').html("<option>Please Wait...</option>").attr("disabled", true);
            $(this).attr("disabled", true);
            var sel_tbl = $(this).val();
            $.post('<?php echo _admin_url ?>/ajax/get_primary_slug', {table_name: sel_tbl}, function (data) {
                $('select#slug-table').attr("disabled", false);
                $('div#slug-main-fields').show().find('select').html(data).attr("disabled", false).chosen();
            });
        });

        $('select#slug-main-field').on("change", function () {

            $("#slug_secondary_field_chzn").remove();
            $("select#slug-secondary-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            $(this).attr("disabled", true);
            $('select#slug-table').attr("disabled", true);
            $('div#slug-secondary-fields').show().find('select').html("<option>Please Wait...</option>").attr("disabled", true);
            var sel_tbl = $('select#slug-table').val();
            var main_field = $(this).val();
            $.post('<?php echo _admin_url ?>/ajax/get_secondary_slug', {table_name: sel_tbl, slug_field: main_field}, function (data) {
                $('select#slug-main-field').attr("disabled", false);
                $('select#slug-table').attr("disabled", false);
                $('div#slug-secondary-fields').show().find('select').html(data).attr("disabled", false).chosen();
            });
        });


        //------------------------------------------------------------------------------
        // END OF SLUG SCRIPT
        //------------------------------------------------------------------------------

        //----------------------------------------------------------------
        // START OF RELATIONSHIPS SCRIPT
        //----------------------------------------------------------------
        $('select#rel-main-table').on("change", function () {


            $("#rel_main_field_chzn").remove();
            $("select#rel-main-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);


            var rel_main_table = $(this).val();
            $(this).prop("disabled", true);
            $('div#rel-secondary-tables').hide(100);
            $('div#rel-secondary-fields').hide(100);
            $('div#rel-select-fields').hide(100);
            $('div#rel-main-fields').show().find('select').html("<option>Please Wait...</option>").attr("disabled", true);
            $.post('<?php echo _admin_url ?>/ajax/get_rel_main_fields', {rel_main_table: rel_main_table}, function (data) {
                $('select#rel-main-table').prop("disabled", false);
                $('select#rel-main-field').prop("disabled", false);
                $('div#rel-main-fields').find('select').html(data);
                $("#rel-main-field").chosen();


            });
        });

        $('select#rel-main-field').on("change", function () {

            $("#rel_secondary_table_chzn").remove();
            $("select#rel-secondary-table").removeClass("chzn-done").css('display', 'inline').data('chosen', null);


            var rel_main_table = $('select#rel-main-table').val();
            $('div#rel-secondary-fields').hide(100);
            $('div#rel-select-fields').hide(100);
            $('select#rel-main-table').prop("disabled", true);
            $(this).prop("disabled", true);
            $('div#data').hide(1000);
            $('div#rel-secondary-tables').show().css("overflow", "visible").find('select').html("<option>Please Wait...</option>").attr("disabled", true);
            $.post('<?php echo _admin_url ?>/ajax/get_rel_secondary_tables', {rel_main_table: rel_main_table}, function (data) {
                //$('div#data').html(data).show(100);
                $('select#rel-main-table').prop("disabled", false);
                $('select#rel-main-field').prop("disabled", false);
                $('div#rel-secondary-tables').find('select').html(data).css("overflow", "visible").prop("disabled", false);
                $("#rel-secondary-table").chosen();
            });
        });

        $('select#rel-secondary-table').on("change", function () {

            $("#rel_secondary_field_chzn").remove();
            $("select#rel-secondary-field").removeClass("chzn-done").css('display', 'inline').data('chosen', null);

            var rel_secondary_table = $(this).val();
            $('select#rel-main-table').prop("disabled", true);
            $('select#rel-main-field').prop("disabled", true);
            $(this).prop("disabled", true);
            $('div#rel-secondary-fields').show().css("overflow", "visible").find('select').html("<option>Please Wait...</option>").css("overflow", "visible").prop("disabled", true);
            $('div#rel-select-fields').show().css("overflow", "visible").find('select').html("<option>Please Wait...</option>").css("overflow", "visible").prop("disabled", true);
            $.post('<?php echo _admin_url ?>/ajax/get_rel_secondary_fields', {rel_secondary_table: rel_secondary_table}, function (data) {
                $('select#rel-main-table').prop("disabled", false);
                $('select#rel-main-field').prop("disabled", false);
                $('select#rel-secondary-table').prop("disabled", false);
                $('select#rel-secondary-field').html(data).css("overflow", "visible").prop("disabled", false).chosen();
                $('select#rel-select-field').html(data).css("overflow", "visible").prop("disabled", false).chosen();


            });
        });

        $('button#del-all-rel').on("click", function () {
            var main_table = $(this).parents('th').find('input').val();
            var confir = confirm("Are You Sure, You Want To delete All Relationships on " + main_table + "???\n This can't be undone..!!");
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_all_rel', {main_table: main_table}, function (data) {
                    $('button').prop("disabled", false);
                    var num_rel_fields = $('input#num_rel_fields').val();
                    num_rel_fields--;
                    $('input#num_rel_fields').val(num_rel_fields);
                    if (num_rel_fields == 0) {
                        //show empty relationships
                        show_empty_relationships();
                    } else {
                        //hide that row
                        hide_rel_main_table(main_table);
                    }
                });
            }
        });

        $('button#del-col-rel').on("click", function () {
            var rel_del_id = $(this).parents('tr').find('input#db-key').val();
            var table = $(this).next().val();
            var confir = confirm("Are You Sure, You Want To delete This Relationship..??\n This can't be undone..!!");
            if (confir)
            {
                $('button').prop("disabled", true);
                $.post('<?php echo _admin_url ?>/ajax/del_col_rel', {rel_del_id: rel_del_id}, function () {
                    $('button').prop("disabled", false);
                    var table_rel_fields = $('input#' + table + '_rel_fields').val();
                    table_rel_fields--;
                    $('input#' + table + '_rel_fields').val(table_rel_fields);
                    if (table_rel_fields == 0) {
                        var num_rel_fields = $('input#num_rel_fields').val();
                        num_rel_fields--;
                        $('input#num_rel_fields').val(num_rel_fields);
                        if (num_rel_fields == 0) {
                            show_empty_relationships();
                        }
                        else {
                            hide_rel_main_table(table);
                        }
                    } else {
                        hide_rel_field(rel_del_id);
                    }
                });
            }
        });

        //----------------------------------------------------------------
        // END OF RELATIONSHIPS SCRIPT
        //----------------------------------------------------------------

        function hide_alert() {
            $('div#alert_error').stop().delay(2000).fadeOut(2000);
            $('div#alert_success').stop().delay(2000).fadeOut(2000);
        }
        function hide_table_row(tbl_nm) {
            $('tr#' + tbl_nm).hide(300);
        }
        function hide_col_div(db_id) {
            $('div#' + db_id).hide(300);
        }
        function hide_file_main_table(tbl_nm) {
            $('tr#file_' + tbl_nm).hide(300);
        }
        function hide_slug_main_table(tbl_nm) {
            $('tr#slug_' + tbl_nm).hide(300);
        }
        function hide_rel_main_table(tbl_nm) {
            $('tr#another_' + tbl_nm).hide(300);
        }
        function hide_file_field(db_id) {
            $('tr#' + db_id).hide(300);
        }
        function hide_slug_field(db_id) {
            $('tr#' + db_id).hide(300);
        }
        function hide_rel_field(db_id) {
            $('tr#' + db_id).hide(300);
        }

        function show_empty_hidden() {
            $('tbody#hidden_shown_table_body').html('<tr class="danger"><th colspan="2">No Hidden Fields Found</th></tr>').hide().show(300);
        }
        function show_empty_ck() {
            $('tbody#ckeditor_shown_table_body').html('<tr class="danger"><th colspan="2">No CKEditor Fields Found</th></tr>').hide().show(300);
        }
        function show_empty_date() {
            $('tbody#date_shown_table_body').html('<tr class="danger"><th colspan="2">No Date Picker Fields Found</th></tr>').hide().show(300);
        }
        function show_empty_files() {
            $('tbody#file_shown_table_body').html('<tr class="danger"><th colspan="2">No File Fields Found</th></tr>').hide().show(300);
        }
        function show_empty_slug() {
            $('tbody#slug_shown_table_body').html('<tr class="danger"><th colspan="2">No Slug Fields Found</th></tr>').hide().show(300);
        }
        function show_empty_relationships() {
            $('tbody#rel_shown_table_body').html('<tr class="danger"><th colspan="2">No Relationships Found</th></tr>').hide().show(300);
        }

        $('div#alert').delay(5000).fadeOut(2000);
    });
</script>

<section class="row">
    <div class="col-sm-12">
        <section class="row">

            <div class="col-md-12 col-lg-12">
                <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>
                    Form Fields Management
                </h1>
<!--                <p class="hidden-sm-down" style='margin:0px;'>Manage your website settings here</p>-->
                <hr>
            </div>
            
            
            
            <div class="col-md-12 col-lg-12">
                
                <div class="bs-example bs-example-tabs">

            <ul id="myTab" class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" href="#required" data-toggle="tab">Required Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#ckeditor" data-toggle="tab">CKEditor Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#hidden" data-toggle="tab">Hidden Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#date" data-toggle="tab">Date Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#files" data-toggle="tab">File Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#slug" data-toggle="tab">Slug Fields</a></li>
                <li class="nav-item"><a class="nav-link" href="#relationships" data-toggle="tab">Table Relationships</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane active" id="required">
                    
                    <div class="row">
                    <div class="col-md-8 col-lg-8">
                        <br>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="active">
                                    <td width="30%"><strong>Table Name</strong></td>
                                    <td><strong>Fields</strong></td>
                                </tr>
                            </thead>
                            <tbody id="required_shown_table_body">
                                <?php if ($this->required_fields) {
                                    $required_fields = $this->required_fields; ?>
                                    <?php
                                    ksort($required_fields);
                                    $num_tot_req_fields = count($required_fields);
                                    echo '<input id="num_tot_req_fields" type="hidden" value="' . $num_tot_req_fields . '">';
                                    foreach ($required_fields as $tbl_nm => $fields) {
                                        $num_req_fields = count($fields);
                                        echo '<tr>
							  <th>
							  <input type="hidden" value="' . $tbl_nm . '" />
							  ' . $tbl_nm . '
							  <br>
							  <span class="">
							  <button class="btn btn-xs btn-danger" id="del-all-required" title="Delete All Fields From \'' . $tbl_nm . '\'">
							  <i class="fa fa-remove"></i> Delete All
							  </button>
							  </span>
							  <input type="hidden" id="num_rows_' . $tbl_nm . '" value="' . $num_req_fields . '">
							  </th>
							  <th>';
                                        foreach ($fields as $k => $field_name) {
                                            echo '<div class="row"><div class="col-md-12">
							<input id="required-field-name" type="hidden" value="' . $field_name . '" />
							<input id="required-field-table" type="hidden" value="' . $tbl_nm . '" />' . $field_name . '
							<span class="pull-right" style="padding:2px"><button class="btn btn-danger btn-xs" id="del-col-required" title="Delete ' . $field_name . '"><i class="fa fa-remove"></i> Delete</button></span>
							 <input type="hidden" value="' . $k . '" />
							</div></div>';
                                        }
                                        echo '</th>
							  </tr>';
                                    }
                                } else {
                                    echo '<tr class="danger"><th colspan="2">No Required Fields Found</th></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 col-lg-4" id="required-form">
                        <p></p>
                        <div class="card">
                                    <div class="card-header">Add New Required Field</div>
                                    <div class="card-block">
                                <form role="form" method="post">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Table Name</label>
                                        <select data-placeholder="Select Table..." class="form-control chosen-select" name="required[table]" id="required-table">
                                            <option style="display:none" value="">Select Table...</option>
                                            <?php
                                            foreach ($this->tables as $k => $table_name) {
                                                //if (!in_array($table_name, $this->skipped_tables)) {
                                                    echo '<option value="' . $table_name . '">' . $table_name . '</option>';
                                                //}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="required-fields" style="display:none">
                                        <label>Field Name <span class="text-danger">*</span><small class="text-warning">Hold Ctrl to Select Multiple Values</small></label>
                                        <select data-placeholder="Choose Fields..." multiple class="form-control" name="required[fields][]" id="required-field"></select>
                                    </div>
                                    <div class="panel-footer">
                                        <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                        </div>

                </div>
                <div class="tab-pane fade" id="ckeditor">
                    <p></p>
                    <div class="panel panel-default">
                        
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="30%"><strong>Table Name</strong></td>
                                            <td><strong>Fields</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="ckeditor_shown_table_body">
<?php
if ($this->ckeditor_fields) {
    $ckeditor_fields = $this->ckeditor_fields;
    ksort($ckeditor_fields);
    $num_tot_ck_fields = count($ckeditor_fields);
    echo '<input id="num_tot_ck_fields" type="hidden" value="' . $num_tot_ck_fields . '">';
    foreach ($ckeditor_fields as $tbl_nm => $fields) {
        $tot_tbl_ck_fields = count($fields);
        echo '<tr id="' . $tbl_nm . '">
							  <th>
							  <input type="hidden" value="' . $tbl_nm . '" />
							  ' . $tbl_nm . '
							  <br>
							  <span class="">
							  <button class="btn btn-xs btn-danger" id="del-all-ckeditor" title="Delete All Fields From \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button>
							  </span>
							  <input type="hidden" id="tot_' . $tbl_nm . '_ck_fields" value="' . $tot_tbl_ck_fields . '">
							  </th>
							  <th>';
        foreach ($fields as $k => $field_name) {
            echo '<div class="row" id="' . $k . '"><div class="col-md-12">
								<input id="ckeditor-field-name" type="hidden" value="' . $field_name . '" />
								<input id="ckeditor-field-table" type="hidden" value="' . $tbl_nm . '" />
								' . $field_name . '
								<span class="pull-right" style="padding:2px">
								<button class="btn btn-danger btn-xs" id="del-col-ckeditor" title="Delete ' . $field_name . '"><i class="fa fa-remove"></i> Delete</button>
								</span>
								<input type="hidden" value="' . $k . '">
								</div></div>';
        }
        echo '</th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No CKEditor Fields Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="ckeditor-form">
                                <div class="card">
                                    <div class="card-header">Add New CKEditor Field</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Table Name</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="ckeditor[table]" id="ckeditor-table">
                                                    <option style="display:none" value="">Select Table...</option>
<?php
foreach ($this->tables as $k => $table_name) {
    //if (!in_array($table_name, $this->skipped_tables)) {
        echo '<option value="' . $table_name . '">' . $table_name . '</option>';
    //}
}
?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="ckeditor-fields" style="display:none">
                                                <label>Field Name <span class="text-danger">*</span><small class="text-warning">Hold Ctrl to Select Multiple Values</small></label>
                                                <select data-placeholder="Choose Fields..." multiple class="form-control" name="ckeditor[fields][]" id="ckeditor-field"></select>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hidden">
                    <p></p>
                    <div class="panel panel-default">
                       
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="30%"><strong>Table Name</strong></td>
                                            <td><strong>Fields</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="hidden_shown_table_body">
<?php if ($this->hidden_fields) {
    $hidden_fields = $this->hidden_fields; ?>
    <?php
    ksort($hidden_fields);
    $num_hidden_fields = count($hidden_fields);
    foreach ($hidden_fields as $tbl_nm => $fields) {
        $table_hidden_fields = count($fields);
        echo '<tr id="' . $tbl_nm . '">
							  <th><input type="hidden" value="' . $tbl_nm . '" />' . $tbl_nm . '<br><span class=""><button class="btn btn-xs btn-danger" id="del-all-hidden" title="Delete All Fields From \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button></span>
							  <input type="hidden" id="num_hidden_fields" value="' . $num_hidden_fields . '" />
							  </th>
							  <th>';
        foreach ($fields as $k => $field_name) {
            echo '<div class="row" id="' . $k . '"><div class="col-md-12"><input id="hidden-field-name" type="hidden" value="' . $field_name . '" /><input id="hidden-field-table" type="hidden" value="' . $tbl_nm . '" />' . $field_name . '
								  <span class="pull-right" style="padding:2px"><button class="btn btn-danger btn-xs" id="del-col-hidden" title="Delete ' . $field_name . '"><i class="fa fa-remove"></i> Delete</button></span>
								  <input type="hidden" id="' . $tbl_nm . '_hidden_fields" value="' . $table_hidden_fields . '" />
								  <input type="hidden" value="' . $k . '" />
								  </div></div>';
        }
        echo '</th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No Hidden Fields Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="hidden-form">
                                <div class="card">
                                    <div class="card-header">Add New Hidden Field</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Table Name</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="hidden[table]" id="hidden-table">
                                                    <option style="display:none" value="">Select Table...</option>
<?php
foreach ($this->tables as $k => $table_name) {
    //if (!in_array($table_name, $this->skipped_tables)) {
        echo '<option value="' . $table_name . '">' . $table_name . '</option>';
    //}
}
?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="hidden-fields" style="display:none">
                                                <label>Field Name <span class="text-danger">*</span><small class="text-warning">Hold Ctrl to Select Multiple Values</small></label>
                                                <select data-placeholder="Choose Fields..." multiple class="form-control" name="hidden[fields][]" id="hidden-field"></select>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="date">
                    <p></p>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="30%"><strong>Table Name</strong></td>
                                            <td><strong>Fields</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="date_shown_table_body">
<?php if ($this->date_fields) {
    $date_fields = $this->date_fields; ?>
    <?php
    ksort($date_fields);
    $num_date_fields = count($date_fields);
    foreach ($date_fields as $tbl_nm => $fields) {
        $table_date_fields = count($fields);
        echo '<tr id="' . $tbl_nm . '">
							  <th><input type="hidden" value="' . $tbl_nm . '" />' . $tbl_nm . '<br><span class=""><button class="btn btn-xs btn-danger" id="del-all-multiple" title="Delete All Fields From \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button></span>
							  <input type="hidden" id="num_date_fields" value="' . $num_date_fields . '" />
							  </th>
							  <th>';
        foreach ($fields as $k => $field_name) {
            echo '<div class="row" id="' . $k . '"><div class="col-md-12"><input id="multiple-field-name" type="hidden" value="' . $field_name . '" /><input id="multiple-field-table" type="hidden" value="' . $tbl_nm . '" />' . $field_name . '
								  <span class="pull-right" style="padding:2px"><button class="btn btn-danger btn-xs" id="del-col-multiple" title="Delete ' . $field_name . '"><i class="fa fa-remove"></i> Delete</button></span>
								  <input type="hidden" id="' . $tbl_nm . '_date_fields" value="' . $table_date_fields . '">
								  <input type="hidden" id="db_id" value="' . $k . '">
								  </div></div>';
        }
        echo '</th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No Date Picker Fields Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="multiple-form">
                                <div class="card">
                                    <div class="card-header">Add New Date Picker Field</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Table Name</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="date[table]" id="multiple-table">
                                                    <option style="display:none" value="">Select Table...</option>
                                        <?php
                                        foreach ($this->tables as $k => $table_name) {
                                            //if (!in_array($table_name, $this->skipped_tables)) {
                                                echo '<option value="' . $table_name . '">' . $table_name . '</option>';
                                            //}
                                        }
                                        ?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="multiple-fields" style="display:none">
                                                <label>Field Name <span class="text-danger">*</span><small class="text-warning">Hold Ctrl to Select Multiple Values</small></label>
                                                <select data-placeholder="Choose Fields..." multiple class="form-control" name="date[fields][]" id="multiple-field"></select>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="files">
                    <p></p>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="20%"><strong>Table Name</strong></td>
                                            <td><strong>Fields</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="file_shown_table_body">
<?php
if ($this->file_fields) {
    $file_fields = $this->file_fields;
    ksort($file_fields);
    $num_file_fields = count($file_fields);
    foreach ($file_fields as $tbl_nm => $fields) {
        $table_file_fields = count($fields);
        echo '<input type="hidden" id="num_file_fields" value="' . $num_file_fields . '" />';
        echo '<tr id="file_' . $tbl_nm . '">
							  <th><input type="hidden" value="' . $tbl_nm . '" />' . $tbl_nm . '<br><button class="btn btn-xs btn-danger" id="del-all-file" title="Delete All Fields From \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button></th>
							  <th><div class="row"><div class="col-lg-12">
							  <table class="table table-bordered table-hover">
							  <thead>
							  <tr class="active">
							  <th width="20%">Field Name</th>
							  <th width="15%">Type</th>
							  <th>Destination</th>
							  </tr>
							  </thead><tbody>';
        ksort($fields);
        foreach ($fields as $db_key => $field_info) {
            $p_class = 'text-success';
            $p_title = 'Directory Path';
            echo '	<tr id="' . $db_key . '">
									<td>' . $field_info['field'] . '</td>
									<td>' . $field_info['type'] . '</td>
									<td><div class="well ' . $p_class . '" title="' . $p_title . '" style="padding:5px">' . ($field_info['path']) . '<input id="file-db_id" type="hidden" value="' . $db_key . '" /><button class="btn btn-danger btn-xs pull-right" id="del-col-file" title="Delete ' . $field_info['field'] . '"><i class="fa fa-remove"></i> Delete</button>
									<input type="hidden" id="' . $tbl_nm . '_file_fields" value="' . $table_file_fields . '" />
									<input type="hidden" id="file_table_name" value="' . $tbl_nm . '" />
									</div>
									</td>
									</tr>';
        }
        echo '</tbody></table></div></div></th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No File Fields Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="file-form">
                                <div class="card">
                                    <div class="card-header">Add New Field Field</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Table Name</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="file[table]" id="file-table">
                                                    <option style="display:none" value="">Select Table...</option>
                                        <?php
                                        foreach ($this->tables as $k => $table_name) {
                                            //if (!in_array($table_name, $this->skipped_tables)) {
                                                echo '<option value="' . $table_name . '">' . $table_name . '</option>';
                                            //}
                                        }
                                        ?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="file-fields" style="display:none">
                                                <label>Field Name</label>
                                                <select data-placeholder="Choose Field..." class="form-control chosen-select" name="file[field]" id="file-field"></select>
                                            </div>
                                            <div id="folder-and-type" style="display:none">

                                                <label>Upload Type</label>
                                                <select data-placeholder="Choose Type..." class="form-control chosen-select" name="file[type]" id="file-type">
                                                    <option value="" style="display:none">Select Upload Type</option>
                                                    <option value="image">Image</option>
                                                    <option value="file">File</option>
                                                </select>

                                                <div id="file-allowed-image-exts" style="display:none">
                                                    <div class="panel panel-default" style="margin-top:10px;margin-bottom: 5px;">
                                                        <!-- Default panel contents -->
                                                        <div class="panel-heading">Select Allowed Image Types</div>
                                                        <div class="panel-body">
                                                            <div class="row" style="max-height:200px;overflow-y:scroll;overflow-x:hidden">
<?php
$allowed_exts_array = array('png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp');
sort($allowed_exts_array);
foreach ($allowed_exts_array as $k => $v) {
    echo '<div class="col-lg-4"><div class="checkbox">
						  <label>
							<input type="checkbox" value="' . $v . '" name="file[allowed_exts][]" />
							' . $v . '
						  </label>
						</div></div>';
}
?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="file-allowed-file-exts" style="display:none">
                                                    <div class="panel panel-default" style="margin-top:10px;margin-bottom: 5px;">
                                                        <!-- Default panel contents -->
                                                        <div class="panel-heading">Select Allowed File Types</div>
                                                        <div class="panel-body">
                                                            <div class="row" style="max-height:200px;overflow-y:scroll;overflow-x:hidden">
                                                                <?php
                                                                $allowed_exts_array = array('mp3', 'wav', 'wmv', 'pdf', 'doc', 'docx', 'txt', 'rtf', 'csv', 'ppt', 'xls', 'xml', 'aac', 'm3u', '3gp', 'mov', 'mp4', 'swf', 'zip', 'rar', 'gz');
                                                                sort($allowed_exts_array);
                                                                foreach ($allowed_exts_array as $k => $v) {
                                                                    echo '<div class="col-lg-4"><div class="checkbox">
						  <label>
							<input type="checkbox" value="' . $v . '" name="file[allowed_exts][]" />
							' . $v . '
						  </label>
						</div></div>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="panel panel-info" style="margin-top:5px;">
                                                                    <!-- Default panel contents -->
                                                                    <div class="panel-heading">Select Destination for uploads
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        Present Selected Folder:
                                                                        <div class="well" id="breadcrumb" style="padding:7px"></div>
                                                                        <input type="hidden" id="file-path" name="file[path]" />
                                                                        <p>
                                                                        <div id='directories_list'><div id="directory" style="display:none"></div></div>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="slug">
                    <p></p>
                    <div class="panel panel-default">
                        
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="30%"><strong>Table Name</strong></td>
                                            <td><strong>Fields</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="slug_shown_table_body">
<?php //pr($this->slug_fields); ?>
<?php if ($this->slug_fields) {
    $slug_fields = $this->slug_fields; ?>
    <?php
    ksort($slug_fields);
    $num_slug_fields = count($slug_fields);
    foreach ($slug_fields as $tbl_nm => $fields) {
        $table_slug_fields = count($fields);
        echo '<input type="hidden" id="num_slug_fields" value="' . $num_slug_fields . '" />';
        echo '<tr id="slug_' . $tbl_nm . '">
							  <th><input type="hidden" value="' . $tbl_nm . '" />' . $tbl_nm . '<br><span><button class="btn btn-xs btn-danger" id="del-all-slug" title="Delete All Fields From \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button></span>
							  <input type="hidden" id="' . $tbl_nm . '_slug_fields" value="' . $table_slug_fields . '" />
							  </th>
							  <th>
							  <div class="row"><div class="col-md-12">
							  <table class="table table-condensed table-bordered">
							  <tr class="active">
							  <th width="40%">Primary</th>
							  <th>Secondary</th>
							  </tr>';
        foreach ($fields as $k => $field_name) {
            echo '<tr id="' . $k . '">
								  <th>
								  <input id="slug-field-id" type="hidden" value="' . $k . '" />
								  ' . $field_name['main'] . '
								  </th>
								  <th>' . $field_name['secondary'] . '
								  <span class="pull-right" style="padding:2px">
								  <button class="btn btn-danger btn-xs" id="del-col-slug" title="Delete Slug Field"><i class="fa fa-remove"></i> Delete</button>
								  </span>
								  <input type="hidden" value="' . $tbl_nm . '" />
								  </th>
								  </tr>';
        }
        echo '</table>
							  </div></div>
							  </th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No Slug Fields Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="slug-form">
                                <div class="card">
                                    <div class="card-header">Add New Slug Field</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Table Name</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="slug[table]" id="slug-table">
                                                    <option style="display:none" value="">Select Table...</option>
                                        <?php
                                        foreach ($this->tables as $k => $table_name) {
                                            //if (!in_array($table_name, $this->skipped_tables)) {
                                                echo '<option value="' . $table_name . '">' . $table_name . '</option>';
                                            //}
                                        }
                                        ?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="slug-main-fields" style="display:none">
                                                <label>Main Field Name</label>
                                                <select class="form-control" name="slug[main]" id="slug-main-field"></select>
                                            </div>
                                            <div class="form-group" id="slug-secondary-fields" style="display:none">
                                                <label>Secondary Field Name</label>
                                                <select class="form-control" name="slug[secondary]" id="slug-secondary-field"></select>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="relationships">
                    <p></p>
                    <div class="panel panel-default">
                       
                        <div class="panel-body">
                            <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <td width="10%"><strong>Table</strong></td>
                                            <td><strong>Relationships</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody id="rel_shown_table_body">
<?php if ($this->get_another_data) {
    $get_another_data = $this->get_another_data; ?>
    <?php
    $get_data = array();
    $num_rel_fields = count($get_another_data);
    echo '<input type="hidden" id="num_rel_fields" value="' . $num_rel_fields . '" />';
    foreach ($get_another_data as $db_key => $data) {
        $get_data[$data['main_table']][$data['main_field']] = array('secondary_table' => $data['secondary_table'], 'secondary_field' => $data['secondary_field'], 'value' => $data['value'], 'db_key' => $db_key);
    }
    foreach ($get_data as $tbl_nm => $mainfields) {
        $table_rel_fields = count($mainfields);
        echo '<tr id="another_' . $tbl_nm . '">
							  <th><input type="hidden" value="' . $tbl_nm . '" />' . $tbl_nm . '<br><span><button class="btn btn-xs btn-danger" id="del-all-rel" title="Delete All Relations in \'' . $tbl_nm . '\'"><i class="fa fa-remove"></i> Delete All</button>
							  <input type="hidden" id="' . $tbl_nm . '_rel_fields" value="' . $table_rel_fields . '">
							  </span>
							  </th>
							  <th>
							  <div class="row"><div class="col-md-12">
							  <table class="table table-condensed table-bordered">
							  <tr class="active">
							  <th width="20%">Main Field</th>
							  <th>Secondary Table</th>
							  <th>Secondary Field</th>
							  <th>Select Value</th>
							  <th>Delete</th>
							  </tr>';
        foreach ($mainfields as $field_name => $secondary_array) {
            echo '<tr id="' . $secondary_array['db_key'] . '">
								  <th>
								  <input id="db-key" type="hidden" value="' . $secondary_array['db_key'] . '" />
								  ' . $field_name . '
								  </th>
								  <th>' . $secondary_array['secondary_table'] . '</th>
								  <th>' . $secondary_array['secondary_field'] . '</th>
								  <th>' . $secondary_array['value'] . '</th>
								  <th>
								  <button class="btn btn-danger btn-xs" id="del-col-rel" title="Delete Relation"><i class="fa fa-remove"></i> Delete</button>
								  <input type="hidden" value="' . $tbl_nm . '" />
								  </th>
								  </tr>';
        }
        echo '</table>
							  </div></div>
							  </th>
							  </tr>';
    }
} else {
    echo '<tr class="danger"><th colspan="2">No Relationships Found</th></tr>';
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 col-lg-4" id="slug-form" >
                                <div class="card">
                                    <div class="card-header">Add New Relationship</div>
                                    <div class="card-block">
                                        <form role="form" method="post">
                                            <div class="form-group">
                                                <label for="">Main Table</label>
                                                <select data-placeholder="Select Table..." class="form-control chosen-select" name="rel[main_table]" id="rel-main-table">
                                                    <option style="display:none" value="">Select Table...</option>
                                        <?php
                                        foreach ($this->tables as $k => $table_name) {
                                            //if (!in_array($table_name, $this->skipped_tables)) {
                                                echo '<option>' . $table_name . '</option>';
                                            //}
                                        }
                                        ?>
                                                </select>
                                            </div>
                                            <div class="form-group" id="rel-main-fields" style="display:none;">
                                                <label>Main Field</label>
                                                <select class="form-control" name="rel[main_field]" id="rel-main-field"></select>
                                            </div>
                                            <div class="form-group" id="rel-secondary-tables" style="display:none;">
                                                <label>Secondary Table</label>
                                                <select class="form-control" name="rel[secondary_table]" id="rel-secondary-table"></select>
                                            </div>
                                            <div class="form-group" id="rel-secondary-fields" style="display:none;">
                                                <label>Secondary Field</label>
                                                <select class="form-control" name="rel[secondary_field]" id="rel-secondary-field"></select>
                                            </div>
                                            <div id="data" style="display:none"></div>
                                            <div class="form-group" id="rel-select-fields" style="display:none;">
                                                <label>Secondary Field Value to be Passed</label>
                                                <select class="form-control" name="rel[select_field]" id="rel-select-field"></select><br />
                                                <label><input type="checkbox" class="form-group" style="margin-left:5px" name="rel[is_multiple]" value="1" />&nbsp;&nbsp;Allow Multiple Select</label>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-plus"></i> &nbsp;ADD</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
                
            </div>
            
        </section>
        
    </div>
</section>

<?php echo $this->render("themes/admin/html/elements/footer.php") ?>
