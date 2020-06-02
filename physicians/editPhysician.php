<?php
ob_start();
include('../header.php');
$id=base64_decode($_GET['id']);
$userQuery="SELECT * from physicians where physician_id=$id";
$userInfo=$db->query($userQuery);
//$query="SELECT * FROM roles where status='active'";
//$result = $db->rawQuery($query);
$lQuery="SELECT * FROM facility_details where status='active'";
$lResult = $db->rawQuery($lQuery);
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1> <i  class="fa fa-gears"></i> Edit Clinician</h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Clinicians</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <div class="box-header with-border">
          <div class="pull-right" style="font-size:15px;"><span class="mandatory">*</span> indicates required field &nbsp;</div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <!-- form start -->
            <form class="form-horizontal" method='post'  name='physicianEditForm' id='physicianEditForm' autocomplete="off" action="editPhysicianHelper.php">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="userName" class="col-lg-4 control-label">Clinician Name <span class="mandatory">*</span></label>
                        <div class="col-lg-7">
                        <input type="text" class="form-control isRequired" id="physicianName" name="physicianName" placeholder="Clinician Name" title="Please enter Clinician's name" value="<?php echo $userInfo[0]['physician_name']; ?>"/>
                        <input type="hidden" name="physicianId" id="physicianId" value="<?php echo base64_encode($userInfo[0]['physician_id']);?>"/>
                        </div>
                    </div>
                  </div>
                   <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="col-lg-4 control-label">Email </label>
                        <div class="col-lg-7">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" title="Please enter email" value="<?php echo $userInfo[0]['physician_email']; ?>" onblur="checkNameValidation('physicians','physician_email',this,'<?php echo "physician_id##".$userInfo[0]['physician_id'];?>','This email id that you entered already exists.Try another email id',null)"/>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                        <label for="phoneNo" class="col-lg-4 control-label">Phone Number</label>
                        <div class="col-lg-7">
                        <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone Number" title="Please enter phone number" value="<?php echo $userInfo[0]['physician_phone']; ?>"/>
                        </div>
                    </div>
                  </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="col-lg-4 control-label">Status <span class="mandatory">*</span></label>
                            <div class="col-lg-7">
                                <select class="form-control isRequired" name='status' id='status' title="Please select the status">
                                    <option value=""> -- Select -- </option>
                                    <option value="active" <?php echo ($userInfo[0]['status']=='active')?"selected='selected'":""?>>Active</option>
                                    <option value="inactive" <?php echo ($userInfo[0]['status']=='inactive')?"selected='selected'":""?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                

                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="site" class="col-lg-4 control-label">Assigned Site </label>
                              <div class="col-lg-7">
                                  <select class="form-control " name='site' id='site' title="Please select the site">
                                      <option value=""> -- Select -- </option>
                                      <?php
                                      foreach ($lResult as $row) {
                                          ?>
                                          <option value="<?php echo $row['facility_id']; ?>" <?php echo ($userInfo[0]['physician_site']==$row['facility_id'])?"selected='selected'":""?>><?php echo $row['facility_name']; ?></option>
                                          <?php
                                      }
                                      ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
               
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow();return false;">Submit</a>
                <a href="physicians.php" class="btn btn-default"> Cancel</a>
              </div>
              <!-- /.box-footer -->
            </form>
          <!-- /.row -->
        </div>
       
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  
  <script type="text/javascript">
    pwdflag = true;
    function validateNow(){
      flag = deforayValidator.init({
          formId: 'physicianEditForm'
      });

      if(flag){
        // if($('.ppwd').val() != ''){
        //   pwdflag = checkPasswordLength();
        // }
        if(pwdflag){
          $.blockUI();
          document.getElementById('physicianEditForm').submit();
        }
      }
    }
    
    function checkNameValidation(tableName,fieldName,obj,fnct,alrt,callback){
          var removeDots=obj.value.replace(/\,/g,"");
          //str=obj.value;
          removeDots = removeDots.replace(/\s{2,}/g,' ');
          $.post("../includes/checkDuplicate.php", { tableName: tableName,fieldName : fieldName ,value : removeDots.trim(),fnct : fnct, format: "html"},
          function(data){
              if(data==='1'){
                  alert(alrt);
                  document.getElementById(obj.id).value="";
              }
          });
    }
    
    // function checkPasswordLength(){
    //   var pwd = $('#confirmPassword').val();
    //   var regex = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9!@#\$%\^\&*\)\(+=. _-]+){8,}$/;
    //   if(regex.test(pwd) == false){
    //     alert('Password must be at least 8 characters long and must include AT LEAST one number, one alphabet and may have special characters.');
    //     $('.ppwd').focus();
    //   }
    //   return regex.test(pwd);
    // }
 </script>
 <?php
 include('../footer.php');
 ?>