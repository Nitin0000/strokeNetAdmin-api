<?php echo $this->render("themes/admin/html/elements/header.php") ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css" />


<section class="row">
    <div class="col-sm-12">
        <section class="row">
           <div class="col-md-12 col-lg-12">
                    <h1 style='border-bottom: 0px; margin: 0px; padding-bottom: 4px;'>
                       <strong><?php echo $this->patientDetails['name']; ?> </strong>
                    </h1>
               <p>
                   <?php echo ucfirst($this->patientDetails['gender']);?>, <?php echo $this->patientDetails['age'];?> years old
               </p>
                                   <hr>
                </div>
                    
               <div class="col-md-12 col-lg-12">
                   
                    <?php if(isset($this->errors[0])){?>
                    <div class="alert alert-danger  alert-dismissible fade show"  role="alert">
                      <?php echo $this->errors[0];?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
                    </div>
                    <?php }?>
                   
                   <?php if(isset($this->success[0])){?>
                    <div class="alert alert-success  alert-dismissible fade show"  role="alert">
                      <?php echo $this->success[0];?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
                    </div>
                    <?php }?>
                   
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#patient_details" role="tab" aria-controls="home" aria-selected="true">Patient Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="ncct-tab" data-toggle="tab" href="#files_ncct" role="tab" aria-controls="profile" aria-selected="false">NCCT</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="cta-ctp-tab" data-toggle="tab" href="#files_cta_ctp" role="tab" aria-controls="profile" aria-selected="false">CTA/CTP</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="mra-mri-tab" data-toggle="tab" href="#files_mra_mri" role="tab" aria-controls="profile" aria-selected="false">MRA/MRI</a>
                        </li><li class="nav-item">
                          <a class="nav-link" id="consent-form-tab" data-toggle="tab" href="#files_consent_form" role="tab" aria-controls="profile" aria-selected="false">Consent form</a>
                        </li>
                        
                        
                        
                          
                          
                          
                        
<!--                        <li class="nav-item">
                          <a class="nav-link" id="contact-tab" data-toggle="tab" href="#followup_schedules" role="tab" aria-controls="contact" aria-selected="false">Followup Schedules</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="contact-tab" data-toggle="tab" href="#feedback_videos" role="tab" aria-controls="contact" aria-selected="false">Feedback Videos</a>
                        </li>-->
                      </ul>
                      <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="patient_details" role="tabpanel" aria-labelledby="patient-details-tab"><br>
                              <?php if($this->patientDetails['id']){?>
                              
                              <section class="row">
                                  <div class="col-md-4 col-lg-4">                                      
                                        
                                      <h4>Basic Details</h4>
                                      <hr>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Patient Name</th>
                                                <td><?php echo $this->patientDetails['name']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Age</th>
                                                <td><?php echo $this->patientDetails['age']; ?> years</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td><?php echo ucfirst($this->patientDetails['gender']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Contact Number</th>
                                                <td><?php echo $this->patientDetails['contact_number']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td><?php echo $this->patientDetails['address']; ?></td>
                                            </tr>                                           
                                        </table>
                                      
                                    
                                  </div>
                                  <div class="col-md-4 col-lg-4">
                                      
                                      <h4>Brief History</h4>
                                      <hr>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Weakness Side</th>
                                                <td><?php echo  ucfirst($this->patientDetails['patient_brief_history']['weakness_side']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Facial Deviation</th>
                                                <td><?php echo  ucfirst($this->patientDetails['patient_brief_history']['facial_deviation']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Power of limbs</th>
                                                <td><?php echo ucfirst($this->patientDetails['patient_brief_history']['power_of_limbs']); ?></td>
                                            </tr>
                                             <tr>
                                                <th>LOC</th>
                                                <td><?php echo  ucfirst($this->patientDetails['patient_brief_history']['loc']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Window period</th>
                                                <td><?php echo ucfirst($this->patientDetails['patient_brief_history']['window_period']); ?></td>
                                            </tr> 
                                            </table>
                                      
                                    <h4>Basic Tests</h4>
                                      <hr>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>RBS</th>
                                                <td><?php echo  ucfirst($this->patientDetails['patient_basic_tests']['rbs']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>INR</th>
                                                <td><?php echo  ucfirst($this->patientDetails['patient_basic_tests']['inr']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Platlets Count</th>
                                                <td><?php echo ucfirst($this->patientDetails['patient_basic_tests']['platelets_count']); ?></td>
                                            </tr>                                                                                
                                        </table>
                                  </div>
                                  <div class="col-md-4 col-lg-4">
                                      
                                      <h4>NIHSS</h4>
                                      <hr>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Admission</th>
                                                <td><?php echo $this->patientDetails['patient_nihss']['admission']['nihss_value']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>24 hours</th>
                                                <td><?php echo $this->patientDetails['patient_nihss']['24_hours']['nihss_value']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Discharge</th>
                                                <td><?php echo ucfirst($this->patientDetails['patient_nihss']['discharge']['nihss_value']); ?></td>
                                            </tr>                                            
                                            </table>
                                      
                                      
                                      
                                      <h4>MRS</h4>
                                      <hr>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Discharge</th>
                                                <td><?php echo $this->patientDetails['patient_mrs']['discharge']['mrs_points']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>1 month</th>
                                                <td><?php echo $this->patientDetails['patient_mrs']['1_month']['mrs_points']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>3 months</th>
                                                <td><?php echo ucfirst($this->patientDetails['patient_mrs']['3_months']['mrs_points']); ?></td>
                                            </tr>                                            
                                            </table>
                                     
                                      
                                  </div>
                              </section>
                              
                                                           
                              
                              <?php }else{?>
                              <div class="alert alert-danger" role="alert">
                                Patient details not added
                              </div>
                              <?php }?>                              
                          </div>                        
                          
                          
                          
                          
                          <div class="tab-pane fade" id="files_ncct" role="tabpanel" aria-labelledby="files-ncct-tab">                                <br>   
                              
                              <?php if(count($this->patientDetails['patient_files']['ncct']) > 0) {?>
                                    
                                 <?php foreach(array_chunk($this->patientDetails['patient_files']['ncct'], 4) as $images){?>
                              <div class="row" style="margin-top: 15px;">
                                        <?php foreach($images as $image){?>
                                          <div class="col-md-3">
                                              <a target="_blank" href="<?php echo $image['file'];?>">
                                                  <img class="rounded img-thumbnail" src="<?php echo $image['file_thumb'];?>" alt="Image" style="max-width:100%;">
                                              </a>
                                          </div>    
                                        <?php }?>
                                    </div>
                               <?php }?>
                              
                              <?php }else{?>
                              <div class="alert alert-danger" role="alert">
                                No files found
                              </div>
                              <?php }?> 
                              
                          </div>
                          
                          
                          <div class="tab-pane fade" id="files_cta_ctp" role="tabpanel" aria-labelledby="files-cta_ctp-tab">                                <br>  
                              
                              <?php if(count($this->patientDetails['patient_files']['cta_ctp']) > 0) {?>
                              
                              
                               <?php foreach(array_chunk($this->patientDetails['patient_files']['cta_ctp'], 4) as $images){?>
                              <div class="row" style="margin-top: 15px;">
                                        <?php foreach($images as $image){?>
                                          <div class="col-md-3">
                                              <a target="_blank" href="<?php echo $image['file'];?>">
                                                  <img class="rounded img-thumbnail" src="<?php echo $image['file_thumb'];?>" alt="Image" style="max-width:100%;">
                                              </a>
                                          </div>    
                                        <?php }?>
                                    </div>
                               <?php }?>
                              
                              <?php }else{?>
                              <div class="alert alert-danger" role="alert">
                                No files found
                              </div>
                              <?php }?> 
                          </div>
                          
                          <div class="tab-pane fade" id="files_mra_mri" role="tabpanel" aria-labelledby="files-mra_mri-tab">                                <br>  
                              
                              <?php if(count($this->patientDetails['patient_files']['mra_mri']) > 0) {?>
                              
                                    <?php foreach(array_chunk($this->patientDetails['patient_files']['mra_mri'], 4) as $images){?>
                              <div class="row" style="margin-top: 15px;">
                                        <?php foreach($images as $image){?>
                                          <div class="col-md-3">
                                              <a target="_blank" href="<?php echo $image['file'];?>">
                                                  <img class="rounded img-thumbnail" src="<?php echo $image['file_thumb'];?>" alt="Image" style="max-width:100%;">
                                              </a>
                                          </div>    
                                        <?php }?>
                                    </div>
                               <?php }?>
                              
                              
                              <?php }else{?>
                              <div class="alert alert-danger" role="alert">
                                No files found
                              </div>
                              <?php }?> 
                          </div>
                          
                          
                          <div class="tab-pane fade" id="files_consent_form" role="tabpanel" aria-labelledby="files-consent_form-tab">                                <br>  
                              
                               <?php if(count($this->patientDetails['patient_files']['consent_form']) > 0) {?>
                              
                               <?php foreach(array_chunk($this->patientDetails['patient_files']['consent_form'], 4) as $images){?>
                              <div class="row" style="margin-top: 15px;">
                                        <?php foreach($images as $image){?>
                                          <div class="col-md-3">
                                              <a target="_blank" href="<?php echo $image['file'];?>">
                                                  <img class="rounded img-thumbnail" src="<?php echo $image['file_thumb'];?>" alt="Image" style="max-width:100%;">
                                              </a>
                                          </div>    
                                        <?php }?>
                                    </div>
                               <?php }?>
                              
                              <?php }else{?>
                              <div class="alert alert-danger" role="alert">
                                No files found
                              </div>
                              <?php }?> 
                          </div>
                          
                          
                      </div>                   
               </div>                    
        </section>
    </div>
</section>

<?php echo $this->render("themes/admin/html/elements/footer.php"); ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
    $(document).ready(function(){
        $('#datetimepicker').datetimepicker({
            format : 'Y-m-d H:m',
            inline:true,
            minDate: '<?php echo date("Y-m-d");?>'
        });
         
    });
</script>