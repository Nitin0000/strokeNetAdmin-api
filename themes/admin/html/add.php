<?php echo $this->render("themes/admin/html/elements/header.php") ?>

<?php $now = new DateTime(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>

<link type="text/css" rel="stylesheet" href="<?php echo backoffice_url ?>/libs/jquery-ui/jquery-ui.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.min.css" />


<link href="<?php echo backoffice_url ?>/themes/admin/assets/css/style.css" rel="stylesheet">
        
<script type="text/javascript" src="<?php echo backoffice_url ?>/libs/jquery-ui/jquery-ui.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />


<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css" rel="stylesheet">

<script>
    $(document).ready(function () {
        var slug = function (str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = 'ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;';
            var to = 'aaaaaeeeeeiiiiooooouuuunc------';
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                    .replace(/\s+/g, '-') // collapse whitespace and replace by -
                    .replace(/-+/g, '-'); // collapse dashes

            return str;
        };

<?php
$fields = array();

foreach ($this->columns as $key => $value) {
    $field = $value['Field'];
    $fields[] = $field;
}

// Check if Table Exists in Required Fields Array
if (array_key_exists($this->table, $this->required_fields)) {
    $required_fields = $this->required_fields[$this->table];
} else {
    $required_fields = $this->required_fields;
}

// Check if Table Exists in File Fields Array
$file_fields_names = array();
if (array_key_exists($this->table, $this->file_fields)) {
    $file_fields = $this->file_fields[$this->table];
    foreach ($file_fields as $db_key => $field_info) {
        $file_fields_names[$db_key] = $field_info['field'];
    }
} else {
    $file_fields = $this->file_fields;
}

// Validation of Reqired Fields Array
foreach ($required_fields as $required) {

    if (in_array($required, $fields)) {
        echo "
                $('#" . $required . "').attr('data-trigger','change');
                $('#" . $required . "').attr('data-required','true');
                $('#" . $required . "').addClass('parsley-validated');
                        ";
    }
}


// Check if Table Exists in Slug Fields
if (array_key_exists($this->table, $this->slug_fields)) {
    $slug_fields = $this->slug_fields[$this->table];
} else {
    $slug_fields = "";
}

if (array_key_exists($this->table, $this->date_fields)) {
    $date_fields = $this->date_fields[$this->table];
} else {
    $date_fields = array();
}

foreach ($slug_fields as $key => $value) {
    //pr($slug_fields);

    if (array_key_exists('main', $value)) {
        echo "

                $('#" . $value['secondary'] . "').attr('readonly','readonly');
                $('#" . $value['secondary'] . "').parent().parent().hide();

                $('#" . $value['main'] . "').on('keyup',function(){
                        var slugval = slug($('#" . $value['main'] . "').attr('value'));
                        $('#" . $value['secondary'] . "').attr('value',slugval);
                });";
    }
};

//relationship fields
$rel_main_fields_array = array();
foreach ($this->get_another_data as $db_id => $rel_info) {
    
    if ($rel_info['is_multiple'] == 1)
        $rel_main_fields_array[] = $rel_info['main_field'];
}
?>
    });
</script>
<script>

    $(document).ready(function () {
        $(".chosen-select").chosen();
    });
</script>

<section class="row">
    <div class="col-sm-12">
        <section class="row">

            <div class="col-md-12 col-lg-12">
                <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>
                    Add
                </h1>
                <hr>
            </div>

            <div class="col-md-12 col-lg-12">
                <?php if (is_array($this->error_message)) { ?>
                    <div class="alert alert-danger" style="text-align: left;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <div class="panel-heading"><strong>Warning!</strong></div>
                        <div class="panel-body">
                            <ul class="" type="circle">
                                <?php foreach ($this->error_message as $k => $err) { ?>
                                    <li><?= $err ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <hr />
                <?php } ?>
                <?php
                if (isset($this->vars[3]) && $this->vars[3] == "error") {
                    ?>
                    <div class="alert alert-danger error">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Error!</strong> Record: <?php echo $this->record_id; ?> was not  updated!. <strong>Reason</strong>: <?php echo $this->vars[4]; ?>
                    </div>
                <?php } ?>    


                <form class="bs-example form-horizontal" action="" name="csrf_form" method="post" enctype="multipart/form-data" data-validate="parsley">
                    <fieldset>
                        <input type="hidden" name="csrf_token" value="<?php echo $this->formtoken; ?>">

                        <?php
                        $keys = array_keys($this->custom_data);
                        //pr($keys);
                        foreach ($this->columns as $key => $value) {

                            $default_field_type = $value['Type'];
                            $field = $value['Field'];

                            if (strstr($value['Type'], '(')) {
                                $field_type = strstr($value['Type'], '(', true);
                            } else {
                                $field_type = $value['Type'];
                            }
                            // Check if Table Exists in Hidden Fields Array
                            if (array_key_exists($this->table, $this->hidden_fields)) {
                                $hidden_fields = $this->hidden_fields[$this->table];
                            } else {
                                $hidden_fields = $this->hidden_fields;
                            }

                            if ($field != $this->tb_primaryid and ! in_array($field, $hidden_fields)) {
                                ?>
                                <div class="form-group" id="group_<?php echo $value['Field']; ?>">
                                    <label for="input_<?php echo $value['Field']; ?>" title="Table Field Name :'<?php echo $value['Field']; ?>'" class="col-lg-4 control-label"><b><?php echo format_names($value['Field']); ?></b></label>
                                    
                                    <?php if($value['Field'] == "page_content") { ?>
                                    
                                    <div class="col-lg-12">
                                    
                                    <?php } else {?>
                                        <div class="col-lg-5">

                                    <?php }?>
                                        
                                        <?php if ($field_type == 'int' || $field_type == "varchar" || $field_type == "bigint" || $field_type == "float") { ?>

                                            <?php
                                            if (in_array($value['Field'], $keys)) {
                                                $multiple_true = "";
                                                $array_true = "";
                                                if (in_array($value['Field'], $rel_main_fields_array)) {
                                                    $multiple_true = "multiple";
                                                    $array_true = "[]";
                                                }
                                                
                                                ?>

                                                <select data-placeholder="Choose an option..." <?php echo $multiple_true; ?> class="form-control chosen-select" style="width:400px;" name="data[<?php echo $value['Field']; ?>]<?php echo $array_true; ?>" id="<?php echo $value['Field']; ?>">  
                                                    <option value="" hidden>Choose an option...</option>
                                                    <?php foreach ($this->custom_data[$value['Field']]['data'] as $key_option => $val_option) { ?>
                                                        <option value="<?php echo $val_option[$this->custom_data[$value['Field']]['attributes']['value']]; ?>"><?php echo $val_option[$this->custom_data[$value['Field']]['attributes']['seconday_field']]; ?></option>
                                                    <?php } ?> 

                                                </select>




                                            <?php } else if ($field == "password") { ?>
                                                <input type="password" name="data[<?php echo $value['Field']; ?>]" class="form-control" id="<?php echo $value['Field']; ?>" placeholder="" value="" autocomplete="off">


                                            <?php } else if (in_array($field, $date_fields)) {
                                                ?>

                                                <div class="input-group">
                                                    <div class="input-group-addon" style="background:#eee"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="data[<?php echo $value['Field']; ?>]" class="form-control" id="<?php echo $value['Field']; ?>" placeholder=""  value="">
                                                </div>
                                                <script>
                                                    $(function () {

                                                        $("#<?php echo $value['Field']; ?>").datepicker({
                                                            dateFormat: "yy-mm-dd",
                                                            changeMonth: true,
                                                            changeYear: true,
                                                            showButtonPanel: true
                                                        });


                                                    });
                                                </script>

                                                <?php
                                            } else if (in_array($field, $file_fields_names)) {
                                                $db_key = '';
                                                foreach ($file_fields_names as $db => $file_field_name) {
                                                    if ($field == $file_field_name) {
                                                        $db_key = $db;
                                                    }
                                                }
                                                $file_field_info = $file_fields[$db_key];
                                                if ($file_field_info['type'] == "image") {
                                                    ?>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail">
                                                            <img id="<?php echo $value['Field']; ?>" src="http://placehold.it/270x200&text=No Image" alt="...">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;"></div>
                                                        <div>
                                                            <span class="btn-file"><span class="fileinput-new btn btn-primary"><i class="fa fa-plus"></i> Select image</span><span class="fileinput-exists  btn btn-success"><i class="fa fa-refresh"></i> Change</span><input type="file" name="data[<?php echo $value['Field']; ?>]"></span>
                                                            <a href="#" style="margin-top:5px;" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"><i class="fa fa-remove"></i> Remove</a>
                                                        </div>
                                                    </div>
                                                <?php } elseif ($file_field_info['type'] == "file") { ?>


                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn-file"><span class="fileinput-new btn btn-primary">Select file</span><span class="fileinput-exists btn btn-danger">Change</span><input type="file" name="data[<?php echo $value['Field']; ?>]"></span>
                                                        <span class="fileinput-filename"></span>
                                                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                                    </div>


                                                <?php
                                                }
                                            } else {
                                                ?>


                                                <input type="text" name="data[<?php echo $value['Field']; ?>]" class="form-control" id="<?php echo $value['Field']; ?>" placeholder="" value="" autocomplete="off">

            <?php } ?>




                                            <?php
                                        } else if ($field_type == 'text' || $field_type == 'longtext') {

                                            // Check if Table Exists in Editor Fields
                                            if (array_key_exists($this->table, $this->ckeditor_fields)) {
                                                $ckeditor_fields = $this->ckeditor_fields[$this->table];
                                            } else {
                                                $ckeditor_fields = $this->ckeditor_fields;
                                            }
                                            //Put the fields												
                                            if (in_array($field, $ckeditor_fields)) {
                                                ?>
                                                <textarea class='form-control ckeditor' id='<?php echo $value['Field']; ?>' cols='80'  rows='25'  name='data[<?php echo $value['Field']; ?>]'></textarea>







                                                <?php
                                            } elseif (in_array($value['Field'], $keys)) {
                                                $multiple_true = "";
                                                $array_true = "";
                                                if (in_array($value['Field'], $rel_main_fields_array)) {
                                                    $multiple_true = "multiple";
                                                    $array_true = "[]";
                                                }
                                                ?>

                                                <select data-placeholder="Choose an option..." <?php echo $multiple_true; ?> class="form-control chosen-select" style="width:400px;" name="data[<?php echo $value['Field']; ?>]<?php echo $array_true; ?>" id="<?php echo $value['Field']; ?>">  
                                                    <option value="" hidden>Choose an option...</option>
                                                    <?php foreach ($this->custom_data[$value['Field']]['data'] as $key_option => $val_option) { ?>
                                                        <option value="<?php echo $val_option[$this->custom_data[$value['Field']]['attributes']['value']]; ?>"><?php echo $val_option[$this->custom_data[$value['Field']]['attributes']['seconday_field']]; ?></option>
                <?php } ?> 

                                                </select>




                                                <?php
                                            } else if (in_array($field, $file_fields_names)) {
                                                $db_key = '';
                                                foreach ($file_fields_names as $db => $file_field_name) {
                                                    if ($field == $file_field_name) {
                                                        $db_key = $db;
                                                    }
                                                }
                                                $file_field_info = $file_fields[$db_key];
                                                if ($file_field_info['type'] == "image") {
                                                    ?>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                                            <img id="<?php echo $value['Field']; ?>" style="padding: 3px; border:1px solid #ccc;" src="http://placehold.it/200x200&text=No Image" alt="...">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;"></div>
                                                        <div>
                                                            <span class="btn btn-success btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="data[<?php echo $value['Field']; ?>]"></span>
                                                            <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                        </div>
                                                    </div>
                <?php } elseif ($file_field_info['type'] == "file") { ?>


                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn-file"><span class="fileinput-new btn btn-success">Select file</span><span class="fileinput-exists btn btn-danger">Change</span><input type="file" name="data[<?php echo $value['Field']; ?>]"></span>
                                                        <span class="fileinput-filename"></span>
                                                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                                    </div>


                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <textarea class="form-control" rows="3" name="data[<?php echo $value['Field']; ?>]" id="<?php echo $value['Field']; ?>"></textarea>


                                            <?php } ?>


                                            <?php
                                        } else if ($field_type == 'enum') {
                                            $val = str_replace("enum(", "", $default_field_type);
                                            $val = str_replace(")", "", $val);
                                            $val = str_replace("'", "", $val);
                                            $values = explode(",", $val);
                                            ?>
                                            <?php
                                            foreach ($values as $key => $val1) {
                                                if ($val1 == "1") {
                                                    ?>
                                                    <div class="radio">
                                                        <label class="radio">
                                                            <input checked type="radio" name="data[<?php echo $value['Field'] ?>]" id="<?php echo $value['Field'] ?>_<?php echo $val1; ?>" value="<?php echo $val1; ?>">
                                                            &nbsp;&nbsp; <?php 
                                                            if($val1 == "1"){
                                                                echo "Active";
                                                            }elseif($val1 == "0"){
                                                                echo "Inactive";
                                                            }else{
                                                                echo format_names($val1);
                                                            }
                                                             ?>
                                                        </label>
                                                    </div>
                <?php } else { ?>
                                                    <div class="radio">
                                                        <label class="radio">
                                                            <input type="radio" name="data[<?php echo $value['Field'] ?>]" id="<?php echo $value['Field'] ?>_<?php echo $val1; ?>" value="<?php echo $val1; ?>">
                                                            &nbsp;&nbsp; <?php 
                                                            if($val1 == "1"){
                                                                echo "Active";
                                                            }elseif($val1 == "0"){
                                                                echo "Inactive";
                                                            }else{
                                                                echo format_names($val1);
                                                            }
                                                             ?></label>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>

        <?php } else if ($field_type == 'timestamp' || $field_type == 'datetime' || $field_type == 'date') { ?>

                                            <input type="text" name="data[<?php echo $value['Field']; ?>]" class="form-control datetimepicker1" id="<?php echo $value['Field']; ?>" placeholder=""  value="<?php echo $now->format('Y-m-d H:i') ?>">


                                        <?php } else { ?>
                                            Need to Add Widget (<?php echo $field_type; ?>)
                                <?php } ?>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                        ?>
                        <div class="form-group" style="background:#eeeeee; margin-left:0px; padding:15px;">
                            <div class="col-lg-12" style="text-align:left">
                                <button type="submit" id="SubmitButton" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                                <a href="<?php echo _admin_url; ?>/table/<?php echo $this->vars[2]; ?>" class="btn btn-info"><i class="fa fa-chevron-left"></i> Cancel and go Back</a>
                            </div>
                        </div>
                    </fieldset>
                </form>  

            </div>
        </section>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>
<script>
    $(document).ready(function () {
        $(function () {
            $('.datetimepicker1').datetimepicker({
                format: 'Y-m-d H:i',
                formatTime: 'g:i a',
                 minDate:'0',//yesterday is minimum date(for today use 0 or -1970/01/01)
            });
        });
    });
</script>    
<?php echo $this->render("themes/admin/html/elements/footer.php") ?> 