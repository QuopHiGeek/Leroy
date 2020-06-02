<?php

require_once ('lib.php');
class DHIS2API
{
    
     private static $USERNAME;
     private static $PASSWORD;
     private static $URL;
     
     private static $LOG_EVENT_NAME = 'DHIS2API';
    
    
    function __construct() 
    {
        self::$USERNAME = TRACKER_USERNAME;
        self::$PASSWORD = TRACKER_PASSWORD;
        self::$URL = TRACKER_BASEURL;
        
        (self::Authenticate() === TRUE) or die('Cannot connect to tracker on'.TRACKER_BASEURL);
    }
	
	
    public static function Authenticate()
    {

        writelog(self::$LOG_EVENT_NAME, "Trying DHIS2 authentication",array('username'=>self::$USERNAME,'password'=>self::$PASSWORD));
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,self::$URL."/?authOnly=true");		
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD,self::$USERNAME.":".self::$PASSWORD);	
        $return = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 	
        writelog(self::$LOG_EVENT_NAME, "Authentication response status:{$status_code}",$return);
        if($status_code ===302)
        {
            curl_setopt($ch,CURLOPT_URL,self::$URL."/api/currentUser");
            curl_setopt($ch,CURLOPT_HEADER,"application/json");
            curl_setopt($ch,CURLOPT_POST,0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
            $return=curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close($ch);	

            writelog(self::$LOG_EVENT_NAME, "Authentication response status:{$status_code}",$return);

            if($status_code ===200)
            {
                    return true;

            }
            else
            {
                    return $status_code;
            }
        }		
        else
        {
                return $status_code;
        }
    }
	
        
    public static function getFromDHIS2($path,$params = null)
    {
        
        writelog(self::$LOG_EVENT_NAME, "Sending request to DHIS2:=> path:{$path}, params:{$params}");

        if(!empty($params))
        {
            $params = '?'.$params;
        }
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,self::$URL."/{$path}.json{$params}");	
        curl_setopt($ch,CURLOPT_HEADER,"application/json");	
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD,self::$USERNAME.":".self::$PASSWORD);
        $return=curl_exec($ch);	
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 		
        curl_close($ch);
        writelog(self::$LOG_EVENT_NAME, "Request response status:{$status_code}",$return);

        if($status_code ===200)
        {			
                return $return;

        }
        else
        {
                return false;
        }
    }
	
	
        
        
    public static function sendtoDHIS2($path,$data,$params = null)
    {	

        $contenttype = "application/json";	

        writelog(self::$LOG_EVENT_NAME, "Sending PUT request to DHIS2:=> path:{$path}, params:{$params}",$data);
        
        if(!empty($params))
        {
            $params = '?'.$params;
        }
       
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,self::$URL."/{$path}{$params}");
        curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: $contenttype"));                               
        //curl_setopt($ch, CURLOPT_POST,1); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);			
        curl_setopt($ch, CURLOPT_USERPWD,self::$USERNAME.":".self::$PASSWORD);			
        $return = curl_exec($ch);	
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 			
        curl_close($ch);
        
        writelog(self::$LOG_EVENT_NAME, "Request response status:{$status_code}",$return);

        if($status_code ===200)
        {			
            return true;

        }
        else
        {
            return false;
        }

    }
        	
}
?>