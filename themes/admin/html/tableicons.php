<?php echo $this->render("themes/admin/html/elements/header.php") ?>
<?php
    $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
    $subject = file_get_contents(getcwd() . '/themes/admin/assets/css/font-awesome.css');

    preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
    $icons = array();

    foreach ($matches as $match) {
        $icons[$match[1]] = $match[2];
    }
    $icons = array_keys($icons);
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('tr#clicked-row').on("click",function () {
            //show icons div
            $('div#all-icons').show();
            //make clicked row as active
            $('tr#clicked-row').removeClass("active");
            $(this).addClass("active");
            //get value of clicked table
            var clicked_table = $(this).find('input#table-name').val();
            //save value of clicked table in idden field in icons
            $('input#clicked-table').val(clicked_table);
            $('span#selected-table').text(clicked_table);

        });
        $('i#icon').click(function () {
            //get value of table in which icon is to be applied
            var clicked_table = $(this).parent().parent().parent().parent().parent().parent().parent().parent().find('input#clicked-table').val();
            var selected_icon = $(this).prev().val();
            $('tr#clicked-row').find('input#' + clicked_table).val(selected_icon);
            $('tr#clicked-row').find('input#' + clicked_table).parent().parent().addClass("warning");
            $('tr#clicked-row').find('i#' + clicked_table).prop("class", "fa "+selected_icon);
            $('tr#save-row').show();
        });
        $('tr#allicon-row').on("click",function () {
            $(this).find('i#icon').click();
        });
    });
</script>

<section class="row">
    <div class="col-sm-12">
        <section class="row">
            
            <div class="col-md-12 col-lg-12">
                    <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>Icon Management</h1>                      <p style='margin:0px;'>Manage icons for your table in sidebar</p>
                    <hr>
            </div>
            
            <div class="col-md-12 col-lg-12">
                <?php
                     if (isset($this->vars[2]) and $this->vars[2] == "success") {
                 ?>
                <div class="alert alert-success" role="alert">
                    <a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>
                    <strong>Well done!</strong> Successfully Updated Table Icons!
                </div>
                <?php } ?>
            </div>  
            
            
            <div class="col-md-4 col-lg-4">
               
                <form method="post">
                    <table align="center" class="table table-hover table-bordered">
                        <thead>
                            <tr id="save-row" style="display: none;">
                                <th colspan="3"><input class="btn btn-success btn-bar btn-block" style="font-size:16px" type="submit" value="SAVE ALL CHANGES" /></th>
                            </tr>
                            <tr>
                                <th>S.No</th>
                                <th>Table Name</th>
                                <th>ICON</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($this->table_icons as $table_name => $icon) {
                                $tbl_name = format_names($table_name);
                                ?>
                                <tr class="" id="clicked-row" style="cursor:pointer">
                                    <td>
                                        <input type="hidden" id="table-name" value="<?= $table_name ?>" />
                                        <input type="hidden" id="<?= $table_name ?>" name="table[<?= $table_name ?>]" value="<?= $icon ?>" />
                                        <?= $i ?>
                                    </td>
                                    <td><?= $tbl_name ?></td>
                                    <td><i id="<?= $table_name ?>" class="fa <?= $icon ?>"></i></td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
                

            </div>            
            
            <div class="col-md-8 col-lg-8">
               
                <div id="all-icons" class="col-lg-8" style="display: none;">

<!--                    <h3>Select Icon for <span id="selected-table" ></span><hr /></h3>-->
                        
                        <input type="hidden" id="clicked-table" />
                        <div class="well well-small" style="background:#ffffff;height:470px;overflow-y:scroll;overflow-x:hidden">
                            <div class="row" style="padding:5px">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover">                           
                                        <tbody>
                                            <?php
                                            foreach ($icons as $k => $icon_class) {
                                                echo '<tr id="allicon-row" class="col-md-4" style="padding:10px;cursor:pointer"><th><input type="hidden" value="' . $icon_class . '"> <i id="icon" class="fa ' . $icon_class . '" ></i> <td> ' . $icon_class . ' </td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                
            </div> 
            
        </section>
    </div>
</section>

<?php
echo $this->render("themes/admin/html/elements/footer.php")?>