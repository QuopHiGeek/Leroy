<?php
session_start();
ob_start();
include('../includes/MysqliDb.php');
include('../includes/General.php');
include ('../includes/tcpdf/tcpdf.php');
include ('../includes/fpdi/fpdi.php');
include ('../includes/fpdf/fpdf.php');
define('UPLOAD_PATH','../uploads');
$tableName1="activity_log";
$tableName2="vl_request_form";
$general=new Deforay_Commons_General();

$configQuery="SELECT * from global_config";
$configResult=$db->query($configQuery);
$arr = array();
// now we create an associative array so that we can easily create view variables
for ($i = 0; $i < sizeof($configResult); $i++) {
  $arr[$configResult[$i]['name']] = $configResult[$i]['value'];
}
if(isset($arr['default_time_zone']) && $arr['default_time_zone']!=''){
  date_default_timezone_set($arr['default_time_zone']);
}else{
  date_default_timezone_set("Europe/London");
}
//set mField Array
$mFieldArray = array();
if(isset($arr['r_mandatory_fields']) && trim($arr['r_mandatory_fields'])!= ''){
  $mFieldArray = explode(',',$arr['r_mandatory_fields']);
}
//set print time
$printedTime = date('Y-m-d H:i:s');
$expStr=explode(" ",$printedTime);
$printDate =$general->humanDateFormat($expStr[0]);
$printDateTime = $expStr[1];
//set query
if(isset($_POST['id']) && trim($_POST['id'])!=''){
  if(isset($_POST['resultMail'])){
    $searchQuery="SELECT vl.*,f.*,rst.*,l.facility_name as labName FROM vl_request_form as vl LEFT JOIN facility_details as f ON vl.facility_id=f.facility_id LEFT JOIN r_sample_type as rst ON rst.sample_id=vl.sample_type LEFT JOIN facility_details as l ON l.facility_id=vl.lab_id where vl.vl_sample_id IN(".$_POST['id'].")";
  }else{
    $searchQuery = $_SESSION['vlResultQuery']." and vl.vl_sample_id IN(".$_POST['id'].")";
  }
}else{
  $searchQuery = $_SESSION['vlRequestSearchResultQuery'];
}
//error_log($searchQuery);
$requestResult=$db->query($searchQuery);
$_SESSION['nbPages'] = sizeof($requestResult);
$_SESSION['aliasPage'] = 1;
//print_r($requestResult);die;
//header and footer
class MYPDF extends TCPDF {

    //Page header
    public function setHeading($logo,$text,$lab) {
      $this->logo = $logo;
      $this->text = $text;
      $this->lab = $lab;
    }
    //Page header
    public function Header() {
        // Logo
        //$image_file = K_PATH_IMAGES.'logo_example.jpg';
        //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        if(trim($this->logo)!=''){
            if (file_exists('../uploads'. DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo)) {
              $image_file = '../uploads'. DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo;
              $this->Image($image_file,20, 13, 15, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
        }
        $this->SetFont('helvetica', 'B', 7);
        $this->writeHTMLCell(30,0,16,28,$this->text, 0, 0, 0, true, 'A', true);
        $this->SetFont('helvetica', '', 18);
        $this->writeHTMLCell(0,0,10,18,'PATIENT EID TEST RESULT', 0, 0, 0, true, 'C', true);
        if(trim($this->lab)!= ''){
          $this->SetFont('helvetica', '', 9);
          $this->writeHTMLCell(0,0,10,26,strtoupper($this->lab), 0, 0, 0, true, 'C', true);
        }
        $this->writeHTMLCell(0,0,15,36,'<hr>', 0, 0, 0, true, 'C', true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
        $this->Cell(0, 10, 'Page'.$_SESSION['aliasPage'].'/'.$_SESSION['nbPages'], 0, false, 'C', 0, '', 0,false, 'T', 'M');
    }
}

class PDF_Rotate extends FPDI {

  var $angle = 0;

  function Rotate($angle, $x = -1, $y = -1) {
      if ($x == -1)
          $x = $this->x;
      if ($y == -1)
          $y = $this->y;
      if ($this->angle != 0)
          $this->_out('Q');
      $this->angle = $angle;
      if ($angle != 0) {
          $angle*=M_PI / 180;
          $c = cos($angle);
          $s = sin($angle);
          $cx = $x * $this->k;
          $cy = ($this->h - $y) * $this->k;
          $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
      }
  }

  function _endpage() {
      if ($this->angle != 0) {
          $this->angle = 0;
          $this->_out('Q');
      }
      parent::_endpage();
  }
}

class Watermark extends PDF_Rotate {

  var $_tplIdx;

  function Header() {
      global $fullPathToFile;

      //Put the watermark
      $this->SetFont('helvetica', 'B', 50);
      $this->SetTextColor(148,162,204);
      $this->RotatedText(67,119,'DRAFT',45);

      if (is_null($this->_tplIdx)) {
          // THIS IS WHERE YOU GET THE NUMBER OF PAGES
          $this->numPages = $this->setSourceFile($fullPathToFile);
          $this->_tplIdx = $this->importPage(1);
      }
      $this->useTemplate($this->_tplIdx, 0, 0, 200);
  }

  function RotatedText($x, $y, $txt, $angle) {
      //Text rotated around its origin
      $this->Rotate($angle, $x, $y);
      $this->Text($x, $y, $txt);
      $this->Rotate(0);
      //$this->SetAlpha(0.7);
  }
}

class Pdf_concat extends FPDI {
    var $files = array();

    function setFiles($files) {
        $this->files = $files;
    }

    function concat() {
        foreach($this->files AS $file) {
             $pagecount = $this->setSourceFile($file);
             for ($i = 1; $i <= $pagecount; $i++) {
                  $tplidx = $this->ImportPage($i);
                  $s = $this->getTemplatesize($tplidx);
                  $this->AddPage('P', array($s['w'], $s['h']));
                  $this->useTemplate($tplidx);
             }
        }
    }
}
$resultFilename = '';
if(sizeof($requestResult)> 0){
    $_SESSION['rVal'] = $general->generateRandomString(6);
    if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_SESSION['rVal']) && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_SESSION['rVal'])) {
      mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_SESSION['rVal']);
    }
    $pathFront = realpath('../uploads/'.$_SESSION['rVal'].'/');
    $pages = array();
    $page = 1;
    foreach($requestResult as $result){
        $_SESSION['aliasPage'] = $page;
        if(!isset($result['labName'])){
          $result['labName'] = '';
        }
        $draftTextShow = false;
        //Set watermark text
        for($m=0;$m<count($mFieldArray);$m++){
          if(!isset($result[$mFieldArray[$m]]) || trim($result[$mFieldArray[$m]]) == '' || $result[$mFieldArray[$m]] == null || $result[$mFieldArray[$m]] == '0000-00-00 00:00:00'){
            $draftTextShow = true;
            break;
          }
        }
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT,true, 'UTF-8', false);
        $pdf->setHeading($arr['logo'],$arr['header'],$result['labName']);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        //$pdf->SetAuthor('Pal');
        $pdf->SetTitle('EID Test Results');
        //$pdf->SetSubject('TCPDF Tutorial');
        //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '',PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '',PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT,PDF_MARGIN_TOP+14,PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', '', 18);

        $pdf->AddPage();
        if(!isset($result['facility_code']) || trim($result['facility_code']) == ''){
           $result['facility_code'] = '';
        }
        if(!isset($result['facility_state']) || trim($result['facility_state']) == ''){
           $result['facility_state'] = '';
        }
        if(!isset($result['facility_district']) || trim($result['facility_district']) == ''){
           $result['facility_district'] = '';
        }
        if(!isset($result['facility_name']) || trim($result['facility_name']) == ''){
           $result['facility_name'] = '';
        }
        if(!isset($result['labName']) || trim($result['labName']) == ''){
           $result['labName'] = '';
        }
        //Set Age
        $age = 'Unknown';
        if(isset($result['patient_dob']) && trim($result['patient_dob'])!='' && $result['patient_dob']!='0000-00-00'){
          $todayDate = strtotime(date('Y-m-d'));
          $dob = strtotime($result['patient_dob']);
          $difference = $todayDate - $dob;
          $seconds_per_year = 60*60*24*365;
          $age = round($difference / $seconds_per_year);
        }elseif(isset($result['patient_age_in_years']) && trim($result['patient_age_in_years'])!='' && trim($result['patient_age_in_years']) >0){
          $age = $result['patient_age_in_years'];
        }elseif(isset($result['patient_age_in_months']) && trim($result['patient_age_in_months'])!='' && trim($result['patient_age_in_months']) >0){
          if($result['patient_age_in_months'] > 1){
            $age = $result['patient_age_in_months'].' months';
          }else{
            $age = $result['patient_age_in_months'].' month';
          }
        }

        if(isset($result['sample_collection_date']) && trim($result['sample_collection_date'])!='' && $result['sample_collection_date']!='0000-00-00 00:00:00'){
          $expStr=explode(" ",$result['sample_collection_date']);
          $result['sample_collection_date']=$general->humanDateFormat($expStr[0]);
          $sampleCollectionTime = $expStr[1];
        }else{
          $result['sample_collection_date']='';
          $sampleCollectionTime = '';
        }
        $sampleReceivedDate='';
        $sampleReceivedTime='';
        if(isset($result['sample_received_at_vl_lab_datetime']) && trim($result['sample_received_at_vl_lab_datetime'])!='' && $result['sample_received_at_vl_lab_datetime']!='0000-00-00 00:00:00'){
          $expStr=explode(" ",$result['sample_received_at_vl_lab_datetime']);
          $sampleReceivedDate=$general->humanDateFormat($expStr[0]);
          $sampleReceivedTime =$expStr[1];
        }

        if(isset($result['sample_tested_datetime']) && trim($result['sample_tested_datetime'])!='' && $result['sample_tested_datetime']!='0000-00-00 00:00:00'){
          $expStr=explode(" ",$result['sample_tested_datetime']);
          $result['sample_tested_datetime']=$general->humanDateFormat($expStr[0])." ".$expStr[1];
        }else{
          $result['sample_tested_datetime']='';
        }

        if(isset($result['last_viral_load_date']) && trim($result['last_viral_load_date'])!='' && $result['last_viral_load_date']!='0000-00-00'){
          $result['last_viral_load_date']=$general->humanDateFormat($result['last_viral_load_date']);
        }else{
          $result['last_viral_load_date']='';
        }
        if(!isset($result['patient_gender']) || trim($result['patient_gender'])== ''){
          $result['patient_gender'] = 'not reported';
        }
        if(isset($result['approvedBy']) && trim($result['approvedBy'])!=''){
          $resultApprovedBy = ucwords($result['approvedBy']);
        }else{
          $resultApprovedBy  = '';
        }
        $vlResult = '';
        $smileyContent = '';
        $showMessage = '';
        $tndMessage = '';
        $messageTextSize = '12px';
        if($result['result']!= NULL && trim($result['result'])!= '') {
          $resultType = is_numeric($result['result']);
          if(in_array(strtolower(trim($result['result'])),array("tnd","target not detected"))){
            $vlResult = 'TND*';
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_smile.png" alt="smile_face"/>';
            $showMessage = ucfirst($arr['l_vl_msg']);
            $tndMessage = 'TND* - Target not Detected';
          }else if(in_array(strtolower(trim($result['result'])),array("failed","fail","no_sample"))){
            $vlResult = $result['result'];
            $smileyContent = '';
            $showMessage = '';
            $messageTextSize = '14px';
          }else if(trim($result['result']) > 1000 && $result['result']<=10000000){
            $vlResult = $result['result'];
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_frown.png" alt="frown_face"/>';
            $showMessage = ucfirst($arr['h_vl_msg']);
            $messageTextSize = '15px';
          }else if(trim($result['result']) <= 1000 && $result['result']>=20){
            $vlResult = $result['result'];
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_smile.png" alt="smile_face"/>';
            $showMessage = ucfirst($arr['l_vl_msg']);
          }else if(trim($result['result'] > 10000000) && $resultType){
            $vlResult = $result['result'];
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_frown.png" alt="frown_face"/>';
            //$showMessage = 'Value outside machine detection limit';
          }else if(trim($result['result'] < 20) && $resultType){
            $vlResult = $result['result'];
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_smile.png" alt="smile_face"/>';
            //$showMessage = 'Value outside machine detection limit';
          }else if(trim($result['result'])=='<20'){
            $vlResult = '&lt;20';
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_smile.png" alt="smile_face"/>';
            $showMessage = ucfirst($arr['l_vl_msg']);
          }else if(trim($result['result'])=='>10000000'){
            $vlResult = $result['result'];
            $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_frown.png" alt="frown_face"/>';
            $showMessage = ucfirst($arr['h_vl_msg']);
          }else if($result['vl_test_platform']=='Roche'){
            $chkSign = '';
            $smileyShow = '';
            $chkSign = strchr($result['result'],'>');
            if($chkSign!=''){
              $smileyShow =str_replace(">","",$result['result']);
              $vlResult = $result['result'];
              //$showMessage = 'Invalid value';
            }
            $chkSign = '';
            $chkSign = strchr($result['result'],'<');
            if($chkSign!=''){
              $smileyShow =str_replace("<","",$result['result']);
              $vlResult = str_replace("<","&lt;",$result['result']);
              //$showMessage = 'Invalid value';
            }
            if($smileyShow!='' && $smileyShow <= $arr['viral_load_threshold_limit']){
              $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_smile.png" alt="smile_face"/>';
            }else if($smileyShow!='' && $smileyShow > $arr['viral_load_threshold_limit']){
              $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/smiley_frown.png" alt="frown_face"/>';
            }
          }
        }
        if(isset($arr['show_smiley']) && trim($arr['show_smiley']) == "no"){
          $smileyContent = '';
        }
        if($result['result_status']=='4'){
          $smileyContent = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../assets/img/cross.png" alt="rejected"/>';
        }
        $html = '';
            $html.='<table style="padding:0px 2px 2px 2px;">';
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">SPECIMEN ID</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">SPECIMEN COLLECTION DATE</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">ART (e-TRACKER) NO.</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['serial_no'].'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['sample_collection_date']." ".$sampleCollectionTime.'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['patient_art_no'].'</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
             if($arr['patient_name_pdf']=='fullname')
             {
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">PATIENT NAME</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">MOBILE NO.</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">AGE</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['patient_first_name']." ".$result['patient_last_name']).'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['patient_mobile_number'].'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$age.'</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">GENDER</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td colspan="3" style="line-height:11px;font-size:11px;text-align:left;">'.ucwords(str_replace("_"," ",$result['patient_gender'])).'</td>';
              $html .='</tr>';
             }else if($arr['patient_name_pdf']=='hidename')
             {
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">MOBILE NO.</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">AGE</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">GENDER</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['patient_mobile_number'].'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$age.'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords(str_replace("_"," ",$result['patient_gender'])).'</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
             }else{
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">PATIENT FIRST NAME</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">PATIENT LAST NAME</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">MOBILE NO.</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['patient_first_name']).'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['patient_last_name']).'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['patient_mobile_number'].'</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">AGE</td>';
               $html .='<td colspan="2" style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">GENDER</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$age.'</td>';
                $html .='<td colspan="2" style="line-height:11px;font-size:11px;text-align:left;">'.ucwords(str_replace("_"," ",$result['patient_gender'])).'</td>';
              $html .='</tr>';
              }
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:2px;border-bottom:2px solid #d3d3d3;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">ART CENTER CODE</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">REGION</td>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">DISTRICT</td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['facility_code'].'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['facility_state']).'</td>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['facility_district']).'</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">ART CENTER NAME</td>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">CLINICIAN NAME</td>';

              $html .='</tr>';
              $html .='<tr>';
                $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['facility_name']).'</td>';
                $html .='<td colspan="3" style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['request_clinician_name']).'</td>';
// New Sample


$html .='</tr>';
$html .='<tr>';
 $html .='<td colspan="3" style="line-height:10px;"></td>';
$html .='</tr>';
$html .='<tr>';
 $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">UNIQUE IDENTIFIER</td>';
 $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">TESTING LAB</td>';

$html .='</tr>';
$html .='<tr>';
  $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['patient_location']).'</td>';
  $html .='<td colspan="3" style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['lab_name']).'</td>';


// End of New

              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:2px;border-bottom:2px solid #d3d3d3;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td colspan="3">';
                 $html .='<table style="padding:2px;">';
                   $html .='<tr>';
                    $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">SPECIMEN RECEIPT DATE</td>';
                    $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">SPECIMEN TEST DATE</td>';
                    $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">SPECIMEN TYPE</td>';
                    $html .='<td style="line-height:11px;font-size:11px;font-weight:bold;text-align:left;">PLATFORM</td>';

                   $html .='</tr>';
                   $html .='<tr>';
                     $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['sample_collection_date']).'</td>';
                     $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.$result['sample_tested_datetime'].'</td>';
                     $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['sample_name']).'</td>';
                     $html .='<td style="line-height:11px;font-size:11px;text-align:left;">'.ucwords($result['vl_test_platform']).'</td>';

                   $html .='</tr>';
                   $html .='<tr>';
                     $html .='<td colspan="4" style="line-height:16px;"></td>';
                   $html .='</tr>';
                   $html .='<tr>';
                    $html .='<td colspan="3"></td>';
                    $html .='<td rowspan="3" style="text-align:left;">'.$smileyContent.'</td>';
                   $html .='</tr>';
                   $html .='<tr><td colspan="3" style="line-height:26px;font-size:12px;font-weight:bold;text-align:left;background-color:#dbdbdb;">&nbsp;&nbsp;EID RESULT&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;'.$result['result'].'</td></tr>';
                   $html .='<tr><td colspan="3"></td></tr>';
                 $html .='</table>';
                $html .='</td>';
              $html .='</tr>';
              if(trim($showMessage)!= ''){
               $html .='<tr>';
                 $html .='<td colspan="3" style="line-height:13px;font-size:'.$messageTextSize.';text-align:left;">'.$showMessage.'</td>';
               $html .='</tr>';
               $html .='<tr>';
                $html .='<td colspan="3" style="line-height:16px;"></td>';
               $html .='</tr>';
             }
             if(trim($tndMessage)!= ''){
               $html .='<tr>';
                 $html .='<td colspan="3" style="line-height:13px;font-size:18px;text-align:left;">'.$tndMessage.'</td>';
               $html .='</tr>';
               $html .='<tr>';
                $html .='<td colspan="3" style="line-height:16px;"></td>';
               $html .='</tr>';
             }
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;">VERIFIED BY&nbsp;&nbsp;:&nbsp;&nbsp;<span style="font-weight:normal;">'.$resultApprovedBy.'</span></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:10px;"></td>';
              $html .='</tr>';
              if(trim($result['approver_comments'])!= ''){
                $html .='<tr>';
                 $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;">LAB COMMENTS&nbsp;&nbsp;:&nbsp;&nbsp;<span style="font-weight:normal;">'.ucfirst($result['approver_comments']).'</span></td>';
                $html .='</tr>';
                $html .='<tr>';
                 $html .='<td colspan="3" style="line-height:10px;"></td>';
                $html .='</tr>';
              }
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:2px;border-bottom:2px solid #d3d3d3;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:14px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;">PREVIOUS RESULTS</td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:8px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;">Date of Last EID Test&nbsp;&nbsp;:&nbsp;&nbsp;<span style="font-weight:normal;">'.$result['last_viral_load_date'].'</span></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:11px;font-size:11px;font-weight:bold;">Result of previous EID Test&nbsp;&nbsp;:&nbsp;&nbsp;<span style="font-weight:normal;">'.$result['last_viral_load_result'].'</span></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:110px;border-bottom:2px solid #d3d3d3;"></td>';
              $html .='</tr>';
              $html .='<tr>';
               $html .='<td colspan="3" style="line-height:2px;"></td>';
              $html .='</tr>';
              $html .='<tr>';
                $html .='<td colspan="3">';
                 $html .='<table>';
                  $html .='<tr>';
                    $html .='<td style="font-size:10px;text-align:left;width:60%;"><img src="../assets/img/smiley_smile.png" alt="smile_face" style="width:10px;height:10px;"/> = EID  = Not Detected</td>';
                    $html .='<td style="font-size:10px;text-align:left;">Printed on : '.$printDate.'&nbsp;&nbsp;'.$printDateTime.'</td>';
                  $html .='</tr>';
                  $html .='<tr>';
                    $html .='<td colspan="2" style="font-size:10px;text-align:left;width:60%;"><img src="../assets/img/smiley_frown.png" alt="frown_face" style="width:10px;height:10px;"/> = EID = HIV-1/Detected: Clinical and counselling action required</td>';
                  $html .='</tr>';
                 $html .='</table>';
                $html .='</td>';
              $html .='</tr>';
            $html.='</table>';
        if($result['result']!=''){
          $pdf->writeHTML($html);
          $pdf->lastPage();
          $filename = $pathFront. DIRECTORY_SEPARATOR .'p'.$page. '.pdf';
          $pdf->Output($filename,"F");
          if($draftTextShow){
            //Watermark section
            $watermark = new Watermark();
            $fullPathToFile = $filename;
            $watermark->Output($filename,"F");
          }
          $pages[] = $filename;
        $page++;
        }
      if(isset($_POST['source']) && trim($_POST['source']) == 'print'){
        //Add event log
        $eventType = 'print-result';
        $action = ucwords($_SESSION['userName']).' print the test result with patient code '.$result['patient_art_no'];
        $resource = 'print-test-result';
        $data=array(
        'event_type'=>$eventType,
        'action'=>$action,
        'resource'=>$resource,
        'date_time'=>$general->getDateTime()
        );
        $db->insert($tableName1,$data);
        //Update print datetime in VL tbl.
        $db=$db->where('vl_sample_id',$result['vl_sample_id']);
        $db->update($tableName2,array('result_printed_datetime'=>$general->getDateTime()));
      }
    }

    if(count($pages) >0){
        $resultPdf = new Pdf_concat();
        $resultPdf->setFiles($pages);
        $resultPdf->setPrintHeader(false);
        $resultPdf->setPrintFooter(false);
        $resultPdf->concat();
        $resultFilename = 'vl-test-result-' . date('d-M-Y-H-i-s') .'.pdf';
        $resultPdf->Output(UPLOAD_PATH. DIRECTORY_SEPARATOR.$resultFilename, "F");
        $general->removeDirectory($pathFront);
        unset($_SESSION['rVal']);
    }
}

echo $resultFilename;
?>
