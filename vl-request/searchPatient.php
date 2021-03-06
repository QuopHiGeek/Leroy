﻿<?php
ob_start();

//    ini_set( "display_errors", 1 );
//	error_reporting( E_ALL );

include('../General.php');
$general=new Deforay_Commons_General();
//global config
$cQuery="SELECT * FROM global_config";
$cResult=$db->query($cQuery);
$arr = array();
// now we create an associative array so that we can easily create view variables
for ($i = 0; $i < sizeof($cResult); $i++) {
  $arr[$cResult[$i]['name']] = $cResult[$i]['value'];
}

if($arr['sample_code']=='auto' || $arr['sample_code']=='alphanumeric' || $arr['sample_code']=='MMYY' || $arr['sample_code']=='YY'){
  $sampleClass = '';
  $maxLength = '';
  if($arr['max_length']!='' && $arr['sample_code']=='alphanumeric'){
    $maxLength = $arr['max_length'];
    $maxLength = "maxlength=".$maxLength;
  }
}else{
  $sampleClass = 'checkNum';
  $maxLength = '';
  if($arr['max_length']!=''){
    $maxLength = $arr['max_length'];
    $maxLength = "maxlength=".$maxLength;
  }
}
//get import config
$importQuery="SELECT * FROM import_config WHERE status = 'active'";
$importResult=$db->query($importQuery);

$userQuery="SELECT * FROM user_details where status='active' and user_id =".$_SESSION['userId'];
$userResult = $db->rawQuery($userQuery);

//get lab facility details

    $siteID = $_SESSION['siteID'];
    $lQuery = "SELECT * FROM facility_details where facility_id='$siteID'";
    $lResult = $db->rawQuery($lQuery);

//else {
//    $lQuery = "SELECT * FROM facility_details where facility_type='2' AND status='active'";
//    $lResult = $db->rawQuery($lQuery);
//}
//sample rejection reason
$rejectionQuery="SELECT * FROM r_sample_rejection_reasons WHERE rejection_reason_status ='active'";
$rejectionResult = $db->rawQuery($rejectionQuery);
//rejection type
$rejectionTypeQuery="SELECT DISTINCT rejection_type FROM r_sample_rejection_reasons WHERE rejection_reason_status ='active'";
$rejectionTypeResult = $db->rawQuery($rejectionTypeQuery);


$facilityState=$db->escape($lResult[0]['facility_state']);
$params = array($facilityState);
$pdQuery="SELECT * from province_details where province_name=?";
$pdResult=$db->rawQuery($pdQuery,$params);
//$province = '';
//$province.="<option value=''> -- Select -- </option>";
  foreach($pdResult as $provinceName){
    $province .= "<option value='".$provinceName['province_name']."##".$provinceName['province_code']."'>".ucwords($provinceName['province_name'])."</option>";
  }

$district = "<option value='".$lResult[0]['facility_district']."'>".ucwords($lResult[0]['facility_district'])."</option>";
//$facility = '';
$facility.="<option data-code='".$lResult[0]['facility_id']."' data-emails='".$lResult[0]['facility_emails']."' data-mobile-nos='".$lResult[0]['facility_mobile_numbers']."' data-contact-person='".$lResult[0]['contact_person']."' value='".$lResult[0]['facility_code']."'>".ucwords($lResult[0]['facility_name'])."</option>";

//get active sample types
$sQuery="SELECT * from r_sample_type where status='active'";
$sResult=$db->query($sQuery);

//get active physicians for site
$phyQuery="SELECT * from physicians where status='active' and physician_site='$siteID'";
$phyResult=$db->query($phyQuery);

//get active specimen pickers for site
$pickerQuery="SELECT * from r_specimen_pickers where status='active' and site='$siteID'";
$pickerResult=$db->query($pickerQuery);

//regimen heading
$artRegimenQuery="SELECT DISTINCT headings FROM r_art_code_details WHERE nation_identifier ='rwd'";
$artRegimenResult = $db->rawQuery($artRegimenQuery);
$aQuery="SELECT * from r_art_code_details where nation_identifier='rwd' AND art_status ='active'";
$aResult=$db->query($aQuery);
if($arr['sample_code']=='MMYY'){
    $mnthYr = date('my');
    $end_date = date('Y-m-31');
    $start_date = date('Y-m-01');
  }else if($arr['sample_code']=='YY'){
    $mnthYr = date('y');
    $end_date = date('Y-12-31');
    $start_date = date('Y-01-01');
  }else{
    $mnthYr = date('y');
    $end_date = date('Y-12-31');
    $start_date = date('Y-m-01');
}

//$svlQuery='select MAX(sample_code_key) FROM vl_request_form as vl where vl.vlsm_country_id="1" AND vl.sample_code_title="'.$arr['sample_code'].'" AND DATE(vl.request_created_datetime) >= "'.$start_date.'" AND DATE(vl.request_created_datetime) <= "'.$end_date.'"';
$svlQuery='SELECT MAX(sample_code_key) FROM vl_request_form as vl WHERE DATE(vl.request_created_datetime) >= "'.$start_date.'" AND DATE(vl.request_created_datetime) <= "'.$end_date.'" ORDER BY vl_sample_id DESC LIMIT 1';
//$svlQuery='select MAX(sample_code_key) FROM vl_request_form'
$svlResult=$db->query($svlQuery);
//var_dump($svlResult);die;
  $prefix = $arr['sample_code_prefix'];
  if($svlResult[0]['MAX(sample_code_key)']!='' && $svlResult[0]['MAX(sample_code_key)']!=NULL){
   $maxId = $svlResult[0]['MAX(sample_code_key)']+1;
   $strparam = strlen($maxId);
   $zeros = substr("000", $strparam);
   $maxId = $zeros.$maxId;
  }else{
   $maxId = '001';
  }
  //var_dump($maxId);die;
$sKey = '';
$sFormat = '';


?>
<style>
  .ui_tpicker_second_label {
       display: none !important;
      }
      .ui_tpicker_second_slider {
       display: none !important;
      }.ui_tpicker_millisec_label {
       display: none !important;
      }.ui_tpicker_millisec_slider {
       display: none !important;
      }.ui_tpicker_microsec_label {
       display: none !important;
      }.ui_tpicker_microsec_slider {
       display: none !important;
      }.ui_tpicker_timezone_label {
       display: none !important;
      }.ui_tpicker_timezone {
       display: none !important;
      }.ui_tpicker_time_input{
       width:100%;
      }
      .table > tbody > tr > td{
        border-top:none;
      }
      .form-control{
        width:100% !important;
      }
      .row{
        margin-top:6px;
      }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-edit"></i> eLabMessenger REQUEST FORM </h1>
        <ol class="breadcrumb">
            <li><a href="../dashboard/index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Request</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- SELECT2 EXAMPLE -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="pull-right" style="font-size:15px;"><span class="mandatory">*</span> indicates required field &nbsp;</div>
            </div>
            <form class="form-inline" method="post" name="vlRequestFormRwd" id="vlRequestFormRwd" autocomplete="off" action="addVlRequestHelper.php">
                <div class="box-body">
                    <div class="widget">
                        <div class="widget-content">
                            <div class="bs-example bs-example-tabs">
                                <ul id="myTab" class="nav nav-tabs">
                                    <li class="active"><a href="#clinicInformation" data-toggle="tab">Clinic Information</a></li>
                                    <li><a href="#patientInformation" data-toggle="tab">Patient Information</a></li>
                                    <li><a href="#specimenInformation" data-toggle="tab">Specimen Information</a></li>
<!--                                    <li><a href="#notifyGhanaPost" data-toggle="tab">Notify GhanaPost</a></li>-->
                                    <li><a href="#treatmentInformation" data-toggle="tab">Treatment Information</a></li>
                                    <li><a href="#viralInformation" data-toggle="tab">Viral Load Information</a></li>
<!--                                    <li><a href="#laboratoryInformation" data-toggle="tab">Laboratory Information</a></li>-->
                                </ul>
                                <div id="myTabContent" class="tab-content" >
                                    <div class="tab-pane fade in active" id="clinicInformation">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Clinic Information: (To be filled by requesting Clinican/Nurse)</h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="sampleCode">Specimen ID <span class="mandatory">*</span></label>
                                                                <?php
                                                                if($arr['sample_code']=='auto'){

                                                                    $pNameVal = $provinceName['province_code'];
                                                                    $sCode = date('ymd');
                                                                    $sCodeKey = $maxId;
                                                                    $sampleCode = $pNameVal.$sCode.$sCodeKey;
                                                                    $sampleCodeFormat = $pNameVal.$sCode;
                                                                    $sampleCodeKey = $sCodeKey;

                                                                }else if($arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){
                                                                    $sampleCode = $prefix.$mnthYr.$maxId;
                                                                    $sampleCodeFormat = $prefix.$mnthYr;
                                                                    $sampleCodeKey = $maxId;

                                                                }?>
                                                                <input type="text" class="form-control isRequired <?php echo $sampleClass;?>" id="sampleCode" name="sampleCode" <?php echo $maxLength;?> readonly placeholder="Enter Sample ID" title="Please enter sample id" style="width:100%;" value="<?php echo $sampleCode;?> "/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="sampleReordered">
                                                                    <input type="checkbox" class="" id="sampleReordered" name="sampleReordered" value="yes" title="Please check sample reordered"> Specimen Reordered
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <!-- BARCODESTUFF START -->
                                                        <?php
                                                        if(isset($global['bar_code_printing']) && $global['bar_code_printing'] != "off"){
                                                            ?>
                                                            <div class="col-xs-3 col-md-3 pull-right">
                                                                <div class="form-group">
                                                                    <label for="sampleCode">Print Barcode Label<span class="mandatory">*</span> </label>
                                                                    <input type="checkbox" class="" id="printBarCode" name="printBarCode" checked/>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        <!-- BARCODESTUFF END -->
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="province">Region <span class="mandatory">*</span></label>
                                                                <select class="form-control isRequired" name="province" id="province" title="Please choose region" readonly="" style="width:100%;" >
                                                                    <?php echo $province;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="district">District <span class="mandatory">*</span></label>
                                                                <select class="form-control isRequired" name="district" id="district" readonly="" title="Please choose county" style="width:100%;" >
                                                                    <?php echo $district;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="fName">ART Centre <span class="mandatory">*</span></label>
                                                                <select class="form-control isRequired" id="fName" name="fName" readonly="" title="Please select clinic/health center name" style="width:100%;" >
                                                                    <?php echo $facility;  ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="fCode">ART Centre Code </label>
                                                                <input type="text" class="form-control" style="width:100%;" name="fCode" id="fCode" readonly placeholder="ART Center Code" title="Please enter ART center code" value="<?php echo $lResult[0]['facility_code']?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row facilityDetails" style="display:none;">
                                                        <div class="col-xs-2 col-md-2 femails" style="display:none;"><strong>Clinic Email(s) -</strong></div>
                                                        <div class="col-xs-2 col-md-2 femails facilityEmails" style="display:none;"></div>
                                                        <div class="col-xs-2 col-md-2 fmobileNumbers" style="display:none;"><strong>Clinic Mobile No.(s) -</strong></div>
                                                        <div class="col-xs-2 col-md-2 fmobileNumbers facilityMobileNumbers" style="display:none;"></div>
                                                        <div class="col-xs-2 col-md-2 fContactPerson" style="display:none;"><strong>Clinic Contact Person -</strong></div>
                                                        <div class="col-xs-2 col-md-2 fContactPerson facilityContactPerson" style="display:none;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow();return false;">Save</a>
                                            <a class="btn btn-primary" href="#patientInformation" class ="btn btn-default btn-sm"  data-toggle="tab">Save and Next </a>
                                            <a href="vlRequest.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="patientInformation">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Patient Information</h3>&nbsp;&nbsp;&nbsp;
                                                    <input style="width:30%;" type="text" name="artPatientNo" id="artPatientNo" class="" placeholder="Enter ART Number or Patient Name" title="Enter art number or patient name"/>&nbsp;&nbsp;
                                                    <a style="margin-top:-0.35%;" href="javascript:void(0);" class="btn btn-default btn-sm" onclick="showPatientList();"><i class="fa fa-search">&nbsp;</i>Search</a><span id="showEmptyResult" style="display:none;color: #ff0000;font-size: 15px;"><b>&nbsp;No Patient Found</b></span>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="artNo">ART No. <span class="mandatory">*</span></label>
                                                                <input type="text" name="artNo" id="artNo" class="form-control isRequired" placeholder="Enter ART Number" title="Enter art number" onchange="checkNameValidation('vl_request_form','patient_art_no',this,null)" />
                                                               <!-- <td colspan="2"><label for="isPatientNew">Is Patient New? </label>  -->
                                                                
                                                                
                                                            <script type="text/javascript"  src="https://cdn.rawgit.com/LeaVerou/awesomplete/gh-pages/awesomplete.min.js"></script>
                                                            <script type="text/javascript">
                                                                
                                                                var input = document.getElementById("artNo");
                                                                var awesomplete = new Awesomplete(input, {
                                                                    list: ["15465","7454","231","4464","685","4654","78","27","544","46","4984","887","889","656","121","2065","484","558","4887"],
                                                                    minChars:1
                                                                });
                                                                    
                                                                
                                                                </script>
                                <!--    <label class="radio-inline" style="padding-left:17px !important;margin-left:0;">Yes</label>
                                    <label class="radio-inline" style="width:4%;padding-bottom:22px;margin-left:0;">
                                        <input type="radio" class="" id="isPatientNewYes" name="isPatientNew" value="yes" onclick="alert('Possible Duplicate ART No, Change to Proceed')"; title="Please check ART No. Duplicate" checked />
                                    </label>
                                    <label class="radio-inline" style="padding-left:17px !important;margin-left:0;">No</label>
                                    <label class="radio-inline" style="width:4%;padding-bottom:22px;margin-left:0;">
                                        <input type="radio" class="" id="isPatientNewNo" name="isPatientNew" value="no" />
                                    </label>  -->
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="dob">Date of Birth </label>
                                                                <input type="text" name="dob" id="dob" class="form-control date" placeholder="Enter DOB" title="Enter dob" onchange="getAge();"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="ageInYears">If DOB unknown, Age in Years <span class="mandatory"> * </span></label>
                                                                <input type="text" name="ageInYears" id="ageInYears" class="form-control checkNum isRequired" maxlength="2" placeholder="Age in Year" title="Enter age in years"/>
                                                            </div>
                                                        </div>
                                                         <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="ageInMonths">If Age < 1, Age in Months  <span class="mandatory">(NB:1-12)</span></label>
                                                            <input type="text" name="ageInMonths" id="ageInMonths" class="form-control checkNum" maxlength="2" placeholder="Age in Month" title="Enter age in months"/>
                                                            <script type="text/javascript">

                                                                var input = document.getElementById("ageInMonths");
                                                                var awesomplete = new Awesomplete(input, {
                                                                    list: ["1","2","3","4","5","6","7","8","9","10","11","12"],
                                                                    minChars:1
                                                                });	
			                                            </script>                    
                                                                  </div>
                                                        </div>
                                                    </div>
                                                   <div class="row">
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="patientFirstName">Patient First Name </label>
                                                            <input type="text" name="patientFirstName" id="patientFirstName" class="form-control" placeholder="Enter Patient First Name" title="Enter patient name"/>
                                                        </div>
                                                    </div>
                                                    <!--  Patient Middle Name Input  ********************************************    -->
                                                    
                                                    
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="patientMiddleName">Patient Middle  Name </label>
                                                            <input type="text" name="patientMiddleName" id="patientMiddleName" class="form-control" placeholder="Enter Patient Middle Name" title="Enter patient name"/>
                                                        </div>
                                                    </div>
                                               
                                                    
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="patientLastName">Patient Last Name </label>
                                                            <input type="text" name="patientLastName" id="patientLastName" class="form-control" placeholder="Enter Patient Last Name" title="Enter patient name"/>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="patientPhoneNumber">Phone Number</label>
                                                            <input type="text" name="patientPhoneNumber" id="patientPhoneNumber" class="form-control checkNum" maxlength="15" placeholder="Enter Phone Number" title="Enter phone number"/>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="gender">Gender <span class="mandatory">*</span></label><br>
                                                            <label class="radio-inline" style="margin-left:0px;">
                                                                <input type="radio" class="isRequired" id="genderMale" name="gender" value="Male" title="Please check gender">Male
                                                            </label>
                                                            <label class="radio-inline" style="margin-left:0px;">
                                                                <input type="radio" class="isRequired" id="genderFemale" name="gender" value="Female" title="Please check gender">Female
                                                            </label>
                                                         <!--   <label class="radio-inline" style="margin-left:0px;">
                                                                <input type="radio" class="" id="genderNotRecorded" name="gender" value="not_recorded" title="Please check gender">Not Recorded
                                                            </label> -->
                                                        </div>
                                                    </div>
                                                   <!-- <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="gender">Patient consent to receive SMS?</label><br>
                                                            <label class="radio-inline" style="margin-left:0px;">
                                                                <input type="radio" class="" id="receivesmsYes" name="receiveSms" value="yes" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> Yes
                                                            </label>
                                                            <label class="radio-inline" style="margin-left:0px;">
                                                                <input type="radio" class="" id="receivesmsNo" name="receiveSms" value="no" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> No
                                                            </label>
                                                        </div>
                                                    </div> -->
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a class="btn btn-primary" href="#specimenInformation" onclick="checkDuplicate()" data-toggle="tab">Save and Next</a>
                                            <a href="vlRequest.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="specimenInformation">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Specimen Information</h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Date of Specimen Collection <span class="mandatory">*</span></label>
                                                                <input type="text" class="form-control isRequired" style="width:100%;" name="sampleCollectionDate" id="sampleCollectionDate" placeholder="Sample Collection Date" title="Please select sample collection date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="specimenType">Specimen Type <span class="mandatory">*</span></label>
                                                                <select name="specimenType" id="specimenType" class="form-control isRequired" title="Please choose sample type">
                                                                    <option value=""> -- Select -- </option>
                                                                    <?php
                                                                    foreach($sResult as $name){
                                                                        ?>
                                                                        <option value="<?php echo $name['sample_id'];?>"><?php echo ucwords($name['sample_name']);?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                  <!--   <option value="dbsid">DBS</option> -->
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="specimenPickedBy">Specimen Picked By <span class="mandatory">*</span></label>
                                                                <select name="specimenPickedBy" id="specimenPickedBy" class="form-control isRequired" title="Please select the person who picked specimen">
                                                                    <option value=""> -- Select -- </option>
                                                                    <?php
                                                                    foreach($pickerResult as $name){
                                                                        ?>
                                                                        <option value="<?php echo $name['picker_id'];?>"><?php echo ucwords($name['picker_name']);?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                        <!-- Unique Identifier Input  -->
                                                    
                                                      <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="patientLastName">Unique Identifier </label>
                                                            <input type="text" name="uniqueIdentifier" id="uniqueIdentifier" class="form-control" placeholder="Enter Unique Identifier Information" title="Enter Unique I"/>
                                                        </div>
                                                    </div>
                                                       
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow();return false;">Save</a>
                                            <a class="btn btn-primary" name="treatBtn" id="treatBtn" href="#treatmentInformation" data-toggle="tab">Save and Next</a>
                                            <a href="vlRequest.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="treatmentInformation">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Treatment Information</h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Date of Treatment Initiation</label>
                                                                <input type="text" class="form-control date" name="dateOfArtInitiation" id="dateOfArtInitiation" placeholder="Date Of Treatment Initiated" title="Date Of treatment initiated" style="width:100%;">
                                                            </div>
                                                        </div>
                                                        
                                                         <!-- Regimen Funtion to allow user input -->        
                                                    <div class="col-xs-3 col-md-3">
                                                        <div class="form-group">
                                                            <label for="artRegimen">Current Regimen</label>
                                                                <select multiple size="4" class="form-control js-example-basic-multiple" id="artRegimen" name="artRegimen" title="Please choose ART Regimen" style="width:100%;" onchange="checkARTValue();">
                                                               <option value="">-- Select --</option> 
                                                                <?php foreach($artRegimenResult as $heading) { ?>
                                                                    <optgroup label="<?php echo ucwords($heading['headings']); ?>">
                                                                        <?php
                                                                        foreach($aResult as $regimen){
                                                                            if($heading['headings'] == $regimen['headings']){
                                                                                ?>
                                                                                <option value="<?php echo $regimen['art_code']; ?>"><?php echo $regimen['art_code']; ?></option>
                                                                                <?php
                                                                        }
                                                                        }
                                                                        ?>
                                                                     <!--   <option value="other" >Other</option> -->
                                                                      </optgroup>
                                                                <?php } ?>
                                                                    </select>
                                                                    
                                                         <!-- <td class="newArtRegimen" style="display: none;"><label for="newArtRegimen">New ART Regimen</label><span class="mandatory">*</span></td>
                                                             <td class="newArtRegimen" style="display: none;">
                                                            <input type="text" class="form-control newArtRegimen" name="newArtRegimen" id="newArtRegimen" placeholder="New Art Regimen" title="New Art Regimen" style="width:100%;" >
                                                                </td>    -->
                                                            <br>
                                                            <br> 
                                                        </div>
                                                        
                                                   </div>
                                                        <!-- Regimen Funtion to allow user input -->  
                                                        
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Date of Initiation of Current Regimen </label>
                                                                <input type="text" class="form-control date" style="width:100%;" name="regimenInitiatedOn" id="regimenInitiatedOn" placeholder="Current Regimen Initiated On" title="Please enter current regimen initiated on">
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="arvAdherence">ARV Adherence </label>
                                                                <select name="arvAdherence" id="arvAdherence" class="form-control" title="Please choose adherence">
                                                                    <option value=""> -- Select -- </option>
                                                                    <option value="good">Good >= 95%</option>
                                                                    <option value="fair">Fair (85-94%)</option>
                                                                    <option value="poor">Poor < 85%</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row ">
                                                        <div class="col-xs-3 col-md-3 femaleSection">
                                                            <div class="form-group">
                                                                <label for="patientPregnant">Is Patient Pregnant? </label><br>
                                                                <label class="radio-inline">
                                                                    <input type="radio" class="required" id="pregYes" name="patientPregnant" value="yes" title="Please check one"> Yes
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" class="required" id="pregNo" name="patientPregnant" value="no"> No
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3 femaleSection">
                                                            <div class="form-group">
                                                                <label for="breastfeeding">Is Patient Breastfeeding?</label><br>
                                                                <label class="radio-inline required">
                                                                    <input type="radio" class="required" id="breastfeedingYes" name="breastfeeding" value="yes" title="Please check one"> Yes
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" class="required" id="breastfeedingNo" name="breastfeeding" value="no"> No
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3" style="display:none;">
                                                            <div class="form-group">
                                                                <label for="">How long has this patient been on treatment ? </label>
                                                                <input type="text" class="form-control" id="treatPeriod" name="treatPeriod" placeholder="Enter Treatment Period" title="Please enter how long has this patient been on treatment" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a class="btn btn-primary" href="#viralInformation" data-toggle="tab">Save and Next</a>
                                            <a href="vlRequest.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="viralInformation">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Indication for Viral Load Testing</h3><small> (Please tick one):(To be completed by clinician)</small>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-lg-12">
                                                                    <label class="radio-inline">
                                                                        <input type="radio" class="" id="rmTesting" name="stViralTesting" value="routine" title="Please check routine monitoring" onclick="showTesting('rmTesting');">
                                                                        <strong>Routine Monitoring</strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row rmTesting hideTestData" style="display:none;">
                                                        <div class="col-md-6">
                                                            <label class="col-lg-5 control-label">Date of last viral load test</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control date viralTestData" id="rmTestingLastVLDate" name="rmTestingLastVLDate" placeholder="Select Last VL Date" title="Please select Last VL Date"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="rmTestingVlValue" class="col-lg-3 control-label">VL Value</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control checkNum viralTestData" id="rmTestingVlValue" name="rmTestingVlValue" placeholder="Enter VL Value" title="Please enter vl value" />
                                                                (copies/ml)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <div class="col-lg-12">
                                                                    <label class="radio-inline">
                                                                        <input type="radio" class="" id="repeatTesting" name="stViralTesting" value="failure" title="Repeat VL test after suspected treatment failure adherence counseling" onclick="showTesting('repeatTesting');">
                                                                        <strong>Repeat VL test after suspected treatment failure adherence counselling </strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row repeatTesting hideTestData" style="display:none;">
                                                        <div class="col-md-6">
                                                            <label class="col-lg-5 control-label">Date of last viral load test</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control date viralTestData" id="repeatTestingLastVLDate" name="repeatTestingLastVLDate" placeholder="Select Last VL Date" title="Please select Last VL Date"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="repeatTestingVlValue" class="col-lg-3 control-label">VL Value</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control checkNum viralTestData" id="repeatTestingVlValue" name="repeatTestingVlValue" placeholder="Enter VL Value" title="Please enter vl value" />
                                                                (copies/ml)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-lg-12">
                                                                    <label class="radio-inline">
                                                                        <input type="radio" class="" id="suspendTreatment" name="stViralTesting" value="suspect" title="Suspect Treatment Failure" onclick="showTesting('suspendTreatment');">
                                                                        <strong>Suspect Treatment Failure</strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row suspendTreatment hideTestData" style="display: none;">
                                                        <div class="col-md-6">
                                                            <label class="col-lg-5 control-label">Date of last viral load test</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control date viralTestData" id="suspendTreatmentLastVLDate" name="suspendTreatmentLastVLDate" placeholder="Select Last VL Date" title="Please select Last VL Date"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="suspendTreatmentVlValue" class="col-lg-3 control-label">VL Value</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control checkNum viralTestData" id="suspendTreatmentVlValue" name="suspendTreatmentVlValue" placeholder="Enter VL Value" title="Please enter vl value" />
                                                                (copies/ml)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label for="reqClinician" class="col-lg-5 control-label">Request Clinician<span class="mandatory">*</span></label>
                                                            <div class="col-lg-7">
<!--                                                                <input type="text" class="form-control" id="reqClinician" name="reqClinician" placeholder="Request Clinician" title="Please enter request clinician" />-->
                                                                <select name="reqClinician" id="reqClinician" class="form-control isRequired" title="Please select request clinician" onchange="getPhysicians(this)">
                                                                    <option value=""> -- Select -- </option>
                                                                    <?php
                                                                    foreach($phyResult as $name){
                                                                        ?>
                                                                        <option value="<?php echo $name['physician_name'];?>"><?php echo ucwords($name['physician_name']);?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="reqClinicianPhoneNumber" class="col-lg-5 control-label">Phone Number</label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control checkNum" id="reqClinicianPhoneNumber" name="reqClinicianPhoneNumber" maxlength="15" placeholder="Phone Number" title="Please enter request clinician phone number" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="col-lg-5 control-label" for="requestDate">Request Date </label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control date" id="requestDate" name="requestDate" placeholder="Request Date" title="Please select request date"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="display:none;">

                                                        <div class="col-md-4">
                                                            <label class="col-lg-5 control-label" for="emailHf">Email for HF </label>
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control isEmail" id="emailHf" name="emailHf" placeholder="Email for HF" title="Please enter email for hf"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">

                                            <!-- BARCODESTUFF START -->
                                            <?php
                                            if(isset($global['bar_code_printing']) && $global['bar_code_printing'] == 'zebra-printer'){
                                                ?>

                                                <div id="printer_data_loading" style="display:none"><span id="loading_message">Loading Printer Details...</span><br/>
                                                    <div class="progress" style="width:100%">
                                                        <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                                        </div>
                                                    </div>
                                                </div> <!-- /printer_data_loading -->
                                                <div id="printer_details" style="display:none">
                                                    <span id="selected_printer">No printer selected!</span>
                                                    <button type="button" class="btn btn-success" onclick="changePrinter()">Change/Retry</button>
                                                </div><br /> <!-- /printer_details -->
                                                <div id="printer_select" style="display:none">
                                                    Zebra Printer Options<br />
                                                    Printer: <select id="printers"></select>
                                                </div> <!-- /printer_select -->

                                                <?php
                                            }
                                            ?>
                                            <!-- BARCODESTUFF END -->

                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow();return false;">Save</a>
                                            <input type="hidden" name="saveNext" id="saveNext"/>


                                            <input type="hidden" name="sampleCodeTitle" id="sampleCodeTitle" value="<?php echo $arr['sample_code'];?>"/>
                                                              <input type="hidden" name="sampleCode" id="sampleCode" value="<?php echo $sampleCode;?>"/>

                                            <?php if($arr['sample_code']=='auto' || $arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
                                                <input type="hidden" name="sampleCodeFormat" id="sampleCodeFormat" value="<?php echo $sampleCodeFormat;?>"/>
                                                <input type="hidden" name="sampleCodeKey" id="sampleCodeKey" value="<?php echo $sampleCodeKey;?>"/>
                                            <?php } ?>
                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateSaveNow();return false;">Save and Next</a>
                                            <a href="vlRequest.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </section>
</div>
<!-- BARCODESTUFF START -->
<?php
if(isset($global['bar_code_printing']) && $global['bar_code_printing'] != "off"){
    if($global['bar_code_printing'] == 'dymo-labelwriter-450'){
        ?>
        <script src="../assets/js/DYMO.Label.Framework.2.0.2.js"></script>
        <script src="../assets/js/dymo-format.js"></script>
        <script src="../assets/js/dymo-print.js"></script>
        <?php
    }else if($global['bar_code_printing'] == 'zebra-printer'){
        ?>
        <script src="../assets/js/BrowserPrint-1.0.4.min.js"></script>
        <script src="../assets/js/zebra-format.js"></script>
        <script src="../assets/js/zebra-print.js"></script>
        <?php
    }
}
?>

<!-- BARCODESTUFF END -->
<script>
    provinceName = true;
    facilityName = true;
    $(document).ready(function() {
        // BARCODESTUFF START

        <?php
        if(isset($_GET['barcode']) && $_GET['barcode'] == 'true'){
            echo "printBarcodeLabel('".$_GET['s']."','".$_GET['f']."');";
        }
        ?>
        // BARCODESTUFF END
        $('.date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-M-yy',
            timeFormat: "hh:mm TT",
            maxDate: "Today",
            yearRange: <?php echo (date('Y') - 100); ?> + ":" + "<?php echo (date('Y')) ?>"
        }).click(function(){
            $('.ui-datepicker-calendar').show();
        });
        $('#sampleCollectionDate,#sampleReceivedOn,#sampleTestingDateAtLab,#resultDispatchedOn').datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-M-yy',
            timeFormat: "HH:mm",
            maxDate: "Today",
            onChangeMonthYear: function(year, month, widget) {
                setTimeout(function() {
                    $('.ui-datepicker-calendar').show();
                });
            },
            yearRange: <?php echo (date('Y') - 100); ?> + ":" + "<?php echo (date('Y')) ?>"
        }).click(function(){
            $('.ui-datepicker-calendar').show();
        });
        $('.date').mask('99-aaa-9999');
        $('#sampleCollectionDate,#sampleReceivedOn,#sampleTestingDateAtLab,#resultDispatchedOn').mask('99-aaa-9999 99:99');
    });

    function showTesting(chosenClass){
        $(".viralTestData").val('');
        $(".hideTestData").hide();
        $("."+chosenClass).show();
    }

    //  function getProvinceDistricts(obj){
    //    $.blockUI();
    //    var cName = $("#fName").val();
    //    var pName = $("#province").val();
    //    if(pName!='' && provinceName && facilityName){
    //      facilityName = false;
    //    }
    //  if(pName!=''){
    //    if(provinceName){
    //    $.post("../includes/getFacilityForClinic.php", { pName : pName},
    //    function(data){
    //  if(data != ""){
    //          details = data.split("###");
    //          $("#district").html(details[1]);
    //          $("#fName").html("<option data-code='' data-emails='' data-mobile-nos='' data-contact-person='' value=''> -- Select -- </option>");
    //          $("#fCode").val('');
    //          $(".facilityDetails").hide();
    //          $(".facilityEmails").html('');
    //          $(".facilityMobileNumbers").html('');
    //          $(".facilityContactPerson").html('');
    //  }
    //    });
    //    }
    //    <?php
    //    if($arr['sample_code']=='auto'){
    //      ?>
    //      pNameVal = pName.split("##");
    //      sCode = '<?php //echo date('ymd');?>//';
    //      sCodeKey = '<?php //echo $maxId;?>//';
    //      $("#sampleCode").val(pNameVal[1]+sCode+sCodeKey);
    //      $("#sampleCodeFormat").val(pNameVal[1]+sCode);
    //      $("#sampleCodeKey").val(sCodeKey);
    //      <?php
    //    }else if($arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
    //      $("#sampleCode").val('<?php //echo $prefix.$mnthYr.$maxId;?>//');
    //      $("#sampleCodeFormat").val('<?php //echo $prefix.$mnthYr;?>//');
    //      $("#sampleCodeKey").val('<?php //echo $maxId;?>//');
    //      <?php
    //    }
    //    ?>
    //  }else if(pName=='' && cName==''){
    //    provinceName = true;
    //    facilityName = true;
    //    $("#province").html("<?php //echo $province;?>//");
    //    $("#fName").html("<?php //echo $facility;?>//");
    //  }
    //  $.unblockUI();
    //}


  


    function getFacilities(obj){
        $.blockUI();
        var dName = $("#district").val();
        var cName = $("#fName").val();
        if(dName!=''){
            $.post("../includes/getFacilityForClinic.php", {dName:dName,cliName:cName},
                function(data){
                    if(data != ""){
                        $("#fName").html(data);
                        $(".facilityDetails").hide();
                        $(".facilityEmails").html('');
                        $(".facilityMobileNumbers").html('');
                        $(".facilityContactPerson").html('');
                    }
                });
        }
        $.unblockUI();
    }

    function fillFacilityDetails(){
        $("#fCode").val($('#fName').find(':selected').data('code'));
        var femails = $('#fName').find(':selected').data('emails');
        var fmobilenos = $('#fName').find(':selected').data('mobile-nos');
        var fContactPerson = $('#fName').find(':selected').data('contact-person');
        if($.trim(femails) !='' || $.trim(fmobilenos) !='' || fContactPerson != ''){
            $(".facilityDetails").show();
        }else{
            $(".facilityDetails").hide();
        }
        ($.trim(femails) !='')?$(".femails").show():$(".femails").hide();
        ($.trim(femails) !='')?$(".facilityEmails").html(femails):$(".facilityEmails").html('');
        ($.trim(fmobilenos) !='')?$(".fmobileNumbers").show():$(".fmobileNumbers").hide();
        ($.trim(fmobilenos) !='')?$(".facilityMobileNumbers").html(fmobilenos):$(".facilityMobileNumbers").html('');
        ($.trim(fContactPerson) !='')?$(".fContactPerson").show():$(".fContactPerson").hide();
        ($.trim(fContactPerson) !='')?$(".facilityContactPerson").html(fContactPerson):$(".facilityContactPerson").html('');
    }

    $("input:radio[name=gender]").click(function() {
        if($(this).val() == 'Male' || $(this).val() == 'not_recorded'){
            $('.femaleSection').hide();
            $('input[name="breastfeeding"]').prop('checked', false);
            $('input[name="patientPregnant"]').prop('checked', false);
        }else if($(this).val() == 'Female'){
            $('.femaleSection').show();
        }
    });

    $("input:radio[name=noResult]").click(function() {
        if($(this).val() == 'yes'){
            $('.rejectionReason').show();
            $('.vlResult').css('display','none');
            $('#rejectionReason').addClass('isRequired');
        }else{
            $('.vlResult').css('display','block');
            $('.rejectionReason').hide();
            $('#rejectionReason').removeClass('isRequired');
            $('#rejectionReason').val('');
        }
    });

    $('#tnd').change(function() {
        if($('#tnd').is(':checked')){
            $('#vlResult').attr('readonly',true);
            $('#bdl').attr('disabled',true);
        }else{
            $('#vlResult').attr('readonly',false);
            $('#bdl').attr('disabled',false);
        }
    });
    $('#bdl').change(function() {
        if($('#bdl').is(':checked')){
            $('#vlResult').attr('readonly',true);
            $('#tnd').attr('disabled',true);
        }else{
            $('#vlResult').attr('readonly',false);
            $('#tnd').attr('disabled',false);
        }
    });

    $('#vlResult').on('input',function(e){
        if(this.value != ''){
            $('#tnd,#bdl').attr('disabled',true);
        }else{
            $('#tnd,#bdl').attr('disabled',false);
        }
    });

    function checkARTValue(){
        var artRegimen = $("#artRegimen").val();
        if(artRegimen=='other'){
            $("#newArtRegimen").show();
            $("#newArtRegimen").addClass("isRequired");
        }else{
            $("#newArtRegimen").hide();
            $("#newArtRegimen").removeClass("isRequired");
            $('#newArtRegimen').val("");
        }
    }

    function getAge(){
        var dob = $("#dob").val();
        if($.trim(dob) == ""){
            $("#ageInMonths").val("");
            $("#ageInYears").val("");
            return false;
        }
        //calculate age
        splitDob = dob.split("-");
        var dobDate = new Date(splitDob[1] + splitDob[2]+", "+splitDob[0]);
        var monthDigit = dobDate.getMonth();
        var dobMonth = isNaN(monthDigit) ? 1 : (parseInt(monthDigit)+parseInt(1));
        dobMonth = (dobMonth<10) ? '0'+dobMonth: dobMonth;
        dob = splitDob[2]+'-'+dobMonth+'-'+splitDob[0];
        var years = moment().diff(dob, 'years',false);
        var months = (years == 0)?moment().diff(dob, 'months',false):'';
        $("#ageInYears").val(years); // Gives difference as years
        $("#ageInMonths").val(months); // Gives difference as months
    }

    function checkRejectionReason(){
        var rejectionReason = $("#rejectionReason").val();
        if(rejectionReason == "other"){
            $("#newRejectionReason").show();
            $("#newRejectionReason").addClass("isRequired");
        }else{
            $("#newRejectionReason").hide();
            $("#newRejectionReason").removeClass("isRequired");
            $('#newRejectionReason').val("");
        }
    }

    function validateNow(){
        var format = '<?php echo $arr['sample_code'];?>';
        var sCodeLentgh = $("#sampleCode").val();
        var minLength = '<?php echo $arr['min_length'];?>';
        if((format == 'alphanumeric' || format =='numeric') && sCodeLentgh.length < minLength && sCodeLentgh!=''){
            alert("Sample id length must be a minimum length of "+minLength+" characters");
            return false;
        }

        flag = deforayValidator.init({
            formId: 'vlRequestFormRwd'
        });

        $('.isRequired').each(function () {
            ($(this).val() == '') ? $(this).css('background-color', '#FFFF99') : $(this).css('background-color', '#FFFFFF')
        });
        $("#saveNext").val('save');
        if(flag){
            $.blockUI();
            document.getElementById('vlRequestFormRwd').submit();
        }
    }

    function validateSaveNow(){
        var format = '<?php echo $arr['sample_code'];?>';
        var sCodeLentgh = $("#sampleCode").val();
        var minLength = '<?php echo $arr['min_length'];?>';
        if((format == 'alphanumeric' || format =='numeric') && sCodeLentgh.length < minLength && sCodeLentgh!=''){
            alert("Sample id length must be a minimum length of "+minLength+" characters");
            return false;
        }
        flag = deforayValidator.init({
            formId: 'vlRequestFormRwd'
        });

        $('.isRequired').each(function () {
            ($(this).val() == '') ? $(this).css('background-color', '#FFFF99') : $(this).css('background-color', '#FFFFFF')
        });
        $("#saveNext").val('next');
        if(flag){
            $.blockUI();
            document.getElementById('vlRequestFormRwd').submit();
        }
    }

    function checkPatientReceivesms(val){
        if(val=='yes'){
            $('#patientPhoneNumber').addClass('isRequired');
        }else{
            $('#patientPhoneNumber').removeClass('isRequired');
        }
    }

    function autoFillFocalDetails() {
        labId = $("#labId").val();
        if ($.trim(labId)!='') {
            $("#vlFocalPerson").val($('#labId option:selected').attr('data-focalperson'));
            $("#vlFocalPersonPhoneNumber").val($('#labId option:selected').attr('data-focalphone'));
        }
    }
    function checkNameValidation(tableName,fieldName,obj,fnct)
    {
        if($.trim(obj.value)!=''){
            $.post("../includes/checkDuplicate.php", { tableName: tableName,fieldName : fieldName ,value : obj.value,fnct : fnct, format: "html"},
                function(data){
                    if(data==='1'){
                        setIsPatientNew(false);
                        showModal('patientModal.php?artNo='+obj.value,900,520);
                    }
                    else
                    {
                        setIsPatientNew(true);
                    }
                });
        }
    }
    
    function setIsPatientNew(bVal){
	  if(bVal == false || bVal == 'No') bVal = true; else bVal = false;
	  $("#isPatientNewNo").prop('checked', bVal);
	  $("#isPatientNewYes").prop('checked', !bVal);
    }

     function setPatientDetails(pDetails){
      patientArray = pDetails.split("##");
                                            
		var artnum = $("#artPatientNo").val().trim();
	     $("#artNo").val(artnum);								//Initail Code // $("#patientFirstName").val(patientArray[0]+" "+patientArray[1]); Next two lines recent 
      $("#patientFirstName").val(patientArray[0]);
      
      $("#patientLastName").val(patientArray[2]);
      $("#patientMiddleName").val(patientArray[1]);
																	// 6 is for age
      $("#patientPhoneNumber").val(patientArray[10]);
      if($.trim(patientArray[3])!=''){
        $("#dob").val(patientArray[3]);
        getAge();
      }else if($.trim(patientArray[6])!='' && $.trim(patientArray[6]) != 0){
        $("#ageInYears").val(patientArray[6]);
      }else if($.trim(patientArray[7])!=''){
        $("#ageInMonths").val(patientArray[7]);
      }
      
      
      if($.trim(patientArray[4])!=''){
        if(patientArray[4] == 'Male' || patientArray[4] == 'not_recorded'){
        $('.femaleSection').hide();
        $('input[name="breastfeeding"]').prop('checked', false);
        $('input[name="patientPregnant"]').prop('checked', false);
          if(patientArray[4] == 'Male'){
            $("#genderMale").prop('checked', true);
          }else{
            $("#genderNotRecorded").prop('checked', true);
          }
        }else if(patientArray[4] == 'Female'){
          $('.femaleSection').show();
          $("#genderFemale").prop('checked', true);
          if($.trim(patientArray[4])!=''){
            if($.trim(patientArray[4])=='yes'){
              $("#pregYes").prop('checked', true);
            }else if($.trim(patientArray[4])=='no'){
              $("#pregNo").prop('checked', true);
            }
          }
          if($.trim(patientArray[7])!=''){
            if($.trim(patientArray[7])=='yes'){
              $("#breastfeedingYes").prop('checked', true);
            }else if($.trim(patientArray[7])=='no'){
              $("#breastfeedingNo").prop('checked', true);
            }
          }
        }
      }
      if($.trim(patientArray[9])!=''){
        if(patientArray[9] == 'yes'){
          $("#receivesmsYes").prop('checked', true);
        }else if(patientArray[9] == 'no'){
          $("#receivesmsNo").prop('checked', true);
        }
      }
      if($.trim(patientArray[15])!=''){
      $("#artNo").val($.trim(patientArray[15]));
      }
      if($.trim(patientArray[12])!=''){
          showTesting('rmTesting');
          $("#rmTesting").prop('checked', true);
          $("#rmTestingLastVLDate").val($.trim(patientArray[12]));
      }
      if($.trim(patientArray[13])!=''){
          $("#rmTestingVlValueDemi").val($.trim(patientArray[13]));
      }
  }
    function showPatientList()
    {
        $("#showEmptyResult").hide();
        if($.trim($("#artPatientNo").val())!=''){
            $.post("checkPatientExist.php", { artPatientNo : $("#artPatientNo").val()},
                function(data){
                    if(data >= '1'){
                        showModal('patientModal.php?artNo='+$.trim($("#artPatientNo").val()),900,520);
                    }else{
                        $("#showEmptyResult").show();
                        if(typeof setIsPatientNew == 'function') setIsPatientNew(true);
                    }
                });
        }
    }
    function getPhysicians(obj){
        $.blockUI();
        var dName = $("#reqClinician").val();
        // var cName = $("#fName").val();
        if(dName!=''){
            $.post("../includes/getPhysiciansForClinic.php", {dName:dName},
                function(data){
                    if(data != ""){
                        $("#reqClinicianPhoneNumber").val(data);

                    }
                });
        }
        $.unblockUI();
    }
    
    $(function()
{
  $(".js-example-basic-multiple").select2();
});


    </script>
