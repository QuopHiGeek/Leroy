<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('connection.php');
require_once ('dhis2.php');
class LIB
{
    private static $REQUEST_LOG_EVENT_NAME ='REQUEST';
    private static $RESULT_LOG_EVENT_NAME ='RESULT';
    
    
    public function getHeader($key)
    {
        $headers = apache_request_headers();
        foreach($headers as $header=>$value)
        {
           if(strtolower($key) == strtolower($header))
           {
               return $value;
           }
        }
         
    }
    
    
    
    
    public static function pushResults()
    {
        writelog(self::$RESULT_LOG_EVENT_NAME, "Started Activity");
        writelog(self::$RESULT_LOG_EVENT_NAME, "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        $path = 'api/events/';
        
        $sql="SELECT evt.*,vl.result_imported_datetime,vl.serial_no,vl.result,"
                . "vl.sample_testing_date FROM `aphl_tracker_requests` evt , vl_request_form vl "
                . "where (evt.vl_sample_id = vl.vl_sample_id) and (evt.result_sent_date is null) and (vl.result is not null) order by evt.fetchdate asc";
        
        $pendinglst = getData($sql);
        
        writelog(self::$RESULT_LOG_EVENT_NAME, "Total items to push:==>".count($pendinglst));
        
        foreach($pendinglst as $rs)
        {
            
            $event = DHIS2API::getFromDHIS2("api/events/".$rs['eventuid']);
            if($event === false)
            {
                writelog(self::$RESULT_LOG_EVENT_NAME, "Event[{$rs['eventuid']}] not found on tracker. Ignored");
                continue;
            }
            
            $payload = json_decode($event, true);
            
            if(self::eventHasResult($payload['dataValues']))
            {
                writelog(self::$RESULT_LOG_EVENT_NAME, "Event[{$rs['eventuid']}] already has as result on tracker. Ignored");
                continue;
            }
            
            $payload['completedDate'] = 'COMPLETED';
            $payload['dataValues'][] = array('dataElement'=>TRACKER_RESULT_FIELD,'value'=>$rs['result']);
            $payload['dataValues'][] = array('dataElement'=>'hRbz2lQH302','value'=>date('Y-m-d'));
            
            if(!empty($rs['sample_testing_date']))
            {
                $fdate = date_create($rs['sample_testing_date']);
                $payload['dataValues'][] = array('dataElement'=>'rhfvJs5p00h','value'=> date_format($fdate, 'Y-m-d\TH:i'));
            }
            
            $respnse = DHIS2API::sendtoDHIS2($path.$rs['eventuid'], json_encode($payload),'mergeMode=MERGE&strategy=UPDATE');
              
              if($respnse !== false)
              {
                  query_blind("update aphl_tracker_requests set result_sent_date = utc_timestamp where eventuid = '{$rs['eventuid']}'");
              }
        }
        
        writelog(self::$RESULT_LOG_EVENT_NAME, "Finished Activity\n");
    }
    
    private static  function eventHasResult($datavalues)
    {
        foreach($datavalues as $data)
        {
            if($data['dataElement'] == TRACKER_RESULT_FIELD && !empty($data['value']))
            {
                return true;
            }
        }
        
        return false;
    }


    
    public static function pullRequests()
    {
        writelog(self::$REQUEST_LOG_EVENT_NAME, "Started Activity");
        writelog(self::$REQUEST_LOG_EVENT_NAME, "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
         
        $path = 'api/analytics/events/query/';
        $dimension = array();
        array_push($dimension, 'paging=false');
        array_push($dimension, 'dimension=pe:'.TRACKER_PE);
        array_push($dimension, 'dimension=ou:'.TRACKER_ORG_UNIT);
        array_push($dimension, 'dimension='.TRACKER_RESULT_FIELD);
        
        
        foreach(TRACKER_REQUEST_MAPPING as $map)
        {
            if(strlen($map['tracker']) == 11)
            {
                array_push($dimension, 'dimension='.$map['tracker']);
            }
        }
        
        foreach(TRACKER_PROGRAMS as $program)
        {
            writelog(self::$REQUEST_LOG_EVENT_NAME, "Working: => Program:". serialize($program));
            $response = DHIS2API::getFromDHIS2($path.$program['PROGRAM'], implode('&',$dimension)."&stage={$program['STAGE']}");
            if($response !== FALSE)
            {
                self::handlePullRequest(json_decode($response));
            }
            
        }
        writelog(self::$REQUEST_LOG_EVENT_NAME, "Finished Activity\n");
        
    }
    
    
    
    private static function handlePullRequest($resp)
    {
       
        if($resp->height == 0)
        {
            writelog(self::$REQUEST_LOG_EVENT_NAME, "No new requests for program stage");
            return;
        }
        
        $field_positions = self::getMappedFieldPositions($resp->headers, $resp->headerWidth);
        
        $result_pos = self::getPositionFromHeaders($resp->headers, $resp->headerWidth, TRACKER_RESULT_FIELD);
        if(empty($result_pos))
        {
           writelog(self::$REQUEST_LOG_EVENT_NAME, "Error occured. Result field could not be found");
           return;
        }
        
        $new_requests = array();
        
        foreach($resp->rows as $req)
        {
            $new_request = array();
            
            if(!empty($req[$result_pos])) //Only interested in new requests
            {
                writelog(self::$REQUEST_LOG_EVENT_NAME, "Event:[{$req[0]}] already has result. Ignored");
                continue;
            }
            
                
            foreach($field_positions as $field)
            {
                
                if($field['map']['tracker'] == TRACKER_SPECIMEN_ID_FIELD && empty($req[$field['pos']])) //Only interested in requests with specimen id
                {
                    writelog(self::$REQUEST_LOG_EVENT_NAME, "Event:[{$req[0]}] with no specimen id. Ignored");
                    $new_request = array(); //refresh the lst
                    break;
                }
                
                $mfields = explode(',',$field['map']['vldms']);
                foreach($mfields as $mfield)
                {
                    array_push($new_request,array('type'=>'req','field'=>$mfield,'value'=>self::getProperValue($field['map'],$req[$field['pos']])));
                }
            }
            
            if(count($new_request) > 0)
            {
                //get Tracked entity instance id
                
                $check = getDBValue("select eventuid from aphl_tracker_requests where eventuid='{$req[0]}'");
                if($check !== false)
                {
                    writelog(self::$REQUEST_LOG_EVENT_NAME, "Event:[{$req[0]}] already synched. Will ignore:");
                    continue;
                }
                
                
                $event = DHIS2API::getFromDHIS2("api/events/".$req[0]);
                if($event !== false)
                {
                    $event = json_decode($event);
                    array_push($new_request,array('type'=>'evt','field'=>'eventuid','value'=>$event->event));
                    array_push($new_request,array('type'=>'evt','field'=>'programid','value'=>$event->program));
                    array_push($new_request,array('type'=>'evt','field'=>'programstageid','value'=>$event->programStage));
                    array_push($new_request,array('type'=>'evt','field'=>'trackedentityid','value'=>$event->trackedEntityInstance));
                    array_push($new_request,array('type'=>'evt','field'=>'orguitid','value'=>$event->orgUnit));
                    array_push($new_request,array('type'=>'evt','field'=>'fetchdate','value'=>date('Y-m-d H:i:s')));
                    
                    array_push($new_requests,$new_request);
                }
                 
            }
        }
        
        // now insert new request into db
        self::saveRequestData($new_requests);
        
    }
    
    
    
    private static function saveRequestData($new_requests)
    {
        foreach($new_requests as $req)
        {
            $reqtop = array();
            $reqbut = array();
            $evttop = array();
            $evtbut = array();
            
            foreach($req as $field)
            {
                if($field['type'] == 'req')
                {
                    array_push($reqtop,$field['field']);
                    array_push($reqbut,"'{$field['value']}'");
                }
                else if($field['type'] == 'evt')
                {
                    array_push($evttop,$field['field']);
                    array_push($evtbut,"'{$field['value']}'");
                }
            }
            
            
            array_push($reqtop,'request_created_datetime');
            array_push($reqbut,"'".date('Y-m-d H:i:s')."'");
            
            array_push($reqtop,'request_imported_datetime');
            array_push($reqbut,"'".date('Y-m-d H:i:s')."'");
            
            array_push($reqtop,'vlsm_instance_id');
            array_push($reqbut,"'grqtd3gleo3p9nbs2omas9irouc0vvcg'");
            
            array_push($reqtop,'source');
            array_push($reqbut,"'etracker'");
            
            array_push($reqtop,'request_created_by');
            array_push($reqbut,VLDMS_USER_ID);
            
            array_push($reqtop,'result_status');
            array_push($reqbut,9);
            
            
            
            
            $sql = "insert into vl_request_form ".'('.implode(',',$reqtop).') values ('. implode(',', $reqbut).')';
            $rid = query_blind($sql);
            
            if($rid)
            {
                array_push($evttop,'vl_sample_id');
                array_push($evtbut,$rid);
                    
                $sql = "insert into aphl_tracker_requests ".'('.implode(',',$evttop).') values ('. implode(',', $evtbut).')';
                $rid = query_blind($sql);
            }
            
        }
    }
    
    private static function getProperValue($map,$value)
    {
        if(array_key_exists('ext_map', $map) && $map['ext_map'])
        {
            $data = getDBValue("select vldmsid from aphl_mapping where maptype='{$map['vldms']}' and tracker='{$value}'");
            return ($data === false) ? '' : self::mutateValue($map,$data);
        }
        
        return self::mutateValue($map,$value);
    }
    
    private static function mutateValue($map, $value)
    {
         if(array_key_exists('mutate', $map) && !empty($map['mutate']))
         {
            $lst = explode(',',$map['mutate']);
            foreach($lst as $action)
            {
                switch($action)
                {
                    case 'lowercase':
                        $value = strtolower($value);
                        break;
                    case 'uppercase':
                        $value = strtoupper($value);
                        break;
                }
            }
         }
         
        return $value;
    }

    



    private static function getPositionFromHeaders($headers,$headersize, $item)
    {
        for($i=0;$i<$headersize;$i++)
        {
            if($headers[$i]->name == $item)
            {
                return $i;
            }
        }
        
        return  null;
    }
    
    private static function getMappedFieldPositions($headers,$headersize)
    {
        $positions = array();
        foreach(TRACKER_REQUEST_MAPPING as $map)
        {
            $pos = self::getPositionFromHeaders($headers, $headersize, $map['tracker']);
            if($pos && is_numeric($pos))
            {
                array_push($positions,array('map'=>$map,'pos'=>$pos));
            }
        }
        
        return $positions;
    }
    
}