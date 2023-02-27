<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo main_url; ?>/assets/images/favicon.ico">
        <title><?php echo $this->page_title; ?></title>
        <link href="<?php echo backoffice_url ?>/themes/admin/assets/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" />

        <link href="<?php echo backoffice_url ?>/themes/admin/assets/css/font-awesome.css" rel="stylesheet">
        <link href="<?php echo backoffice_url ?>/themes/admin/assets/css/style.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/responsive/2.2.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.0.5/sweetalert2.css" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.mcustomscrollbar/3.0.6/jquery.mCustomScrollbar.min.css" />
        
<!--        <script src="//cdn.jsdelivr.net/jquery.mcustomscrollbar/3.0.6/jquery.mCustomScrollbar.concat.min.js"></script>-->
       
        
    </head>
    <body>
        <div class="container-fluid" id="wrapper">
            <div class="row">
                <nav class="sidebar col-xs-12 col-sm-4 col-lg-3 col-xl-2 sidebar-style-1" style="padding-top: 0px;">

                     <h1 class="site-title" style="padding: 0px; margin: 0px;border-radius: 0px; background: #fff; border: 0px;">
                        <img class='logo-image' width="90%" style="margin-left: 10px; margin-top: 10px;" src='<?php echo backoffice_url; ?><?php echo website_logo ?>' />
                    </h1> 

                    <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><em class="fa fa-bars"></em></a>

                    <ul class="nav nav-pills flex-column sidebar-nav">

                        
                        <div class='card card-dark hidden-xs-down' style='padding-left: 15px; padding-top:15px; padding-right: 15px; border-radius: 0px;  border: 0px;background: #fff; text-align: left'>
                            <h4 class='text-white'>
                                <small style='color: #ccc; font-size: 14px;'>Welcome back,</small> 
                                <br>
                                <b style="text-transform: uppercase; font-weight: bold;font-size: 20px;color: #7C7CA3;">
                                    <?php echo $_SESSION['admin_name']; ?>
                                </b>
                            </h4>
                            
                        </div>

                        <?php if ($_SESSION['admin_type'] == "super_admin") { ?>
                        
                        <?php 
		if(is_array($this->table_icons))
		{
			$table_icons=$this->table_icons;
			foreach($this->tables as $table){
				if(!in_array($table,$this->skipped_tables))
				{
					$iconclass=$table_icons[$table];
				?>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php
                            if ($this->vars[2] == $table) {
                                echo 'active';
                            }
                                        ?>" href="<?php echo _admin_url;?>/table/<?php echo $table; ?>"><em class="fa  <?php echo $iconclass;?>"></em> &nbsp;&nbsp;<?php echo format_names($table);?></a>
                        </li>
                        
                        <?php } } } ?>
                        <!-- <li class="nav-item">
                            <a class="nav-link <?php
                            if ($this->vars[2] == "website_settings") {
                                echo 'active';
                            }
                                        ?>" href="<?php echo _admin_url;?>/website_settings"><em class="fa  <?php echo "fa-desktop";?>"></em> &nbsp;&nbsp;<?php echo format_names("website_settings");?></a>
                        </li> -->
                        <?php } ?>
                       
                        
                        <li class="nav-item"><a class="nav-link <?php if ($this->vars[1] == 'index') { echo 'active';}?>" href="<?php echo _admin_url;?>/change_password"><em class="fa fa-refresh"></em> &nbsp;&nbsp;Change Password</a></li>
                                        
                        <li class="nav-item"><a class="nav-link" href="<?php echo _admin_url;?>/logout"><em class="fa fa-power-off"></em> &nbsp;&nbsp;Sign out</a></li>

                        
                    </ul>
                    
                </nav>

                 <script>
//                    $(document).ready(function(){
//                        $(window).on("load",function(){
//                            $(".sidebar").mCustomScrollbar({
//                                theme: "dark"
//                            });
//                        });
//                    });
                </script>
                
                <main class="col-xs-12 col-sm-8 offset-sm-4 col-lg-9 offset-lg-3 col-xl-10 offset-xl-2 pt-3 pl-4">


