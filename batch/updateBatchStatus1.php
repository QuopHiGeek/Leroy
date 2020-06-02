<?php
ob_start();
include('../includes/MysqliDb.php');
include('../General.php');
$general=new Deforay_Commons_General();
$tableName="batch_details";
try {
    $id = intval($_POST['id']);
    $value = intval($_POST['value']);
        $status=array(
            'post_notifies'=>$value
        );
        $db=$db->where('batch_id',$id);
        $db->update($tableName,$status);
        $result = $id;
}
catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}
echo $result;