<?php
session_start();
ob_start();

    ini_set( "display_errors", 1 );
	error_reporting( E_ALL );
include('../includes/MysqliDb.php');
include('../General.php');
$general=new Deforay_Commons_General();
$tableName="di_request_form";
$tableName1="activity_log";
$vlTestReasonTable="r_vl_test_reasons";
$fDetails="facility_details";
try {
    //var_dump($_POST);die();
    $status = 6;
    $configQuery ="SELECT value FROM global_config where name='auto_approval'";
    $configResult = $db->rawQuery($configQuery);
    if(isset($configResult[0]['value']) && trim($configResult[0]['value']) == 'yes'){
        $status = 7;
    }
    if(isset($_POST['noResult']) && $_POST['noResult']=='yes'){
        $status = 4;
    }
    //add province
    if(isset($_POST['province'])) {
        $splitProvince = explode("##", $_POST['province']);
        if (isset($splitProvince[0]) && trim($splitProvince[0]) != '') {
            $_POST['province'] = $splitProvince[0];
        }
    }
    if(isset($_POST['region'])) {
        $splitRegion = explode("##", $_POST['region']);
        if (isset($splitRegion[0]) && trim($splitRegion[0]) != '') {
            $_POST['region'] = $splitRegion[0];
        }
    }
    //var_dump($_POST);die;

    if(isset($_POST['firstDate1']) && trim($_POST['firstDate1'])!=""){
        $sampleDate = explode(" ",$_POST['firstDate1']);
        $_POST['firstDate1']=$general->dateFormat($sampleDate[0])." ".$sampleDate[1];
    }else{
        $_POST['firstDate1'] = NULL;
    }
    if(isset($_POST['firstDate']) && trim($_POST['firstDate'])!=""){
        $sampleDate = explode(" ",$_POST['firstDate']);
        $_POST['firstDate']=$general->dateFormat($sampleDate[0])." ".$sampleDate[1];
    }else{
        $_POST['firstDate'] = NULL;
    }

    if(isset($_POST['admissionDate']) && trim($_POST['admissionDate'])!=""){
        $sampleDate = explode(" ",$_POST['admissionDate']);
        $_POST['admissionDate']=$general->dateFormat($sampleDate[0])." ".$sampleDate[1];
    }else{
        $_POST['admissionDate'] = NULL;
    }

    if(isset($_POST['admissionDate1']) && trim($_POST['admissionDate1'])!=""){
        $sampleDate = explode(" ",$_POST['admissionDate1']);
        $_POST['admissionDate1']=$general->dateFormat($sampleDate[0])." ".$sampleDate[1];
    }else{
        $_POST['admissionDate1'] = NULL;
    }

    if(isset($_POST['onSetDate']) && trim($_POST['onSetDate'])!=""){
        $sampleDate = explode(" ",$_POST['onSetDate']);
        $_POST['onSetDate']=$general->dateFormat($sampleDate[0])." ".$sampleDate[1];
    }else{
        $_POST['onSetDate'] = NULL;
    }

    if(isset($_POST['dob']) && trim($_POST['dob'])!=""){
        $_POST['dob']=$general->dateFormat($_POST['dob']);
    }else{
        $_POST['dob'] = NULL;
    }
    if(isset($_POST['dob1']) && trim($_POST['dob1'])!=""){
        $_POST['dob1']=$general->dateFormat($_POST['dob1']);
    }else{
        $_POST['dob1'] = NULL;
    }

    if(isset($_POST['notifyDate']) && trim($_POST['notifyDate'])!=""){
        $_POST['notifyDate']=$general->dateFormat($_POST['notifyDate']);
    }else{
        $_POST['notifyDate'] = NULL;
    }

    if(isset($_POST['notifyDate1']) && trim($_POST['notifyDate1'])!=""){
        $_POST['notifyDate1']=$general->dateFormat($_POST['notifyDate1']);
    }else{
        $_POST['notifyDate1'] = NULL;
    }

    if(isset($_POST['sendDate']) && trim($_POST['sendDate'])!=""){
        $_POST['sendDate']=$general->dateFormat($_POST['sendDate']);
    }else{
        $_POST['sendDate'] = NULL;
    }

    if(isset($_POST['sendDate1']) && trim($_POST['sendDate1'])!=""){
        $_POST['sendDate1']=$general->dateFormat($_POST['sendDate1']);
    }else{
        $_POST['sendDate1'] = NULL;
    }

    if(isset($_POST['updateDate1']) && trim($_POST['updateDate1'])!=""){
        $_POST['updateDate1']=$general->dateFormat($_POST['updateDate1']);
    }else{
        $_POST['updateDate1'] = NULL;
    }

    if(isset($_POST['updateDate']) && trim($_POST['updateDate'])!=""){
        $_POST['updateDate']=$general->dateFormat($_POST['updateDate']);
    }else{
        $_POST['updateDate'] = NULL;
    }

    if(isset($_POST['labDate']) && trim($_POST['labDate'])!=""){
        $_POST['labDate']=$general->dateFormat($_POST['labDate']);
    }else{
        $_POST['labDate'] = NULL;
    }

    if(isset($_POST['newArtRegimen']) && trim($_POST['newArtRegimen'])!=""){
        $artQuery ="SELECT art_id,art_code FROM r_art_code_details where (art_code='".$_POST['newArtRegimen']."' OR art_code='".strtolower($_POST['newArtRegimen'])."' OR art_code='".ucfirst(strtolower($_POST['newArtRegimen']))."') AND nation_identifier='sudan'";
        $artResult = $db->rawQuery($artQuery);
        if(!isset($artResult[0]['art_id'])){
            $data=array(
                'art_code'=>$_POST['newArtRegimen'],
                'nation_identifier'=>'sudan',
                'parent_art'=>'1'
            );
            $result=$db->insert('r_art_code_details',$data);
            $_POST['artRegimen'] = $_POST['newArtRegimen'];
        }else{
            $_POST['artRegimen'] = $artResult[0]['art_code'];
        }
    }
    //var_dump($_POST);die;
    //update facility code
    if(isset($_POST['fCode'])) {
        if (trim($_POST['fCode']) != '') {
            $fData = array('facility_code' => $_POST['fCode']);
            $db = $db->where('facility_id', $_POST['fName']);
            $id = $db->update($fDetails, $fData);
        }
    }
    if(isset($_POST['fCode1'])) {
        if (trim($_POST['fCode1']) != '') {
            $fData = array('facility_code' => $_POST['fCode1']);
            $db = $db->where('facility_id', $_POST['fName']);
            $id = $db->update($fDetails, $fData);
        }
    }
    //update facility emails
    //if(trim($_POST['emailHf'])!=''){
    //   $fData = array('facility_emails'=>$_POST['emailHf']);
    //   $db=$db->where('facility_id',$_POST['fName']);
    //   $id=$db->update($fDetails,$fData);
    //}

    if(isset($_POST['gender']) && trim($_POST['gender'])=='male'){
        $_POST['patientPregnant']='';
        $_POST['breastfeeding']='';
    }
    $instanceId = '';
    if(isset($_SESSION['instanceId'])){
        $instanceId = $_SESSION['instanceId'];
    }
    $testingPlatform = '';
    if(isset($_POST['testingPlatform']) && trim($_POST['testingPlatform'])!=''){
        $platForm = explode("##",$_POST['testingPlatform']);
        $testingPlatform = $platForm[0];
    }
    if(isset($_POST['sampleReceivedOn']) && trim($_POST['sampleReceivedOn'])!=""){
        $sampleReceivedDateLab = explode(" ",$_POST['sampleReceivedOn']);
        $_POST['sampleReceivedOn']=$general->dateFormat($sampleReceivedDateLab[0])." ".$sampleReceivedDateLab[1];
    }else{
        $_POST['sampleReceivedOn'] = NULL;
    }
    if(isset($_POST['sampleTestingDateAtLab']) && trim($_POST['sampleTestingDateAtLab'])!=""){
        $sampleTestingDateAtLab = explode(" ",$_POST['sampleTestingDateAtLab']);
        $_POST['sampleTestingDateAtLab']=$general->dateFormat($sampleTestingDateAtLab[0])." ".$sampleTestingDateAtLab[1];
    }else{
        $_POST['sampleTestingDateAtLab'] = NULL;
    }
    if(isset($_POST['resultDispatchedOn']) && trim($_POST['resultDispatchedOn'])!=""){
        $resultDispatchedOn = explode(" ",$_POST['resultDispatchedOn']);
        $_POST['resultDispatchedOn']=$general->dateFormat($resultDispatchedOn[0])." ".$resultDispatchedOn[1];
    }else{
        $_POST['resultDispatchedOn'] = NULL;
    }

    if(isset($_POST['newRejectionReason']) && trim($_POST['newRejectionReason'])!=""){
        $rejectionReasonQuery ="SELECT rejection_reason_id FROM r_sample_rejection_reasons where rejection_reason_name='".$_POST['newRejectionReason']."' OR rejection_reason_name='".strtolower($_POST['newRejectionReason'])."' OR rejection_reason_name='".ucfirst(strtolower($_POST['newRejectionReason']))."'";
        $rejectionResult = $db->rawQuery($rejectionReasonQuery);
        if(!isset($rejectionResult[0]['rejection_reason_id'])){
            $data=array(
                'rejection_reason_name'=>$_POST['newRejectionReason'],
                'rejection_type'=>'general',
                'rejection_reason_status'=>'active'
            );
            $id=$db->insert('r_sample_rejection_reasons',$data);
            $_POST['rejectionReason'] = $id;
        }else{
            $_POST['rejectionReason'] = $rejectionResult[0]['rejection_reason_id'];
        }
    }

    $isRejection = false;
    if(isset($_POST['noResult']) && $_POST['noResult'] =='yes'){
        $isRejection = true;
        $_POST['vlResult'] = '';
    }

    if(isset($_POST['tnd']) && $_POST['tnd'] =='yes' && $isRejection == false){
        $_POST['vlResult'] = 'Target Not Detected';
    }
    if(isset($_POST['bdl']) && $_POST['bdl'] =='bdl' && $isRejection == false){
        $_POST['vlResult'] = '< 20';
    }

    $_POST['result'] = '';
    if(isset($_POST['vlResult']) && trim($_POST['vlResult']) != ''){
        $_POST['result'] = $_POST['vlResult'];
    }
    //var_dump($_POST);die;
    //check existing sample code
    if(isset($_POST['sampleCode'])) {
        $existSampleQuery = "SELECT sample_code FROM di_request_form where sample_code='" . trim($_POST['sampleCode']) . "'";
        $existResult = $db->rawQuery($existSampleQuery);
        //var_dump($existResult);die;
        if ($existResult) {
            if (isset($_POST['sampleCodeKey']) && $_POST['sampleCodeKey'] != '') {
                $sCode = $_POST['sampleCodeKey'] + 1;
                $strparam = strlen($sCode);
                $zeros = substr("000", $strparam);
                $maxId = $zeros . $sCode;
                $_POST['sampleCode'] = $_POST['sampleCodeFormat'] . $maxId;
                $_POST['sampleCodeKey'] = $maxId;
                //var_dump($_POST['sampleCode'] );die;
            } else {
                $_SESSION['alertMsg'] = "Please check your sample ID";
                header("location:genericIDSR.php");
            }
        }
    }
    $pfName = null;
    if(isset($_POST['patientFirstName1'])){
        $pfName = $_POST['patientFirstName1'];
    }
    $ageInYears1 = null;
    if(isset($_POST['ageInYears1'])){
        $ageInYears1 = $_POST['ageInYears1'];
    }
    $ageInMonths1 = null;
    if(isset($_POST['ageInMonths1'])){
        $ageInMonths1 = $_POST['ageInMonths1'];
    }
    $artNo1 = null;
    if(isset($_POST['artNo1'])){
        $artNo1 = $_POST['artNo1'];
    }
    $labPersonnel = null;
    if(isset($_POST['labPersonnel'])){
        $labPersonnel = $_POST['labPersonnel'];
    }

    //var_dump($_POST);die;
    $vldata=array(
        'vlsm_instance_id'=>$instanceId,
        'vlsm_country_id'=>1,
        'sample_code_title'=>(isset($_POST['sampleCodeTitle']) && $_POST['sampleCodeTitle']!='') ? $_POST['sampleCodeTitle'] :  'auto' ,
        'serial_no'=>(isset($_POST['sampleCode']) && $_POST['sampleCode']!='') ? $_POST['sampleCode'] :  NULL ,
        'sample_reordered'=>(isset($_POST['sampleReordered']) && $_POST['sampleReordered']!='') ? $_POST['sampleReordered'] :  'no',
        'sample_code'=>(isset($_POST['sampleCode']) && $_POST['sampleCode']!='') ? $_POST['sampleCode'] :  NULL,
        'sample_code_format'=>(isset($_POST['sampleCodeFormat']) && $_POST['sampleCodeFormat']!='') ? $_POST['sampleCodeFormat'] :  NULL,
        'sample_code_key'=>(isset($_POST['sampleCodeKey']) && $_POST['sampleCodeKey']!='') ? $_POST['sampleCodeKey'] :  NULL,
        'facility_id'=>(isset($_POST['fName']) && $_POST['fName']!='') ? $_POST['fName'] :  NULL,
        'sample_collection_date'=>$_POST['firstDate'],
        'patient_first_name'=>(isset($_POST['patientFirstName']) && $_POST['patientFirstName']!='') ? $_POST['patientFirstName'] :  $pfName,
        'patient_gender'=>(isset($_POST['gender']) && $_POST['gender']!='') ? $_POST['gender'] :  NULL,
        'patient_dob'=>(isset($_POST['dob']) && $_POST['dob']!='') ? $_POST['dob'] :  $_POST['dob1'],
        'patient_age_in_years'=>(isset($_POST['ageInYears']) && $_POST['ageInYears']!='') ? $_POST['ageInYears'] :  $ageInYears1,
        'patient_age_in_months'=>(isset($_POST['ageInMonths']) && $_POST['ageInMonths']!='') ? $_POST['ageInMonths'] : $ageInMonths1,
        'patient_district'=>(isset($_POST['district']) && $_POST['district']!='') ? $_POST['district'] :  NULL,
        'rDistrict'=>(isset($_POST['rDistrict']) && $_POST['rDistrict']!='') ? $_POST['rDistrict'] :  NULL,
        'patient_art_no'=>(isset($_POST['artNo']) && $_POST['artNo']!='') ? $_POST['artNo'] :  $artNo1,
        'treatment_initiated_date'=>(isset($_POST['admissionDate']) && $_POST['admissionDate']!='') ? $_POST['admissionDate'] :  $_POST['admissionDate1'],
        //'treatment_initiation'=>(isset($_POST['treatPeriod']) && $_POST['treatPeriod']!='') ? $_POST['treatPeriod'] :  NULL,
        'sDistrict'=>(isset($_POST['sDistrict']) && $_POST['sDistrict']!='') ? $_POST['sDistrict'] :  NULL,
        'date_of_initiation_of_current_regimen'=>$_POST['onSetDate'],
        'patient_mobile_number'=>(isset($_POST['patientPhoneNumber']) && $_POST['patientPhoneNumber']!='') ? $_POST['patientPhoneNumber'] :  NULL,
        'consent_to_receive_sms'=>(isset($_POST['receiveSms']) && $_POST['receiveSms']!='') ? $_POST['receiveSms'] :  NULL,
        'sample_type1'=>(isset($_POST['testType']) && $_POST['testType']!='') ? $_POST['testType'] :  NULL,
        'labPersonnel'=>(isset($_POST['Clinician']) && $_POST['Clinician']!='') ? $_POST['Clinician'] :  $labPersonnel,
        'ePID'=>(isset($_POST['ePID']) && $_POST['ePID']!='') ? $_POST['ePID'] :  NULL,
        'gps'=>(isset($_POST['gps']))?$_POST['gps']:NULL,
        'sendDate'=>(isset($_POST['sendDate']) && $_POST['sendDate']!='') ? $_POST['sendDate'] :  $_POST['sendDate1'],
        'test'=>(isset($_POST['test']) && $_POST['test']!='') ? $_POST['test'] :  NULL,
        'updateDate'=>(isset($_POST['updateDate']) && $_POST['updateDate']!='') ? $_POST['updateDate']:   $_POST['updateDate1'],
        'disease'=>(isset($_POST['disease']) && $_POST['disease']!='') ? $_POST['disease'] :  NULL,
        'last_vl_date_failure'=>(isset($_POST['suspendTreatmentLastVLDate']) && $_POST['suspendTreatmentLastVLDate']!='') ? $general->dateFormat($_POST['suspendTreatmentLastVLDate']) :  NULL,
        'last_vl_result_failure'=>(isset($_POST['suspendTreatmentVlValue']) && $_POST['suspendTreatmentVlValue']!='') ? $_POST['suspendTreatmentVlValue'] :  NULL,
        'gaurdianName'=>(isset($_POST['gaurdianName']) && $_POST['gaurdianName']!='') ? $_POST['gaurdianName'] :  NULL,
        'noHouseHold'=>(isset($_POST['noHouseHold']) && $_POST['noHouseHold']!='') ? $_POST['noHouseHold'] :  NULL,
        'notifyDate'=>(isset($_POST['notifyDate']) && $_POST['notifyDate']!='') ? $_POST['notifyDate'] :  $_POST['notifyDate1'] ,
        'residentialAdd'=>(isset($_POST['residentialAdd']) && $_POST['residentialAdd']!='') ? $_POST['residentialAdd'] :  NULL,
        'symptoms'=>(isset($_POST['symptoms']) && $_POST['symptoms']!='') ? $_POST['symptoms'] :  NULL,
        'lab_id'=>(isset($_POST['labId']) && $_POST['labId']!='') ? $_POST['labId'] :  NULL,
        'region'=>(isset($_POST['region']) && $_POST['region']!='') ? $_POST['region'] :  NULL,
        //'test_methods'=>(isset($_POST['testMethods']) && $_POST['testMethods']!='') ? $_POST['testMethods'] :  NULL,
        'specimenCondition'=>(isset($_POST['specimenCondition']) && $_POST['specimenCondition']!='') ? $_POST['specimenCondition'] :  NULL,
        'labDate'=>$_POST['labDate'],
        'rCountry'=>(isset($_POST['rCountry']) && $_POST['rCountry']!='') ? $_POST['rCountry'] :  NULL,
        'outCome'=>(isset($_POST['outCome']) && $_POST['outCome']!='') ? $_POST['outCome'] :  NULL,
        'fClassification'=>(isset($_POST['fClassification']) && $_POST['fClassification']!='') ? $_POST['fClassification'] :  NULL,
        'organismTypes'=>(isset($_POST['organismTypes']) && $_POST['organismTypes']!='') ? $_POST['organismTypes'] :  NULL,
        'personSending'=>(isset($_POST['personSending']) && $_POST['personSending']!='' ) ? $_POST['personSending'] :  NULL,
        'result'=>(isset($_POST['labResult']) && $_POST['labResult']!='') ? $_POST['labResult'] :  NULL,
        'organismSensitivity'=>(isset($_POST['organismSensitivity']) && $_POST['organismSensitivity']!='') ? $_POST['organismSensitivity'] :  NULL,
        'organismResistance'=>(isset($_POST['organismResistance']) && trim($_POST['organismResistance'])!='') ? trim($_POST['organismResistance']) :  NULL,
        'result_status'=>$status,
        'request_created_by'=>$_SESSION['userId'],
        'request_created_datetime'=>$general->getDateTime(),
        'last_modified_by'=>$_SESSION['userId'],
        'last_modified_datetime'=>$general->getDateTime(),
        'manual_result_entry'=>'yes'
    );
    //echo "<pre>";var_dump($vldata);die;
    $id=$db->insert($tableName,$vldata);
    //var_dump($id);die;
    if($id>0){
        $_SESSION['alertMsg']="IDSR request added successfully";
        //Add event log
        $eventType = 'add-idsr-request-gh';
        $action = ucwords($_SESSION['userName']).' added a new request data with the sample code '.$_POST['sampleCode'];
        $resource = 'idsr-request-gh';
        $data=array(
            'event_type'=>$eventType,
            'action'=>$action,
            'resource'=>$resource,
            'date_time'=>$general->getDateTime()
        );
        $db->insert($tableName1,$data);

        $barcode = "";
        if(isset($_POST['printBarCode']) && $_POST['printBarCode'] =='on'){
            $s = $_POST['sampleCode'];
            $facQuery="SELECT * FROM facility_details where facility_id=".$_POST['fName'];
            $facResult = $db->rawQuery($facQuery);
            //var_dump($facResult);die;
            $f = ucwords($facResult[0]['facility_name'])." | ".$_POST['sampleCollectionDate'];
            $barcode = "?barcode=true&s=$s&f=$f";
        }

        if(isset($_POST['saveNext']) && $_POST['saveNext']=='next'){
            header("location:addGenericIDSR.php");
        }else{
            header("location:genericIDSR.php");
        }
    }else{
        $_SESSION['alertMsg']="Please try again later";
        header("location:genericIDSR.php");
    }

} catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}