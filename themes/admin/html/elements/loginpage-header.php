<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6">
<![endif]-->
<!--[if IE 7]>
<html id="ie7">
<![endif]-->
<!--[if IE 8]>
<html id="ie8">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html>
<!--<![endif]-->
<head>    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">    <title><?php echo $this->page_title; ?></title>    <link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/themes/admin/css/cosmo.css">
    <link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/themes/admin/css/bootstrap-responsive.css">
    <link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/themes/admin/css/font-awesome.css">
    <link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/themes/admin/css/font-awesome-ie7.css">    <!--[if lte IE 8]>
    <link type="text/css" rel="stylesheet" href="<?php echo backoffice_url; ?>/themes/admin/css/ie.css">
    <script type="text/javascript" src="<?php echo backoffice_url; ?>/themes/admin/js/modernizr.min.js"></script>
    <![endif]-->    <!--[if lte IE 6]>
    <script type="text/javascript" src="<?php echo backoffice_url; ?>/themes/admin/js/belated.js"></script>
    <![endif]-->    <script type="text/javascript" src="<?php echo backoffice_url; ?>/themes/admin/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo backoffice_url; ?>/themes/admin/js/parsley.min.js"></script>
    <script type="text/javascript" src="<?php echo backoffice_url; ?>/themes/admin/js/bootstrap.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			$("[data-toggle=tooltip]").tooltip();
		});
	</script>
    <style>
	body{padding-top:150px;
	background:#f5f5f5}
	.parsley-success{
		color:green;
	}
	.parsley-error{
		color:red;
	}
	ul.parsley-error-list{
		list-style:none;
		margin-left:0px;
		padding-left:0px;
		font-size:12px;
		padding-top:5px;
		color:red;
	}
	li.parsley-error{
		list-style:none;
		color:red;
	}
	input.parsley-success, textarea.parsley-success {
			color: #468847 !important;
			background-color: #DFF0D8 !important;
			border: 1px solid #D6E9C6 !important;
	}
	input.parsley-error, textarea.parsley-error {
		color: #B94A48 !important;
		background-color: #F2DEDE !important;
		border: 1px solid #EED3D7 !important;
	}
	</style></head><body id="page-name">
