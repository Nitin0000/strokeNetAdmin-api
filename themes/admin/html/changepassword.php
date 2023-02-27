<?php echo $this->render("themes/admin/html/elements/header.php") ?>

<section class="row">
    <div class="col-sm-12">
        <section class="row">

            <div class="col-md-12 col-lg-12">
                <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>
                    Change Password
                </h1>
<!--                <p class="hidden-sm-down" style='margin:0px;'>Manage your website settings &nbsp;</p>-->
                <hr>
            </div>
            
            <div class="col-12 col-md-12 col-lg-12">

                <?php if ($this->errors) { ?>
                    <div class="alert alert-danger">
                        <a class="close" data-dismiss="alert" href="#">&times;</a>
                        <?php
                        foreach ($this->errors as $msg) {
                            echo "<li style='list-style:circle'>" . $msg . "</li>";
                        }
                        ?>
                    </div>
<?php } ?>
                    <?php if ($this->success) { ?>
                    <div class="alert alert-success">
                        <a class="close" data-dismiss="alert" href="#">&times;</a>
                        <?php
                        foreach ($this->success as $msg) {
                            echo "<li style='list-style:circle'>" . $msg . "</li>";
                        }
                        ?>
                    </div>
<?php } ?>
                    <form class="form-horizontal col-lg-8" action="<?php echo _admin_url; ?>/changepassword" method="post" role="form" data-validate="parsley">
                        <div class="form-group">
                            <label for="inputEmail1" class="col-lg-6 control-label">Old Pasword</label>
                            <div class="col-lg-6">
                                <input type="password" class="form-control" name="data[old_password]" id="old_password" data-required="true" />
                            </div>
                        </div>    <div class="form-group">
                            <label for="inputEmail1" class="col-lg-6 control-label">New Pasword</label>
                            <div class="col-lg-6">
                                <input type="password" class="form-control"  name="data[new_password]" id="new_password" data-required="true" />
                            </div>
                        </div>    <div class="form-group">
                            <label for="inputEmail1" class="col-lg-6 control-label">Repeat Pasword</label>
                            <div class="col-lg-6">
                                <input type="password" class="form-control" name="data[repeat_password]" id="repeat_password" data-required="true" data-equalto="#new_password" />
                            </div>
                        </div>    
                        <br>
                        <div class="form-group" >
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Save Password</button>
                            </div>
                        </div> 
                    </form>
                
            </div>
            
        </section>
        </div>
</section>

<?php echo $this->render("themes/admin/html/elements/footer.php") ?> 