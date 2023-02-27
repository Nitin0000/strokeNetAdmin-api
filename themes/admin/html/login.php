<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo _admin_url;?>/assets/images/favicon.ico">
        <title>Login</title>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo backoffice_url ?>/themes/admin/assets/dist/css/bootstrap.min.css" rel="stylesheet">
        <!--Fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<link href="http://fonts.googleapis.com/css?family=Poppins:600%2C300%2C400|Roboto:900" rel="stylesheet" property="stylesheet" type="text/css" media="all">
        <!-- Icons -->
        <link href="<?php echo backoffice_url ?>/themes/admin/assets/css/font-awesome.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="<?php echo backoffice_url ?>/themes/admin/assets/css/style.css" rel="stylesheet">
    </head>
    <body style="padding-top:10%; background: #7C7CA3;">
        <div class="container-fluid" id="wrapper">
                        
            <div class="row">
                <form class="form-signin" id='parsleyForm' data-parsley-validate method="POST" action="<?php echo _admin_url; ?>/login" name="csrf_form">
                     <input type="hidden" name="csrf_token" value="<?php echo $this->formtoken; ?>">
                     
                    <center>
                        <?php
                        if (website_logo) {
                            ?>
                            <a href="<?php echo backoffice_url; ?>/admin" class="logo"><img src="<?php echo backoffice_url; ?><?php echo website_logo ?>" width="80%" style="text-align: center;" alt=""></a><?php } else { ?>
                            Administration
                                <?php } ?>
                            </center>
                    <hr>
                    
                    <?php if(isset($this->errors[0])){?>
                     <div class="alert alert-danger" role="alert">
                        <?php echo $this->errors[0];?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                    <?php }?>
                    
                    <p class="form-signin-heading text-center">Enter your details to login</p>
                    
                    <div class="form-group">
                    <label for="inputEmail" class="sr-only">Email address</label>
                    <input style="padding: 15px;" type="text" name="data[username]" autocomplete="off" data-required="true" id="inputEmail" class="form-control input-lg" placeholder="Username or Email Address" required autofocus>
                    </div>
                    <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" name="data[password]" autocomplete="off" data-required="true" id="inputPassword" class="form-control" placeholder="Password" required>
                    </div>
                    <br>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                  </form>
            </div>
        </div>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="<?php echo backoffice_url ?>/themes/admin/assets/js/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="<?php echo backoffice_url ?>/themes/admin/assets/dist/js/bootstrap.min.js"></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.5.0/parsley.min.js'></script>
        
        <script>
            Parsley.options.errorClass = 'has-danger'
            Parsley.options.successClass = 'has-success'
            Parsley.options.classHandler = function(f) { return f.$element.closest('.form-group'); }
            Parsley.options.errorsWrapper = '<div class="form-control-feedback"></div>'
            Parsley.options.errorTemplate = '<div></div>'
          </script>
          
        
    </body>
</html>