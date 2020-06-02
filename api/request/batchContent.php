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
$stmt= $request->getBatch($request->sampleID);
$num = $stmt->rowCount();
//var_dump($num);die;
// check if more than 0 record found
if($num>0){

    // products array
    $products_arr=array();
    $products_arr["records"]=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);

        $product_item=array(
            'vl_sample_id' => $vl_sample_id,
            'vlsm_instance_id'=>$vlsm_instance_id,
            'vlsm_country_id'=>$vlsm_country_id,
            'sample_code_title'=>$sample_code_title ,
            'serial_no'=>$serial_no ,
            'sample_reordered'=>$sample_reordered,
            'sample_code'=>$sample_code,
            'sample_code_format'=>$sample_code_format,
            'sample_code_key'=>$sample_code_key,
            'facility_id'=>$facility_id,
            'sample_collection_date'=>$sample_collection_date,
            'patient_first_name'=>$patient_first_name,
            'patient_middle_name'=>$patient_middle_name,
            'patient_last_name'=>$patient_last_name,
            'patient_gender'=>$patient_gender,
            'patient_dob'=>$patient_dob,
            'patient_age_in_years'=>$patient_age_in_years,
            'patient_age_in_months'=>$patient_age_in_months,
            'is_patient_pregnant'=>$is_patient_pregnant,
            'is_patient_breastfeeding'=>$is_patient_breastfeeding,
            'patient_art_no'=>$patient_art_no,
            'treatment_initiated_date'=>$treatment_initiated_date,
            'current_regimen'=>$current_regimen,
            'date_of_initiation_of_current_regimen'=>$date_of_initiation_of_current_regimen,
            'patient_mobile_number'=>$patient_mobile_number,
            'consent_to_receive_sms'=>$consent_to_receive_sms,
            'sample_type'=>$sample_type,
            'picked_by'=>$picked_by,
            'arv_adherance_percentage'=>$arv_adherance_percentage,
            'reason_for_vl_testing'=>$reason_for_vl_testing,
            'last_vl_date_routine'=>$last_vl_date_routine,
            'last_vl_result_routine'=>$last_vl_result_routine,
            'last_vl_date_failure_ac'=>$last_vl_date_failure_ac,
            'last_vl_result_failure_ac'=>$last_vl_result_failure_ac,
            'last_vl_date_failure'=>$last_vl_date_failure,
            'last_vl_result_failure'=>$last_vl_result_failure,
            'request_clinician_name'=>$request_clinician_name,
            'request_clinician_phone_number'=>$request_clinician_phone_number,
            'test_requested_on'=>$test_requested_on,
            'vl_focal_person'=>$vl_focal_person,
            'vl_focal_person_phone_number'=>$vl_focal_person_phone_number,
            'lab_id'=>$lab_id,
            'vl_test_platform'=>$vl_test_platform,
            'sample_received_at_vl_lab_datetime'=>$sample_received_at_vl_lab_datetime,
            'sample_tested_datetime'=>$sample_tested_datetime,
            'result_dispatched_datetime'=>$result_dispatched_datetime,
            'is_sample_rejected'=>$is_sample_rejected,
            'reason_for_sample_rejection'=>$reason_for_sample_rejection,
            'result_value_absolute'=>$result_value_absolute,
            'result_value_absolute_decimal'=>$result_value_absolute_decimal,
            'result'=>$result,
            'result_approved_by'=>$result_approved_by,
            'approver_comments'=>$approver_comments,
            'result_status'=>$result_status,
            'request_created_by'=>$request_created_by,
            'request_created_datetime'=>$request_created_datetime,
            'last_modified_by'=>$last_modified_by,
            'last_modified_datetime'=>$last_modified_datetime,
            'manual_result_entry'=>$manual_result_entry
        );

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode($products_arr);
}

else{
    echo json_encode(
        array("message" => "No products found.")
    );
}
?>