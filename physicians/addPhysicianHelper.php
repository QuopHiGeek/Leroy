<?php
ob_start();
session_start();
include('../includes/MysqliDb.php');
//include('../header.php');
$tableName="physicians";
try {
    if(trim($_POST['physicianName'])!='' && trim($_POST['email'])!='' && ($_POST['phoneNo'])!='' && ($_POST['site'])!=''){
        
//    $passwordSalt = '0This1Is2A3Real4Complex5And6Safe7Salt8With9Some10Dynamic11Stuff12Attched13later';
//    $password = sha1($_POST['password'].$passwordSalt);
    
    $data=array(
    'physician_name'=>$_POST['physicianName'],
    'physician_email'=>$_POST['email'],
//    'login_id'=>$_POST['loginId'],
    'physician_phone'=>$_POST['phoneNo'],
//    'password'=>$password,
//    'role_id'=>$_POST['role'],
    'status'=>'active',
    'physician_site'=>$_POST['site']
    );
    $db->insert($tableName,$data);    
    
    $_SESSION['alertMsg']="Clinician details added successfully";
    }
    header("location:physicians.php");
  
} catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}