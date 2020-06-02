<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/requests.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$request = new Request($db);

// set ID property of product to be edited
$request->sampleID = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of product to be edited
$request->getOne($request->sampleID);

// create array
$product_arr = array(
    'vl_sample_id' => $request->sampleID,
    'vlsm_instance_id'=>$request->instanceId,
    'vlsm_country_id'=>$request->countryID,
    'sample_code_title'=>$request->sampleCodeTitle ,
    'serial_no'=>$request->serialNo ,
    'sample_reordered'=>$request->sampleReOrdered,
    'sample_code'=>$request->sampleCode,
    'sample_code_format'=>$request->sampleCodeFormat,
    'sample_code_key'=>$request->sampleCodeKey,
    'facility_id'=>$request->facilityID,
    'sample_collection_date'=>$request->sampleCollectionDate,
    'patient_first_name'=>$request->patientFirstName,
    'patient_middle_name'=>$request->patientMiddleName,
    'patient_last_name'=>$request->patientLastName,
    'patient_gender'=>$request->patientGender,
    'patient_dob'=>$request->patientDOB,
    'patient_age_in_years'=>$request->patientAgeInYears,
    'patient_age_in_months'=>$request->patientAgeInMonths,
    'is_patient_pregnant'=>$request->isPatientPregnant,
    'is_patient_breastfeeding'=>$request->isPatientBreastfeeding,
    'patient_art_no'=>$request->patientARTNo,
    'treatment_initiated_date'=>$request->treatmentInitiationDate,
    'current_regimen'=>$request->currentRegimen,
    'date_of_initiation_of_current_regimen'=>$request->currentRegimenInitiatonDate,
    'patient_mobile_number'=>$request->patientMobileNo,
    'consent_to_receive_sms'=>$request->SMSConsent,
    'sample_type'=>$request->sampleType,
    'picked_by'=>$request->pickedBy,
    'arv_adherance_percentage'=>$request->arvAdherence,
    'reason_for_vl_testing'=>$request->vlTestReason,
    'last_vl_date_routine'=>$request->lastVlTestDate,
    'last_vl_result_routine'=>$request->lastVlTestResult,
    'last_vl_date_failure_ac'=>$request->lastVlFailureACDate,
    'last_vl_result_failure_ac'=>$request->lastVlFailureACResult,
    'last_vl_date_failure'=>$request->lastVlFailureDate,
    'last_vl_result_failure'=>$request->lastVlFailureResult,
    'request_clinician_name'=>$request->reqClinician,
    'request_clinician_phone_number'=>$request->reqClinicianPhoneNo,
    'test_requested_on'=>$request->testRequestedOn,
    'vl_focal_person'=>$request->vlFocalPerson,
    'vl_focal_person_phone_number'=>$request->vlFocalPersonPhoneNo,
    'lab_id'=>$request->labID,
    'vl_test_platform'=>$request->vlTestPlatform,
    'sample_received_at_vl_lab_datetime'=>$request->sampleReceivedOn,
    'sample_tested_datetime'=>$request->sampleTestDateAtLab,
    'result_dispatched_datetime'=>$request->resultDispatchedOn,
    'is_sample_rejected'=>$request->isSampleRejected,
    'reason_for_sample_rejection'=>$request->sampleRejectionReason,
    'result_value_absolute'=>$request->absoluteResultValue,
    'result_value_absolute_decimal'=>$request->absoluteDecimalResultValue,
    'result'=>$request->resultValue,
    'result_approved_by'=>$request->resultApprovedBy,
    'approver_comments'=>$request->approverComments,
    'result_status'=>$request->resultStatus,
    'request_created_by'=>$request->requestCreatedBy,
    'request_created_datetime'=>$request->requestCreatedDate,
    'last_modified_by'=>$request->lastModifiedBy,
    'last_modified_datetime'=>$request->lastModifiedDate,
    'manual_result_entry'=>$request->manualResultEntry

);

// make it json format
print_r(json_encode($product_arr));
?>