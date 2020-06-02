<?php
require_once('../config.php');

if(function_exists('mysqli_connect'))
{	
		
	$conn = mysqli_connect($DB_SERVER,$DB_USER,$DB_PASSWORD,$DB_NAME,$DB_PORT);
	if(!$conn)
	{
            die("Database connection Error: (".mysqli_connect_errno().")". mysqli_connect_error());
	}
       
}
else
{
    die("mysqli extension has not been installed!");
}

function get($tbl,$filter)
{
    return getData("select * from `{$tbl}` where {$filter}");
}

function query_blind($sql)
{
    global $conn;		
    writelog("MYSQL",$sql);
    $rs = mysqli_query($conn,$sql) or writelog("MYSQL","Could not execute query", mysqli_error($conn));
    if($rs)
    {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

function getData($sql)
{
    global $conn;	

    writelog("MYSQL",$sql);
    $rs = mysqli_query($conn,$sql) or writelog("MYSQL","Could not execute query", mysqli_error($conn));
    $retval = array();
    while( $row = @mysqli_fetch_array($rs, MYSQLI_ASSOC) )		
    {
            $retval[] = $row;
    }
    
    if(DEBUG_LEVEL < 2)
    {
        writelog("MYSQL","Response",$retval);
    }
    return $retval;
}

function getDBValue($sql)
{	
    global $conn;	
    writelog("MYSQL",$sql);
    $rs = mysqli_query($conn,$sql);
    if($rs && mysqli_num_rows($rs)> 0)
    {		
        $row = mysqli_fetch_array($rs, MYSQLI_NUM);	
        writelog("MYSQL","Returned {$row[0]}");
        return $row[0];
    }
    else
    {
        if(DEBUG_LEVEL < 2)
        {
            writelog("MYSQL","Returned empty");
        }
        return false;
    }		

}

function writelog($action,$msg,$details = null)
{
    
   if(DEBUG)
   { 
       $base_dir = BASE_DIR.'/logs/';
       
       $logfile = $base_dir.date("Ymd").'.log';
       file_put_contents($logfile,"\n".date('Y-m-d H:i:s')." - {$action}: \t".$msg,FILE_APPEND);
       
       if(!empty($details) && DEBUG_LEVEL < 2)
       {
           if(is_resource($details))
           {
               $details = get_resource_type($details).": Resource not serializable";
           }
           else
           {
                $details = serialize($details);
           }
           
           file_put_contents($logfile,"\n".date('Y-m-d H:i:s')." - {$action}: \t".$details,FILE_APPEND);
       }
   }
}



if( !function_exists('apache_request_headers') ) 
{
    function apache_request_headers() {
      $arh = array();
      $rx_http = '/\AHTTP_/';
      foreach($_SERVER as $key => $val) {
        if( preg_match($rx_http, $key) ) {
          $arh_key = preg_replace($rx_http, '', $key);
          $rx_matches = array();
          // do some nasty string manipulations to restore the original letter case
          // this should work in most cases
          $rx_matches = explode('_', $arh_key);
          if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
            foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
            $arh_key = implode('-', $rx_matches);
          }
          $arh[$arh_key] = $val;
        }
      }
      return( $arh );
    }

}
?>