<?php echo $this->render("themes/admin/html/elements/header.php") ?>
<script>
    $(document).ready(function () {
        $(".savefields").click(function () {
            var table_name = $(this).attr("table_name");
            var items = [];
            var field_item = $("#" + table_name + " input[type=checkbox]").each(function () {
                if (this.checked) {
                    var it_val = $(this).val();
                    items.push(it_val);
                }
            });
            console.log(table_name + " : " + items);
            $.post('<?php echo _admin_url; ?>/common/manage_fields', {table_name: table_name, fields: items}, function (data) {
                console.log(data);
                $("#success_message .messagetext").text("Fields for [" + table_name + "] were sucessfully Saved!");
                $("#success_message").show();
                setTimeout(function () {
                    $('#success_message').hide(100);
                }, 3000);
            });
        });
        $(".saveallfields").click(function () {
            var aftertext = $(".savefields").trigger('click');
            $("#success_message2 .messagetext").text("All fields were sucessfully Saved!");
            $("#success_message2").show();
            setTimeout(function () {
                $('#success_message2').hide(300);
            }, 5000);
        });
    });
</script>
<section class="row">
    <div class="col-sm-12">
        <section class="row">
            
            <div class="col-md-12 col-lg-12">
                <a href="javascript:void(0)" class="btn btn-danger pull-right saveallfields"><i class="fa fa-save"></i> &nbsp;Save All Fields</a>
                
                    <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>Icon Management</h1>   
                    <p style='margin:0px;'>Manage icons for your table in sidebar</p>
                    <hr>
            </div>
            
            <div class="col-md-12 col-lg-12">
                <div class="alert alert-success" id="success_message" style="display:none">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> <span class="messagetext"></span> 
                </div>
                <div class="alert alert-success" id="success_message2" style="display:none">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> <span class="messagetext"></span> 
                </div>
                
            </div>
            <div class="col-md-12 col-lg-12">
                <table class="table table-striped table-hover table-bordered" style="font-size:120%;">
        <tbody>
            <tr>
                <td> <strong>Table Name</strong></td>
                <td>Fields</td>
                <td></td>
            </tr>
            <?php //pr($this->db_fields);
            ?>
            <?php foreach ($this->manage_fields as $key => $value) {
                ?>
                <?php if (!in_array($key, $this->skipped_tables)) { ?>
                    <tr class="tablefieldinfo" table_name="<?php echo $key; ?>">
                        <td width="120"><strong><?php echo $key; ?> </strong></td>
                        
                        
                        <td id="<?php echo $key; ?>" style="word-wrap: break-word">
                            <?php
                            foreach ($value as $key1 => $value) {
                                $values = unserialize($this->db_fields[$key]);
                                if (isset($values[0]) && $values[0]) {
                                    $values[0] = $values[0];
                                } else {
                                    $values[0] = array();
                                }
                                if (in_array($value['Field'], $values[0])) {
                                    ?>
                                    <label class="custom-control custom-checkbox" style="margin-left:0px; margin-right:20px;float:none; line-height:30px;">
                                        <input type="checkbox" class="custom-control-input" checked="checked" field_name="<?php echo $value['Field']; ?>" value="<?php echo $value['Field']; ?>">
                                        
                                        <span class="custom-control-indicator"></span>
  <span class="custom-control-description"><?php echo $value['Field']; ?></span>
                                        
                                    </label>
                                <?php } else {
                                    ?>
                            
                                <label class="custom-control custom-checkbox" style="margin-left:0px; margin-right:20px;float:none; line-height:30px;">
                                        <input type="checkbox" class="custom-control-input" field_name="<?php echo $value['Field']; ?>" value="<?php echo $value['Field']; ?>">
                                        
                                        <span class="custom-control-indicator"></span>
  <span class="custom-control-description"><?php echo $value['Field']; ?></span>
                                        
                                    </label>
                            
<!--                                    <label class="checkbox-inline" style="margin-left:0px; margin-right:20px;float:none; line-height:30px;">
                                        <input type="checkbox" id="inlineCheckbox1" field_name="<?php echo $value['Field']; ?>" value="<?php echo $value['Field']; ?>">&nbsp;&nbsp;<?php echo $value['Field']; ?></label>-->
                                        <?php
                                    }
                                }
                                ?>
                        </td>
                        
                        <td width="120">
                            
                            <a style="margin-left:30px;" href="javascript:void(0)" class="btn btn-small btn-success savefields pull-right" table_name="<?php echo $key; ?>">Save Fields</a>
                        </td>
                        
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
                
            </div>
            
        </section>
    </div>
</section>

<?php echo $this->render("themes/admin/html/elements/footer.php") ?>