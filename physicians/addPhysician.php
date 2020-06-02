<?php
ob_start();
include('../header.php');
$query="SELECT * FROM roles where status='active'";
$result = $db->rawQuery($query);

$lQuery="SELECT * FROM facility_details where status='active'";
$lResult = $db->rawQuery($lQuery);
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-gears"></i> Add Clinician</h1>
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
            <form class="form-horizontal" method='post'  name='physicianForm' id='physicianForm' autocomplete="off" action="addPhysicianHelper.php">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="userName" class="col-lg-4 control-label">Clinician Name <span class="mandatory">*</span></label>
                        <div class="col-lg-7">
                        <input type="text" class="form-control isRequired" id="physicianName" name="physicianName" placeholder="Clinician Name" title="Please enter Clinician's name" />
                        </div>
                    </div>
                  </div>
                   <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="col-lg-4 control-label">Clinician Email </label>
                        <div class="col-lg-7">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" title="Please enter email" onblur="checkNameValidation('physicians','physician_email',this,null,'This email that you entered already exists.Try another email id',null)" />
                        </div>
                    </div>
                  </div>
                </div>
<!--                <div class="row">-->
<!--                   <div class="col-md-6">-->
<!--                      <div class="form-group">-->
<!--                          <label for="phoneNo" class="col-lg-4 control-label">Phone Number </label>-->
<!--                          <div class="col-lg-7">-->
<!--                              <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone Number" title="Please enter phone number"/>-->
<!--                          </div>-->
<!--                      </div>-->
<!--                  </div>-->
<!--                  <div class="col-md-6">-->
<!--                    <div class="form-group">-->
<!--                        <label for="role" class="col-lg-4 control-label">Role <span class="mandatory">*</span></label>-->
<!--                        <div class="col-lg-7">-->
<!--                        <select class="form-control isRequired" name='role' id='role' title="Please select the role">-->
<!--                        <option value=""> -- Select -- </option>-->
<!--                        --><?php
//                        foreach ($result as $row) {
//                        ?>
<!--                        <option value="--><?php //echo $row['role_id']; ?><!--">--><?php //echo $row['role_name']; ?><!--</option>-->
<!--                        --><?php
//                        }
//                        ?>
<!--                        </select>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
                
<!--                <div class="row">-->
<!--                  <div class="col-md-6">-->
<!--                    <div class="form-group">-->
<!--                        <label for="loginId" class="col-lg-4 control-label">Login Id <span class="mandatory">*</span></label>-->
<!--                        <div class="col-lg-7">-->
<!--                        <input type="text" class="form-control isRequired" id="loginId" name="loginId" placeholder="Login Id" title="Please enter login id" onblur="checkNameValidation('user_details','login_id',this,null,'This login id that you entered already exists.Try another login id',null)"/>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                  <div class="col-md-6">-->
<!--                    <div class="form-group">-->
<!--                        <label for="password" class="col-lg-4 control-label">Password <span class="mandatory">*</span></label>-->
<!--                        <div class="col-lg-7">-->
<!--                            <input type="password" class="form-control ppwd isRequired" id="confirmPassword" name="password" placeholder="Password" title="Please enter the password"/>-->
<!--                            <code>Password must be at least 8 characters long and must include AT LEAST one number, one alphabet and may have special characters.</code>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phoneNo" class="col-lg-4 control-label">Phone Number </label>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone Number" title="Please enter phone number"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="site" class="col-lg-4 control-label">Assign Site </label>
                            <div class="col-lg-7">
                                <select class="form-control " name='site' id='site' title="Please select the site">
                                    <option value=""> -- Select -- </option>
                                    <?php
                                    foreach ($lResult as $row) {
                                        ?>
                                        <option value="<?php echo $row['facility_id']; ?>"><?php echo $row['facility_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row">

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
  function validateNow(){
    flag = deforayValidator.init({
        formId: 'physicianForm'
    });
    if(flag){
      pwdflag = true;
      if(pwdflag){
        $.blockUI();
        document.getElementById('physicianForm').submit();
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