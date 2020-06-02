<?php
ob_start();
session_start();
include('../includes/MysqliDb.php');
//include('../header.php');


$tableName="physicians";
$userId=base64_decode($_POST['physicianId']);

try {
    if(trim($_POST['physicianName'])!='' && trim($_POST['email'])!='' && ($_POST['site'])!=''){
    $data=array(
    'physician_name'=>$_POST['physicianName'],
    'physician_email'=>$_POST['email'],
    'physician_phone'=>$_POST['phoneNo'],
//    'login_id'=>$_POST['loginId'],
//    'role_id'=>$_POST['role'],
    'status'=>$_POST['status'],
    'physician_site'=>$_POST['site']
    );
    
//    if(isset($_POST['password']) && trim($_POST['password'])!=""){
//        $passwordSalt = '0This1Is2A3Real4Complex5And6Safe7Salt8With9Some10Dynamic11Stuff12Attched13later';
//        $data['password'] = sha1($_POST['password'].$passwordSalt);
//    }
    
    $db=$db->where('physician_id',$userId);
    //print_r($data);die;
    $db->update($tableName,$data);    
    
    $_SESSION['alertMsg']="Clinician details updated successfully";
    }
    header("location:physicians.php");
  
} catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}