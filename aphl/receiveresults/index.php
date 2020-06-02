<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../libs/lib.php');
$clientIP = $_SERVER['REMOTE_ADDR'] ? : ($_SERVER['HTTP_X_FORWARDED_FOR'] ? : $_SERVER['HTTP_CLIENT_IP']);

$lib = new LIB();
$LOG_EVENT_NAME = "RECEIVER";

writelog($LOG_EVENT_NAME, "{$clientIP}: Receieved new request", serialize($_REQUEST));

if(!$lib->getHeader('Authorization') || $lib->getHeader('Authorization') != $API_KEY)
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: Invalid Authorization Key[".$lib->getHeader('Authorization')."]");
    die('401::Invalid Authorization Key');
}

$result = filter_var(@$_REQUEST['result'],FILTER_SANITIZE_SPECIAL_CHARS);
$specimen_id = filter_var(@$_REQUEST['specimen_id']);
$test_date = filter_var(@$_REQUEST['test_date']);

if(empty($result))
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: Invalid Result:{$result}");
    die("400::Invalid Result:{$result}");
}

if(empty($specimen_id))
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: Invalid Specimen ID:{$specimen_id}");
    die("400::Invalid Specimen ID:{$specimen_id}");
}

if(empty($test_date))
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: Invalid Test date:{$test_date}");
    die("400::Invalid Test date:{$test_date}");
}


$data = getDBValue("select sample_code from vl_request_form where sample_code='{$specimen_id}'");
if($data ===  false)
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: Specimen ID cannot be found in VLDMS:{$specimen_id}");
    die("400::Specimen ID cannot be found in VLDMS:{$specimen_id}");
}
            
$sql = "update vl_request_form set result='{$result}', sample_testing_date='{$test_date}',"
. "result_imported_datetime=utc_timestamp, result_status = 7 where sample_code='{$specimen_id}'";
$resp = query_blind($sql);
if($resp === false)
{
    writelog($LOG_EVENT_NAME, "{$clientIP}: System Error.Could not update result");
    die("500::System Error.Could not update result");
}

die("200::Result accepted by VLDMS sucessfully");












    
   


