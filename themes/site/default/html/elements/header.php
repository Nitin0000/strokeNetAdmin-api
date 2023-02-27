<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />

        <!-- METADATA -->
        <meta name="description" content="<?php echo site_name; ?>" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

        <!-- PAGE TITLE -->
        <title><?php echo $this->page_title; ?> | <?php echo site_name; ?></title>

        <!-- All meta content -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width"><title></title>
        <meta name="title" content="<?php echo $this->page_title; ?>"  />
        <meta name="description" content="<?php echo $this->page_description; ?>"  />
        <meta property="og:title" content="<?php echo $this->page_title; ?>"/> 

        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?php echo $this->page_title; ?>">
        <meta itemprop="description" content="<?php echo $this->page_description; ?>">
        <meta itemprop="image" content="<?php echo $this->page_image; ?>">

        <link rel="canonical" href="<?php echo current_url(); ?>">

        <!-- Open Graph data -->
        <meta property="og:title" content="<?php echo $this->page_title; ?>" />
        <meta property="og:type" content="article" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:url" content="<?php echo current_url(); ?>" />
        <meta property="og:image" content="<?php echo $this->page_image; ?>" />
        <meta property="og:description" content="<?php echo $this->page_description; ?>" />
        <meta property="og:site_name" content="Zingura" />
        <meta property="article:published_time" content="2017-03-15T17:04:11+05:30" />
        <meta property="article:modified_time" content="2017-03-15T17:04:11+05:30" />
        <meta property="article:section" content="<?php echo $this->page_description; ?>" />
        <meta property="article:tag" content="studyz" />

        <meta property="fb:admins" content="683332918"/>
        <meta property="fb:app_id" content="654345124726276"/>

        <meta name="twitter:card" content="summary">
        <meta name="twitter:site" content="@studyz_app">
        <meta name="twitter:title" content="<?php echo $this->page_title; ?>">
        <meta name="twitter:description" content="<?php echo $this->page_description; ?>">
        <meta name="twitter:creator" content="@studyz_app">
        <!-- Twitter summary card with large image must be at least 280x150px -->
        <meta name="twitter:image:src" content="http://studyz.codecrab.in/appadmin/upload/empty-img.png">

        <meta content="1 days" name="revisit" /> 
        <meta content="index, follow" name="robots" /> 
        <meta name="robots" content="index,follow">         <!-- All meta content -->  

        <!-- FAVICON -->
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/favicon-16x16.png">
        <link rel="manifest" href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/manifest.json">

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/images/favicons/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">


        <!-- Favicons -->
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/img/favicon.png" rel="icon">
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/img/apple-touch-icon.png" rel="apple-touch-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,700|Open+Sans:300,300i,400,400i,700,700i" rel="stylesheet">

        <!-- Bootstrap CSS File -->
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Libraries CSS Files -->
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/animate/animate.min.css" rel="stylesheet">
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/magnific-popup/magnific-popup.css" rel="stylesheet">

        <!-- Main Stylesheet File -->
        <link href="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/css/style.css" rel="stylesheet">

        <!-- =======================================================
          Theme Name: Avilon
          Theme URL: https://bootstrapmade.com/avilon-bootstrap-landing-page-template/
          Author: BootstrapMade.com
          License: https://bootstrapmade.com/license/
        ======================================================= -->
    </head>

    <body>
      