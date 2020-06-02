<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/facility.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$request = new Facility($db);

// read the details of product to be edited
$stmt= $request->get();
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
            'facility_id' => $facility_id,
            'facility_name'=>$facility_name,
            'facility_code'=>$facility_code,
            'other_id'=>$other_id ,
            'facility_emails'=>$facility_emails ,
            'report_email'=>$report_email,
            'contact_person'=>$contact_person,
            'facility_mobile_numbers'=>$facility_mobile_numbers,
            'address'=>$address,
            'facility_region'=>$facility_state,
            'facility_district'=>$facility_district,
            'facility_hub_name'=>$facility_hub_name,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'facility_type'=>$facility_type,
            'status'=>$status,

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