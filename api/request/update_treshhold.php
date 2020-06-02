<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// include database and object files
include_once '../config/database.php';
include_once '../objects/facility.php';

$facility_id = null;
$qty = null;

if(isset($_GET['facility_id']) && isset($_GET['qty'])){
    $facility_id = $_GET['facility_id'];
    $qty = $_GET['qty'];
}
// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$request = new Facility($db);

// read the details of product to be edited
$status = $request->update_treshhold($facility_id,$qty);


    echo json_encode(
        array("message" => $status)
    );

?>