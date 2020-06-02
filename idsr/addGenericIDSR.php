<?php
ob_start();
$title = "eLabMessenger | Add New IDSR Request";
include('../header.php');
    $configQuery="SELECT * from global_config";
    $configResult=$db->query($configQuery);
    $arr = array();
    // now we create an associative array so that we can easily create view variables
    for ($i = 0; $i < sizeof($configResult); $i++) {
      $arr[$configResult[$i]['name']] = $configResult[$i]['value'];
    }
    if($arr['vl_form']==1){
        if(isset($_SESSION['siteID']) && $_SESSION['siteID']!=""){
            include('addDIRequestUser.php');
            //include('defaultaddDIRequest.php');
        }else {
            include('defaultaddDIRequest.php');
        }
    }
include('../footer.php');
 ?>
