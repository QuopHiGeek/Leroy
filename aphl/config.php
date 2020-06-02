<?php

/* 
 * This is the main configuration file for seting up this application
 * 
 * 
 */

define('BASE_DIR',__DIR__);


//Database configurations
$DB_SERVER = 'localhost';
$DB_NAME = 'elab_db';
$DB_USER='root';
$DB_PASSWORD ='Elab@Db1234';
$DB_PORT = 3306;


//App Misc configurations

/*
 * Whether debuging application or not. Applicable values (TRUE/FALSE)
 */
define('DEBUG',TRUE);

/*
 * Debug verbosity. This controls how much data is logged. Only used when application is in debug mode.
 * Applicable values (1:Very Verbose, 2:Verbose, 3:Not Verbose)
 */
define('DEBUG_LEVEL',1);


/*
 * Tracker related mapping details.
 * Modify as required
 */

define('TRACKER_BASEURL','http://localhost:8080/dhis');
define('TRACKER_USERNAME','stephen');
define('TRACKER_PASSWORD','Ghana@2020');


/*
 * Tracker and VLDMS mappings
 */

define('TRACKER_REQUEST_MAPPING',
        array(
        array('vldms'=>'sample_code,serial_no','tracker'=>'y5spKJcqTm6'),
        array('vldms'=>'facility_id','tracker'=>'ou','ext_map'=>TRUE),
        array('vldms'=>'sample_collection_date','tracker'=>'KbqNq7jrLHW'),
        array('vldms'=>'picked_by','tracker'=>'mdMmfLCDvY4','ext_map'=>TRUE),
        array('vldms'=>'sample_type','tracker'=>'a0aqJY7DGH1','ext_map'=>TRUE),
        array('vldms'=>'patient_dob','tracker'=>'OK2xefZv34j'),
        array('vldms'=>'patient_first_name','tracker'=>'MIAeed2B1Ca'),
        array('vldms'=>'patient_middle_name','tracker'=>'X1eT4Baj6V2'),
        array('vldms'=>'patient_last_name','tracker'=>'cv6y8OFLWeV'),
        array('vldms'=>'patient_art_no','tracker'=>'njnjhVQwXSe'),
        array('vldms'=>'patient_gender','tracker'=>'p02T84H7PYv','mutate'=>'lowercase'),
        array('vldms'=>'patient_mobile_number','tracker'=>'XpaS6NLmNyp'),
        array('vldms'=>'patient_other_id','tracker'=>'p5Z9ZWcRVKX')));



/*
 * Tracker program and stages to pull events from
 */
define('TRACKER_PROGRAMS',array(
    array('PROGRAM'=>'XqQ9M9qQPk8','STAGE'=>'sdVtSwZL5LY'),
    array('PROGRAM'=>'QRGzttaLeym','STAGE'=>'Qkb37MfFf7X')));



/*
 * Tracker org unit
 */
define('TRACKER_ORG_UNIT','E4h5WBOg71F');

/*
 * Tracker. How far back should we attempt to pull data
 */
define('TRACKER_PE','THIS_SIX_MONTH');

/*
 * Result field from tracker
 */
define('TRACKER_RESULT_FIELD','maczP2xjYtM');


/*
 * Specimen ID field from tracker
 */
define('TRACKER_SPECIMEN_ID_FIELD','y5spKJcqTm6');


/*
 * Default user id to be used in saving data in vldms tables
 */

define('VLDMS_USER_ID',3);

/*
 * API Key to be used to validate result updates from VLDMS Interface Client
 */
$API_KEY = 'b911cdf1-4198-4cab-9320-869c047c4937';


