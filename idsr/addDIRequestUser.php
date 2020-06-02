<?php
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

$lQuery1="SELECT * FROM facility_details where facility_type='2' AND status='active'";
$lResult1 = $db->rawQuery($lQuery1);
$facility1 = '';
$facility1.="<option data-code='' data-emails='' data-mobile-nos='' data-contact-person='' value=''> -- Select -- </option>";

$appCountryQuery="SELECT * from apps_countries";
$countryResult=$db->query($appCountryQuery);
$rCountry = '';
$rCountry.="<option value=''> -- Select -- </option>";
foreach($countryResult as $countryName){
    $rCountry .= "<option value='".$countryName['country_name']."'>".ucwords($countryName['country_name'])."</option>";
}

$diseasesQuery="SELECT * from diseases";
$diseasesResult=$db->query($diseasesQuery);
$diseases = '';
$diseases.="<option value=''> -- Select -- </option>";
foreach($diseasesResult as $diseasesName){
    $diseases .= "<option value='".$diseasesName['dname']."'>".ucwords($diseasesName['dname'])."</option>";
}

$outcomeQuery="SELECT * from outcome";
$outcomeResult=$db->query($outcomeQuery);
$outCome = '';
$outCome.="<option value=''> -- Select -- </option>";
foreach($outcomeResult as $outComeName){
    $outCome .= "<option value='".$outComeName['name']."'>".ucwords($outComeName['name'])."</option>";
}

$classifyQuery="SELECT * from classify";
$classifyResult=$db->query($classifyQuery);
$classify = '';
$classify.="<option value=''> -- Select -- </option>";
foreach($classifyResult as $classifyName){
    $classify .= "<option value='".$classifyName['cname']."'>".ucwords($classifyName['cname'])."</option>";
}

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
$facility.="<option data-code='".$lResult[0]['facility_id']."' data-emails='".$lResult[0]['facility_emails']."' data-mobile-nos=".$lResult[0]['facility_mobile_numbers']."'' data-contact-person='".$lResult[0]['contact_person']."' value=".$lResult[0]['facility_code']."'>".ucwords($lResult[0]['facility_name'])."</option>";

$pdQuery1="SELECT * from province_details";
$pdResult1=$db->query($pdQuery1);
$province1 = '';
$province1.="<option value=''> -- Select -- </option>";
foreach($pdResult1 as $provinceName1){
    $province1 .= "<option value='".$provinceName1['province_name']."##".$provinceName1['province_code']."'>".ucwords($provinceName1['province_name'])."</option>";
}


$province2 = '';
$province2.="<option value=''> -- Select -- </option>";
foreach($pdResult1 as $provinceName2){
    $province2 .= "<option value='".$provinceName2['province_name']."##".$provinceName2['province_code']."'>".ucwords($provinceName2['province_name'])."</option>";
}

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
$artRegimenQuery="SELECT DISTINCT headings FROM r_art_code_details WHERE nation_identifier ='sudan'";
$artRegimenResult = $db->rawQuery($artRegimenQuery);
$aQuery="SELECT * from r_art_code_details where nation_identifier='sudan' AND art_status ='active'";
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
        <h1><i class="fa fa-edit"></i> GENERIC IDSR REPORT FORM </h1>
        <ol class="breadcrumb">
            <li><a href="../dashboard/index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add IDSR Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- SELECT2 EXAMPLE -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="pull-right" style="font-size:15px;"><span class="mandatory">*</span> indicates required field &nbsp;</div>
            </div>
                <div class="box-body">
                    <div class="widget">
                        <div class="widget-content">
                            <div class="bs-example bs-example-tabs">
                                <ul id="myTab" class="nav nav-tabs">
                                    <li class="active"><a href="#clinicInformation" data-toggle="tab">Generic Form</a></li>
                                    <li><a href="#laboratoryInformation" data-toggle="tab">Laboratory Form</a></li>
                                </ul>
                                <div id="myTabContent" class="tab-content" >
                                    <div class="tab-pane fade in active" id="clinicInformation">
                                        <form class="form-inline" method="post" name="vlRequestFormRwd" id="vlRequestFormRwd" autocomplete="off" action="addGenericHelper.php">
                                        <div class="box-body">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Clinic Information:</h3>
                                                </div>
                                                <div class="box-body">
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
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="firstDate">Date seen at Health Facility <span class="mandatory">*</span></label>
                                                                <input type="text" class="form-control" style="width:100%;" name="firstDate" id="firstDate" placeholder="First Date" title="Please select first date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="disease">Disease/Event (Diagnosis) <span class="mandatory">*</span></label>
                                                                <select class="form-control isRequired" name="disease" id="disease" title="Please choose disease" style="width:100%;" >
                                                                    <?php echo $diseases;  ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="ePID">Epid No (eg. GHA-RRR-DDD-YY-NNN) <span class="mandatory">*</span></label>
                                                                <input type="text" class="form-control" style="width:100%;" name="ePID" id="ePID" value="GHA-" title="Please input ePID No" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="gps">GPS Coordinates </label>
                                                                <input type="text" class="form-control" style="width:100%;" name="gps" id="gps" placeholder="GPS Coordinates" title="GPS Coordinates">
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
                                                                <label for="artNo">Patient ART No. <span class="mandatory">*</span></label>
                                                                <input type="text" name="artNo" id="artNo" class="form-control isRequired" placeholder="Enter ART Number" title="Enter art number" onchange="checkNameValidation('vl_request_form','patient_art_no',this,null)" />
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
                                                                <label for="ageInYears">If DOB unknown, Age in Years </label>
                                                                <input type="text" name="ageInYears" id="ageInYears" class="form-control checkNum" maxlength="2" placeholder="Age in Year" title="Enter age in years"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="ageInMonths">If Age < 1, Age in Months </label>
                                                                <input type="text" name="ageInMonths" id="ageInMonths" class="form-control checkNum" maxlength="2" placeholder="Age in Month" title="Enter age in months"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="patientFirstName">Patient Name (First Name, Last Name) </label>
                                                                <input type="text" name="patientFirstName" id="patientFirstName" class="form-control" placeholder="Enter Patient Name" title="Enter patient name"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="gender">Gender</label><br>
                                                                <label class="radio-inline" style="margin-left:0px;">
                                                                    <input type="radio" class="" id="genderMale" name="gender" value="male" title="Please check gender">Male
                                                                </label>
                                                                <label class="radio-inline" style="margin-left:0px;">
                                                                    <input type="radio" class="" id="genderFemale" name="gender" value="female" title="Please check gender">Female
                                                                </label>
                                                                <label class="radio-inline" style="margin-left:0px;">
                                                                    <input type="radio" class="" id="genderNotRecorded" name="gender" value="not_recorded" title="Please check gender">Not Recorded
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="gender">Patient Admission Status</label><br>
                                                                <label class="radio-inline" style="margin-left:0px;">
                                                                    <input type="radio" class="" id="receivesmsYes" name="receiveSms" value="yes" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> In Patient
                                                                </label>
                                                                <label class="radio-inline" style="margin-left:0px;">
                                                                    <input type="radio" class="" id="receivesmsNo" name="receiveSms" value="no" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> Out Patient
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="admissionDate">Date of Admission (If In-Patient)</label>
                                                                <input type="text" class="form-control" style="width:100%;" name="admissionDate" id="admissionDate" placeholder="Admission Date" title="Please select admission date" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="gaurdianName">Gaurdian Name (if child is 12 years or below) <span class="mandatory"></span></label>
                                                                <input type="text" name="gaurdianName" id="gaurdianName" class="form-control" placeholder="Enter Gaurdian Name" title="Enter gaurdianName"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="noHouseHold">No of People in household </label>
                                                                <input type="text" name="noHouseHold" id="noHouseHold" class="form-control checkNum" maxlength="2" placeholder="Enter No of People in Household" title="noHouseHold"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="residentialAdd">Patient's Residential Address (HNo) </label>
                                                                <input type="text" name="residentialAdd" id="residentialAdd" class="form-control"  placeholder="HNo/Locaction/Community" title="residentialAdd"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="patientPhoneNumber">Phone Number</label>
                                                                <input type="text" name="patientPhoneNumber" id="patientPhoneNumber" class="form-control checkNum" maxlength="15" placeholder="Enter Phone Number" title="Enter phone number"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="rCountry">Country of Residence </label>
                                                                <select class="form-control isRequired" name="rCountry" id="rCountry" title="Please choose country" style="width:100%;">
                                                                    <?php echo $rCountry;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="region">Region of Residence<span class="mandatory"></span></label>
                                                                <select class="form-control isRequired" name="region" id="region" title="Please choose region" style="width:100%;" onchange="getProvinceDistricts1(this);" >
                                                                    <?php echo $province1;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="rDistrict">District of Residence <span class="mandatory"></span></label>
                                                                <select class="form-control isRequired" name="rDistrict" id="rDistrict" title="Please choose residential district" style="width:100%;" onchange="getFacilities1(this);">
                                                                    <option value=""> -- Select -- </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="sDistrict">Sub-District of Residence<span class="mandatory"></span></label>
                                                                <input type="text" name="sDistrict" id="sDistrict" class="form-control"  placeholder="Enter Sub-District" title="Enter Sub-District"/>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="onSetDate">Date of Onset (First Symptom)</label>
                                                                <input type="text" class="form-control" style="width:100%;" name="onSetDate" id="onSetDate" placeholder="Date of Onset" title="Please select onset date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="symptoms">Clinical signs/symptoms </label>
                                                                <input type="text" name="symptoms" id="symptoms" class="form-control"  placeholder="Enter Signs/Symptoms" title="Enter Signs/Symptoms"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="outCome">Outcome </label>
                                                                <select class="form-control isRequired" name="outCome" id="outCome" title="Please choose outcome" style="width:100%;" >
                                                                    <?php echo $outCome;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="fClassification">Final Classification</label>
                                                                <select class="form-control isRequired" name="fClassification" id="fClassification" title="Please choose classification" style="width:100%;" >
                                                                    <?php echo $classify;?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="notifyDate">Date Facility notified District</label>
                                                                <input type="text" class="form-control" style="width:100%;" name="notifyDate" id="notifyDate" placeholder="Date of Notification" title="Please select notification date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="sendDate">Date form sent to District</label>
                                                                <input type="text" class="form-control" style="width:100%;" name="sendDate" id="sendDate" placeholder="Date form sent" title="Please select send date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="updateDate">Date of Last Update</label>
                                                                <input type="text" class="form-control" style="width:100%;" name="updateDate" id="updateDate" placeholder="Date of update" title="Please select update date" >
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3 col-md-3">
                                                            <div class="form-group">
                                                                <label for="Clinician">Clinician</label>
                                                                <input type="text" name="Clinician" id="Clinician" class="form-control"  placeholder="Enter Name" title="Enter Name"/>
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
                                            <input type="hidden" name="testtype" id="testType" value="generic"/>

                                            <?php if($arr['sample_code']=='auto' || $arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
                                                <input type="hidden" name="sampleCodeFormat" id="sampleCodeFormat" value="<?php echo $sampleCodeFormat;?>"/>
                                                <input type="hidden" name="sampleCodeKey" id="sampleCodeKey" value="<?php echo $sampleCodeKey;?>"/>
                                            <?php } ?>
                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateSaveNow();return false;">Save and Next</a>
                                            <a href="addGenericIDSR.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="laboratoryInformation">
                                        <form class="form-inline" method="post" name="vlRequestFormRwd1" id="vlRequestFormRwd1" autocomplete="off" action="addGenericHelper.php">
                                            <div class="box-body">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Laboratory Information:</h3>
                                                    </div>
                                                    <div class="box-body">
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
                                                                    <label for="fCode1">LAB Centre Code </label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="fCode1" id="fCode1" readonly="" placeholder="ART Center Code" title="Please enter ART center code"  value="<?php echo $lResult[0]['facility_code']?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="firstDate1">Date Specimen Collected <span class="mandatory">*</span></label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="firstDate1" id="firstDate1" placeholder="Specimen Collection Date" title="Please select first date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="labDate">Date Specimen Sent to Lab <span class="mandatory">*</span></label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="labDate" id="labDate" placeholder="Date speciment sent to Lab" title="Please select lab date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="ePID">Epid No (eg. GHA-RRR-DDD-YY-NNN) <span class="mandatory">*</span></label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="ePID" id="ePID" value="GHA-" title="Please input ePID No" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="personSending">Person sending specimen </label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="personSending" id="personSending" placeholder="Name-Designation-Email-Phone" title="Person Sending Specimen">
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
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Patient Information</h3>&nbsp;&nbsp;&nbsp;
                                                        <input style="width:30%;" type="text" name="artPatientNo1 " id="artPatientNo1" class="" placeholder="Enter ART Number or Patient Name" title="Enter art number or patient name"/>&nbsp;&nbsp;
                                                        <a style="margin-top:-0.35%;" href="javascript:void(0);" class="btn btn-default btn-sm" onclick="showPatientList1();"><i class="fa fa-search">&nbsp;</i>Search</a><span id="showEmptyResult1" style="display:none;color: #ff0000;font-size: 15px;"><b>&nbsp;No Patient Found</b></span>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="artNo1">Patient LAB No. <span class="mandatory">*</span></label>
                                                                    <input type="text" name="artNo1" id="artNo1" class="form-control isRequired" placeholder="Enter ART Number" title="Enter art number" onchange="checkNameValidation1('vl_request_form','patient_art_no',this,null)" />
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="dob1">Date of Birth </label>
                                                                    <input type="text" name="dob1" id="dob1" class="form-control date" placeholder="Enter DOB" title="Enter dob" onchange="getAge1();"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="ageInYears1">If DOB unknown, Age in Years </label>
                                                                    <input type="text" name="ageInYears1" id="ageInYears1" class="form-control checkNum" maxlength="2" placeholder="Age in Year" title="Enter age in years"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="ageInMonths1">If Age < 1, Age in Months </label>
                                                                    <input type="text" name="ageInMonths1" id="ageInMonths1" class="form-control checkNum" maxlength="2" placeholder="Age in Month" title="Enter age in months"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="patientFirstName1">Patient Name (First Name, Last Name) </label>
                                                                    <input type="text" name="patientFirstName1" id="patientFirstName1" class="form-control" placeholder="Enter Patient Name" title="Enter patient name"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="gender">Gender</label><br>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="genderMale1" name="gender" value="male" title="Please check gender">Male
                                                                    </label>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="genderFemale1" name="gender" value="female" title="Please check gender">Female
                                                                    </label>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="genderNotRecorded1" name="gender" value="not_recorded" title="Please check gender">Not Recorded
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="specimenCondition">Specimen Condition</label><br>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="receivesmsYes1" name="specimenCondition" value="yes" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> Adequate
                                                                    </label>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="receivesmsNo1" name="specimenCondition" value="no" title="Patient consent to receive SMS" onclick="checkPatientReceivesms(this.value);"> Not Adequate
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="test">Type of Test Performed</label><br>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="testRDT" name="test" value="RDT" title="Please check test">RDT
                                                                    </label>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="testCulture" name="test" value="Culture" title="Please check test">Culture
                                                                    </label>
                                                                    <label class="radio-inline" style="margin-left:0px;">
                                                                        <input type="radio" class="" id="testPCR" name="test" value="PCR" title="Please check test">PCR
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xs-4 col-md-4">
                                                                <div class="form-group">
                                                                    <label for="admissionDate1">Date Lab Received Specimen</label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="admissionDate1" id="admissionDate1" placeholder="Date Lab Received Specimen" title="Please select admission date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-md-4">
                                                                <div class="form-group">
                                                                    <label for="labResult">Final Lab Result<span class="mandatory"></span></label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="labResult" id="labResult" placeholder="Final Lab Results" title="labResult" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-md-4">
                                                                <div class="form-group">
                                                                    <label for="organismTypes">Types of Organisms Isolated <span class="mandatory"></span></label>
                                                                    <input type="text" name="organismTypes" id="organismTypes" class="form-control"  placeholder="Enter Organisms Isolated" title="organismTypes"/>
                                                                </div>
                                                            </div>


                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="organismSensitivity">Drugs to which isolated strain is sensitive<span class="mandatory"></span></label>
                                                                    <input type="text" name="organismSensitivity" id="organismSensitivity" class="form-control"  placeholder="Drugs to which isolated strain is sensitive" title="Enter Drug Sensitivity"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="organismResistance">Drugs to which isolated strain is resistant<span class="mandatory"></span></label>
                                                                    <input type="text" name="organismResistance" id="organismResistance" class="form-control"  placeholder="Drugs to which isolated strain is resistant" title="Enter Drug Resistivity"/>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="notifyDate1">Date Result Sent to Clinician</label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="notifyDate1" id="notifyDate1" placeholder="Date Result Sent to Clinician" title="Please select notification date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="sendDate1">Date Lab Sent Results to District</label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="sendDate1" id="sendDate1" placeholder="Date Lab Sent Results to District" title="Please select send date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="updateDate1">Date District Received Results</label>
                                                                    <input type="text" class="form-control" style="width:100%;" name="updateDate1" id="updateDate1" placeholder="Date District Received Results" title="Please select update date" >
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-3 col-md-3">
                                                                <div class="form-group">
                                                                    <label for="labPersonnel">Lab Personnel</label>
                                                                    <input type="text" name="labPersonnel" id="labPersonnel" class="form-control"  placeholder="Name-Designation-Email-Phone" title="Enter Name"/>
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

                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow1();return false;">Save</a>
                                            <input type="hidden" name="saveNext" id="saveNext"/>
                                            <input type="hidden" name="sampleCodeTitle" id="sampleCodeTitle" value="<?php echo $arr['sample_code'];?>"/>
                                            <input type="hidden" name="sampleCode" id="sampleCode" value="<?php echo $sampleCode;?>"/>
                                            <input type="hidden" name="testtype" id="testType" value="lab"/>

                                            <?php if($arr['sample_code']=='auto' || $arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
                                                <input type="hidden" name="sampleCodeFormat" id="sampleCodeFormat" value="<?php echo $sampleCodeFormat;?>"/>
                                                <input type="hidden" name="sampleCodeKey" id="sampleCodeKey" value="<?php echo $sampleCodeKey;?>"/>
                                            <?php } ?>
                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="validateSaveNow1();return false;">Save and Next</a>
                                            <a href="addGenericIDSR.php" class="btn btn-default"> Cancel</a>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        $('#admissionDate,#updateDate,#onSetDate,#notifyDate,#sendDate,#firstDate,#labDate,#admissionDate1,#updateDate1,#firstDate1,#sendDate1,#notifyDate1').datetimepicker({
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
        $('#admissionDate,#updateDate,#onSetDate,#notifyDate,#sendDate,#firstDate,#labDate,#admissionDate1,#updateDate1,#firstDate1,#sendDate1,#notifyDate1').mask('99-aaa-9999 99:99');
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

    function getProvinceDistricts2(obj){
        $.blockUI();
        var cName = $("#fName1").val();
        var pName = $("#province1").val();
        if(pName!='' && provinceName && facilityName){
            facilityName = false;
        }
        if(pName!=''){
            if(provinceName){
                $.post("../includes/getFacilityForClinic.php", { pName : pName},
                    function(data){
                        if(data != ""){
                            details = data.split("###");
                            $("#district1").html(details[1]);
                            $("#fName1").html("<option data-code='' data-emails='' data-mobile-nos='' data-contact-person='' value=''> -- Select -- </option>");
                            $("#fCode1").val('');
                            $(".facilityDetails1").hide();
                            $(".facilityEmails1").html('');
                            $(".facilityMobileNumbers1").html('');
                            $(".facilityContactPerson1").html('');
                        }
                    });
            }
            <?php
            if($arr['sample_code']=='auto'){
            ?>
            pNameVal = pName.split("##");
            sCode = '<?php echo date('ymd');?>';
            sCodeKey = '<?php echo $maxId;?>';
            $("#sampleCode1").val(pNameVal[1]+sCode+sCodeKey);
            $("#sampleCodeFormat1").val(pNameVal[1]+sCode);
            $("#sampleCodeKey1").val(sCodeKey);
            <?php
            }else if($arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
            $("#sampleCode1").val('<?php echo $prefix.$mnthYr.$maxId;?>');
            $("#sampleCodeFormat1").val('<?php echo $prefix.$mnthYr;?>');
            $("#sampleCodeKey1").val('<?php echo $maxId;?>');
            <?php
            }
            ?>
        }else if(pName=='' && cName==''){
            provinceName = true;
            facilityName = true;

        }
        $.unblockUI();
    }

    function getProvinceDistricts1(obj){
        $.blockUI();
        var cName = $("#sDistrict").val();
        var pName = $("#region").val();
        if(pName!='' && provinceName && facilityName){
            facilityName = false;
        }
        if(pName!=''){
            if(provinceName){
                $.post("../includes/getFacilityForClinic.php", { pName : pName},
                    function(data){
                        if(data != ""){
                            details = data.split("###");
                            $("#rDistrict").html(details[1]);
                            $("#sDistrict").html("<option data-code='' data-emails='' data-mobile-nos='' data-contact-person='' value=''> -- Select -- </option>");

                        }
                    });
            }
            <?php
            if($arr['sample_code']=='auto'){
            ?>
            pNameVal = pName.split("##");
            sCode = '<?php echo date('ymd');?>';
            sCodeKey = '<?php echo $maxId;?>';
            $("#sampleCode").val(pNameVal[1]+sCode+sCodeKey);
            $("#sampleCodeFormat").val(pNameVal[1]+sCode);
            $("#sampleCodeKey").val(sCodeKey);
            <?php
            }else if($arr['sample_code']=='YY' || $arr['sample_code']=='MMYY'){ ?>
            $("#sampleCode").val('<?php echo $prefix.$mnthYr.$maxId;?>');
            $("#sampleCodeFormat").val('<?php echo $prefix.$mnthYr;?>');
            $("#sampleCodeKey").val('<?php echo $maxId;?>');
            <?php
            }
            ?>
        }else if(pName=='' && cName==''){
            provinceName = true;
            facilityName = true;
            $("#region").html("<?php echo $province2;?>");

        }
        $.unblockUI();
    }


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
        if($(this).val() == 'male' || $(this).val() == 'not_recorded'){
            $('.femaleSection').hide();
            $('input[name="breastfeeding"]').prop('checked', false);
            $('input[name="patientPregnant"]').prop('checked', false);
        }else if($(this).val() == 'female'){
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

    function validateNow1(){
        var format = '<?php echo $arr['sample_code'];?>';
        var sCodeLentgh = $("#sampleCode").val();
        var minLength = '<?php echo $arr['min_length'];?>';
        if((format == 'alphanumeric' || format =='numeric') && sCodeLentgh.length < minLength && sCodeLentgh!=''){
            alert("Sample id length must be a minimum length of "+minLength+" characters");
            return false;
        }

        flag = deforayValidator.init({
            formId: 'vlRequestFormRwd1'
        });

        $('.isRequired').each(function () {
            ($(this).val() == '') ? $(this).css('background-color', '#FFFF99') : $(this).css('background-color', '#FFFFFF')
        });
        $("#saveNext").val('save');
        if(flag){
            $.blockUI();
            document.getElementById('vlRequestFormRwd1').submit();
        }
    }

    function validateSaveNow1(){
        var format = '<?php echo $arr['sample_code'];?>';
        var sCodeLentgh = $("#sampleCode").val();
        var minLength = '<?php echo $arr['min_length'];?>';
        if((format == 'alphanumeric' || format =='numeric') && sCodeLentgh.length < minLength && sCodeLentgh!=''){
            alert("Sample id length must be a minimum length of "+minLength+" characters");
            return false;
        }
        flag = deforayValidator.init({
            formId: 'vlRequestFormRwd1'
        });

        $('.isRequired').each(function () {
            ($(this).val() == '') ? $(this).css('background-color', '#FFFF99') : $(this).css('background-color', '#FFFFFF')
        });
        $("#saveNext").val('next');
        if(flag){
            $.blockUI();
            document.getElementById('vlRequestFormRwd1').submit();
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
                        showModal('patientModal.php?artNo='+obj.value,900,520);
                    }
                });
        }
    }
    function setPatientDetails(pDetails){
        patientArray = pDetails.split("##");
        $("#patientFirstName").val(patientArray[0]+" "+patientArray[1]);
        $("#patientPhoneNumber").val(patientArray[8]);
        if($.trim(patientArray[3])!=''){
            $("#dob").val(patientArray[3]);
            getAge();
        }else if($.trim(patientArray[4])!='' && $.trim(patientArray[4]) != 0){
            $("#ageInYears").val(patientArray[4]);
        }else if($.trim(patientArray[5])!=''){
            $("#ageInMonths").val(patientArray[5]);
        }


        if($.trim(patientArray[2])!=''){
            if(patientArray[2] == 'male' || patientArray[2] == 'not_recorded'){
                $('.femaleSection').hide();
                $('input[name="breastfeeding"]').prop('checked', false);
                $('input[name="patientPregnant"]').prop('checked', false);
                if(patientArray[2] == 'male'){
                    $("#genderMale").prop('checked', true);
                }else{
                    $("#genderNotRecorded").prop('checked', true);
                }
            }else if(patientArray[2] == 'female'){
                $('.femaleSection').show();
                $("#genderFemale").prop('checked', true);
                if($.trim(patientArray[6])!=''){
                    if($.trim(patientArray[6])=='yes'){
                        $("#pregYes").prop('checked', true);
                    }else if($.trim(patientArray[6])=='no'){
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

    function getFacilities2(obj){
        $.blockUI();
        var dName = $("#district1").val();
        var cName = $("#fName1").val();
        if(dName!=''){
            $.post("../includes/getFacilityForClinic.php", {dName:dName,cliName:cName},
                function(data){
                    if(data != ""){
                        $("#fName1").html(data);
                        $(".facilityDetails1").hide();
                        $(".facilityEmails1").html('');
                        $(".facilityMobileNumbers1").html('');
                        $(".facilityContactPerson1").html('');
                    }
                });
        }
        $.unblockUI();
    }

    function getFacilities1(obj){
        $.blockUI();
        var dName = $("#rDistrict").val();
        var cName = $("#sDistrict").val();
        if(dName!=''){
            $.post("../includes/getFacilityForClinic.php", {dName:dName,cliName:cName},
                function(data){
                    if(data != ""){
                        $("#sDistrict").html(data);

                    }
                });
        }
        $.unblockUI();
    }

    function fillFacilityDetails1(){
        $("#fCode1").val($('#fName1').find(':selected').data('code'));
        var femails1 = $('#fName1').find(':selected').data('emails');
        var fmobilenos1 = $('#fName1').find(':selected').data('mobile-nos');
        var fContactPerson1 = $('#fName1').find(':selected').data('contact-person');
        if($.trim(femails1) !='' || $.trim(fmobilenos1) !='' || fContactPerson1 != ''){
            $(".facilityDetails1").show();
        }else{
            $(".facilityDetails1").hide();
        }
        ($.trim(femails1) !='')?$(".femails1").show():$(".femails1").hide();
        ($.trim(femails1) !='')?$(".facilityEmails1").html(femails1):$(".facilityEmails1").html('');
        ($.trim(fmobilenos1) !='')?$(".fmobileNumbers1").show():$(".fmobileNumbers1").hide();
        ($.trim(fmobilenos1) !='')?$(".facilityMobileNumbers1").html(fmobilenos1):$(".facilityMobileNumbers1").html('');
        ($.trim(fContactPerson1) !='')?$(".fContactPerson1").show():$(".fContactPerson1").hide();
        ($.trim(fContactPerson1) !='')?$(".facilityContactPerson1").html(fContactPerson1):$(".facilityContactPerson1").html('');
    }

    function getAge1(){
        var dob = $("#dob1").val();
        if($.trim(dob) == ""){
            $("#ageInMonths1").val("");
            $("#ageInYears1").val("");
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
        $("#ageInYears1").val(years); // Gives difference as years
        $("#ageInMonths1").val(months); // Gives difference as months
    }

    function checkNameValidation1(tableName,fieldName,obj,fnct)
    {
        if($.trim(obj.value)!=''){
            $.post("../includes/checkDuplicate.php", { tableName: tableName,fieldName : fieldName ,value : obj.value,fnct : fnct, format: "html"},
                function(data){
                    if(data==='1'){
                        showModal('patientModal.php?artNo='+obj.value,900,520);
                    }
                });
        }
    }

    function setPatientDetails1(pDetails){
        patientArray = pDetails.split("##");
        $("#patientFirstName1").val(patientArray[0]+" "+patientArray[1]);
        $("#patientPhoneNumber1").val(patientArray[8]);
        if($.trim(patientArray[3])!=''){
            $("#dob1").val(patientArray[3]);
            getAge1();
        }else if($.trim(patientArray[4])!='' && $.trim(patientArray[4]) != 0){
            $("#ageInYears1").val(patientArray[4]);
        }else if($.trim(patientArray[5])!=''){
            $("#ageInMonths1").val(patientArray[5]);
        }


        if($.trim(patientArray[2])!=''){
            if(patientArray[2] == 'male' || patientArray[2] == 'not_recorded'){
                $('.femaleSection').hide();
                $('input[name="breastfeeding"]').prop('checked', false);
                $('input[name="patientPregnant"]').prop('checked', false);
                if(patientArray[2] == 'male'){
                    $("#genderMale1").prop('checked', true);
                }else{
                    $("#genderNotRecorded1").prop('checked', true);
                }
            }else if(patientArray[2] == 'female'){
                $('.femaleSection').show();
                $("#genderFemale1").prop('checked', true);
                if($.trim(patientArray[6])!=''){
                    if($.trim(patientArray[6])=='yes'){
                        $("#pregYes1").prop('checked', true);
                    }else if($.trim(patientArray[6])=='no'){
                        $("#pregNo1").prop('checked', true);
                    }
                }
                if($.trim(patientArray[7])!=''){
                    if($.trim(patientArray[7])=='yes'){
                        $("#breastfeedingYes1").prop('checked', true);
                    }else if($.trim(patientArray[7])=='no'){
                        $("#breastfeedingNo1").prop('checked', true);
                    }
                }
            }
        }
        if($.trim(patientArray[9])!=''){
            if(patientArray[9] == 'yes'){
                $("#receivesmsYes1").prop('checked', true);
            }else if(patientArray[9] == 'no'){
                $("#receivesmsNo1").prop('checked', true);
            }
        }
        if($.trim(patientArray[15])!=''){
            $("#artNo1").val($.trim(patientArray[15]));
        }
    }
    function showPatientList1()
    {
        $("#showEmptyResult1").hide();
        if($.trim($("#artPatientNo1").val())!=''){
            $.post("checkPatientExist.php", { artPatientNo : $("#artPatientNo1").val()},
                function(data){
                    if(data >= '1'){
                        showModal('patientModal1.php?artNo1='+$.trim($("#artPatientNo1").val()),900,520);
                    }else{
                        $("#showEmptyResult1").show();
                    }
                });
        }
    }

    </script>