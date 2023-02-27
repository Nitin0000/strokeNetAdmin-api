
<!-- JavaScript Libraries -->
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/jquery/jquery.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/jquery/jquery-migrate.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/easing/easing.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/wow/wow.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/superfish/hoverIntent.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/superfish/superfish.min.js"></script>
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/lib/magnific-popup/magnific-popup.min.js"></script>

<!-- Contact Form JavaScript File -->
<!--<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/contactform/contactform.js"></script>-->

<!-- Template Main Javascript File -->
<script src="<?php echo main_url; ?>/themes/site/<?php echo theme_name; ?>/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.1.2/parsley.min.js"></script>
<script type="text/javascript">
    $("#ContactForm").parsley({
        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsWrapper: "<span class='help-block'></span>",
        errorTemplate: "<span></span>"
    });
</script>
</body>
</html>
