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
$stmt= $request->fetch_treshhold();
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
            'facility_region'=>$facility_state,
            'facility_district'=>$facility_district,
            'current_treshhold'=>$current_tresh,
            'total_treshhold'=>$total_tresh,

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