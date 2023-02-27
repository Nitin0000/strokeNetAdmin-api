                
                </main> 
            </div>
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        
                  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="<?php echo backoffice_url ?>/themes/admin/assets/dist/js/bootstrap.min.js"></script>  
        
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>  
        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>  
        <script src="https://cdn.datatables.net/responsive/2.2.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.0/js/responsive.bootstrap4.min.js"></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.5.0/parsley.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.0.5/sweetalert2.all.js"></script>
        <script type="text/javascript" src="<?php echo backoffice_url ?>/libs/parsley-js-validations/parsley.min.js"></script>
        <script type="text/javascript" src="<?php echo backoffice_url ?>/libs/bootstrap-paginator/bootstrap-paginator.js"></script>

<!--        <script type="text/javascript" src="<?php echo backoffice_url ?>/libs/ckeditor4/ckeditor.js"></script> -->
        
        <script src="<?php echo backoffice_url; ?>/libs/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '.ckeditor',
    height: 500,
    theme: 'silver',
    convert_urls : false,
    directionality :"ltr",
    toolbar: "ltr",
    plugins: [
      'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars fullscreen',
      'insertdatetime media nonbreaking save table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern imagetools directionality'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    toolbar2: 'preview | forecolor backcolor',
    image_advtab: true,
    
    // without images_upload_url set, Upload tab won't show up
    images_upload_url: '<?php echo main_url; ?>/uploads/upload_editor_tinymce.php',
    
    // override default upload handler to simulate successful upload
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '<?php echo main_url; ?>/uploads/upload_editor_tinymce.php');
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            success(json.location);
        };
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    },
});
</script>    
        
        <script>
            Parsley.options.errorClass = 'has-danger'
            Parsley.options.successClass = 'has-success'
            Parsley.options.classHandler = function(f) { return f.$element.closest('.form-group'); }
            Parsley.options.errorsWrapper = '<div class="form-control-feedback"></div>'
            Parsley.options.errorTemplate = '<div></div>'
          </script>
        <script>
            $(document).ready(function(){
                $('#DataTable').DataTable({
                     responsive: true,
                     pageLength: 50
                });
            });
        </script>
        <script src="<?php echo backoffice_url ?>/themes/admin/assets/js/custom.js"></script>        
    </body>
</html>
