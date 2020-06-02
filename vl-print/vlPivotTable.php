<?php
$title = "eLabMessenger | Test Results Report Summary";
include('../header.php');
$tsQuery="SELECT * FROM r_sample_status";
$tsResult = $db->rawQuery($tsQuery);
$configFormQuery="SELECT * FROM global_config WHERE name ='vl_form'";
$configFormResult = $db->rawQuery($configFormQuery);
$sQuery="SELECT * FROM r_sample_type where status='active'";
$sResult = $db->rawQuery($sQuery);
$fQuery="SELECT * FROM facility_details where status='active'";
$fResult = $db->rawQuery($fQuery);
$provinceQuery="SELECT province_id, province_name FROM province_details";
$provinceResult = $db->rawQuery($provinceQuery);
$pivotQuery="SELECT resource_name FROM resources where display_name='PivotDS'";
$pivotResult = $db->rawQuery($pivotQuery);
//check filters
$collectionDate = '';
$batchCode = '';
$region = array();
$pivotDS = array();
$facilityName = array();
$age = '';
$status ='';
$lastUrl1 = '';
$lastUrl2 = '';
if(isset($_SERVER['HTTP_REFERER'])){
  $lastUrl1 = strpos($_SERVER['HTTP_REFERER'],"updateVlTestResult.php");
  $lastUrl2 = strpos($_SERVER['HTTP_REFERER'],"vlTestResult.php");
}
if($lastUrl1!='' || $lastUrl2!=''){
  $collectionDate=(isset($_COOKIE['collectionDate']) && $_COOKIE['collectionDate']!='') ? $_COOKIE['collectionDate'] :  '';
  $batchCode=(isset($_COOKIE['batchCode']) && $_COOKIE['batchCode']!='') ? $_COOKIE['batchCode'] :  '';
  $region=(isset($_COOKIE['region']) && $_COOKIE['region']!='') ? $_COOKIE['region'] :  '';
  $facilityName=(isset($_COOKIE['facilityName']) && $_COOKIE['facilityName']!='')? explode(',',$_COOKIE['facilityName']) :  array();
  $age=(isset($_COOKIE['age']) && $_COOKIE['age']!='') ? $_COOKIE['age'] :  '';
  $status=(isset($_COOKIE['status']) && $_COOKIE['status']!='') ? $_COOKIE['status'] :  '';
}
?>
  <style>
    .select2-selection__choice{
      color:black !important;
    }
    .mdc-fab {
      background-color: #3c8dbc;
      font-size: 24px;
      position: fixed;
      bottom: 1rem;
      right: 1rem;
    }
  </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-edit"></i>Test Results Summary</h1>
      <ol class="breadcrumb">
        <li><a href="../dashboard/index.php"><i class="fa fa-dashboard"></i> Home <?php echo $lastUrl1;?></a></li>
        <li class="active">Results Summary</li>
      </ol>
    </section>
     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box table-responsive">
            <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;margin-bottom: 0px;">
                  <tr>
                      <td><b>Test Period&nbsp;:</b></td>
                      <td>
                      <input type="text" id="sampleCollectionDate" name="sampleCollectionDate" class="form-control" placeholder="Select Collection Date" readonly style="width:220px;background:#fff;" value="<?php echo $collectionDate;?>"/>
                      </td>
                      <td>&nbsp;<b>Type&nbsp;:</b></td>
                      <td>
                      <select class="form-control" id="pivotDS" name="pivotDS" title="Please select pivot type" style="width:220px;">
                          <option value=""> Total Number Of Tests Done </option>
                      <?php
                      foreach($pivotResult as $pivot){
                      ?>
                      <option value="<?php echo $pivot['resource_name'];?>"<?php echo (in_array($pivot['resource_name'],$pivotDS))?"selected='selected'":""?> ><?php echo $pivot['resource_name'];?></option>
                      <?php
                      }
                      ?>
                      </select>
                      </td>
                  
                      <td><b>Region&nbsp;:</b></td>
                      <td>
                      <select style="width:220px;" class="form-control" id="region" name="region" title="Please select region" multiple="multiple">
                      <option value=""> --  All Regions -- </option>
                      <?php
                      foreach($provinceResult as $type){
                      ?>
                      <option value="<?php echo $type['province_name'];?>"<?php echo (in_array($type['province_name'],$region))?"selected='selected'":""?>><?php echo ucwords($type['province_name']);?></option>
                      <?php
                      }
                      ?>
                      </select>
                      </td>
                  </tr>
                  <tr>
                      <td><b>Facility Name&nbsp;:</b></td>
                      <td>
                      <select class="form-control" id="facilityName" name="facilityName" title="Please select site name" multiple="multiple" style="width:220px;">
                      <option value=""> All Facilities </option>
                      <?php
                      foreach($fResult as $name){
                      ?>
                      <option value="<?php echo $name['facility_id'];?>"<?php echo (in_array($name['facility_id'],$facilityName))?"selected='selected'":""?>><?php echo ucwords($name['facility_name']."-".$name['facility_code']);?></option>
                      <?php
                      }
                      ?>
                      </select>
                      </td>
                      
                  
                  <td><b>Age&nbsp;:</b></td>
                  <td>
                      <select name="age" id="age" class="form-control" title="Please choose age" style="width:220px;">
                      <option value=""> All Ages</option>
                      <option value="0 AND 4">0-4</option>
                      <option value="5 AND 14">5-14</option>
                      <option value="15 AND 49">15-49</option>
                      <option value="50 AND 999">50 and above</option>
                      </select>
                  </td>
                  
               <!--    <td><b>ANC Status &nbsp;:</b></td>
                  <td>
                      <select name="age" id="age" class="form-control" title="Please choose age" style="width:220px;">
                      <option value="pregnant">-- Select --</option>
                      <option value="pregnant">Pregnant</option>
                      <option value="not_pregnant">Not Pregnant</option>
                      
                      </select>
                  </td>  -->
                  
                  
                  </tr>
                  <tr>
                  <td colspan="6">&nbsp;<input type="button" onclick="searchVlRequestData();" value="Search" class="btn btn-default btn-sm">
                      &nbsp;<button class="btn btn-success" type="button" onclick="exportAllExcel()"><i class="fa fa-cloud-download" aria-hidden="true"></i> Export to Excel</button>
                      &nbsp;<button class="btn btn-danger btn-sm" onclick="reset();"><span>Reset</span></button>                      
                      </td>
                  </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="row table-responsive">
        <div class="col-xs-12">
          <div class="table-responsive" id="output"></div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->
  </div>
  <script type="text/javascript" src="../assets/plugins/daterangepicker/moment.min.js"></script>
  <script type="text/javascript" src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.13.0/pivot.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.13.0/pivot.min.css">
  <script type="text/javascript">
   var startDate = "";
   var endDate = "";
   var selectedTests=[];
   var selectedTestsId=[];
   var oTable = null;
  $(document).ready(function() {
     $("#facilityName").select2({placeholder:"All Facilities"});
     $("#region").select2({placeholder:"All Regions"});
     $('#sampleCollectionDate').daterangepicker({
            locale: {
              format: 'YYYY-MM-DD'
            },
	    separator: ' to ',
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            maxDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Last quarter': [moment().quarter(moment().quarter()).startOf('quarter'), moment().quarter(moment().quarter()).endOf('quarter')],
                'Previous quarter': [moment().subtract(1, 'Q').startOf('quarter'), moment().subtract(1, 'Q').endOf('quarter')],
                '1st quarter': [moment().month(0).startOf('month'), moment().month(3).subtract('days', 1).startOf('month')],
                '2nd quarter': [moment().month(2).startOf('month'), moment().month(5).endOf('month')],
                '3rd quarter': [moment().month(6).startOf('month'), moment().month(9).startOf('month').subtract('days', 1)],
                '4th quarter': [moment().month(9).startOf('month'), moment().month(12).startOf('month').subtract('days', 1)],
                'Last yr. 4th quarter': [moment().month(9).subtract('years', 1).startOf('month'), moment().month(12).subtract('years', 1).startOf('month').subtract('days', 1)],
                'Last yr. 3tr quarter': [moment().month(6).subtract('years', 1).startOf('month'), moment().month(9).subtract('years', 1).startOf('month').subtract('days', 1)],
                'Last yr. 2nd quarter': [moment().month(2).subtract('years', 1).startOf('month'), moment().month(5).subtract('years', 1).endOf('month')],
                'Last yr. 1st quarter': [moment().month(0).subtract('years', 1).startOf('month'), moment().month(3).subtract('days', 1).subtract('years', 1).startOf('month')],
            }
        },
        function(start, end) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
      });
     <?php
     if(!isset($_COOKIE['collectionDate']) || $_COOKIE['collectionDate']==''){
      ?>
      $('#sampleCollectionDate').val("");
      <?php
     } else if(($lastUrl1!='' || $lastUrl2!='') && isset($_COOKIE['collectionDate'])){ ?>
      $('#sampleCollectionDate').val("<?php echo $_COOKIE['collectionDate'];?>");
     <?php } ?>
     
     loadVlRequestData();
     $(".showhideCheckBox").change(function(){
            
            if($(this).attr('checked')){
                idpart = $(this).attr('data-showhide');
                $("#"+idpart+"-sort").show();
            }else{
                idpart = $(this).attr('data-showhide');
                $("#"+idpart+"-sort").hide();
            }
        });
        
        $("#showhide").hover(function(){}, function(){$(this).fadeOut('slow')});
        
        
  } );
  
  function fnShowHide(iCol)
    {
        var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
        oTable.fnSetColumnVis( iCol, bVis ? false : true );
    }
  function loadVlRequestData(){
    $.blockUI();
    // $_POST['batchCode']=$("#batchCode").val();
    // $_POST['region']=$("#region").val();
    // $_POST['facilityName']=$("#facilityName").val();
    // $_POST['age']=$("#age").val();
    // $_POST['status']=$("#status").val();
     $.post("getVlTestResultDetailsBackJson.php", {
         collectionDate:$("#sampleCollectionDate").val(),
         pivotDS:$("#pivotDS").val(),
         region:$("#region").val(),
         siteName:$("#facilityName").val(),
         age:$("#age").val()
        },
    //  $.get("getVlTestResultDetailsBackJson.php", { },
      function(data){
        $("#output").pivotUI(JSON.parse(data).aaData, {
                rows: ["current_regimen"],
                cols: ["patient_age_in_years"],
            });
      });
     $.unblockUI();
  }
  
  function searchVlRequestData(){
    $.blockUI();
    document.cookie = "collectionDate="+$("#sampleCollectionDate").val();
    document.cookie = "pivotDS="+$("#pivotDS").val();
    document.cookie = "region="+$("#region").val();
    document.cookie = "facilityName="+$("#facilityName").val();
    document.cookie = "age="+$("#age").val();
    document.cookie = "status="+$("#status").val();
    loadVlRequestData();
    $.unblockUI();
  }
  
  function convertResultToPdf(id){
    <?php
    $path = '';
    if($configFormResult[0]['value'] == 3){
      $path = '../result-pdf/vlRequestDrcSearchResultPdf.php';
    }else {
      $path = '../result-pdf/vlRequestSearchResultPdf.php';  
    }
    ?>
      $.post("<?php echo $path; ?>", { source:'print', id : id},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate download');
	  }else{
	      window.open('../uploads/'+data,'_blank');
	  }
      });
  }
  
  function convertSearchResultToPdf(id){
    $.blockUI();
    <?php
    $path = '';
    if($configFormResult[0]['value'] == 3){
      $path = '../result-pdf/vlRequestDrcSearchResultPdf.php';
    }else {
      $path = '../result-pdf/vlRequestSearchResultPdf.php'; 
    }
    ?>
    $.post("<?php echo $path;?>", { source:'print',id:id},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate download');
	  }else{
	      window.open('../uploads/'+data,'_blank');
	  }
      });
    $.unblockUI();
  }
  
  function exportAllVlTestResult(){
     $.blockUI();
     $.post("generateVlTestResultExcel.php", { },
      function(data){
	$.unblockUI();
       if(data === "" || data === null || data === undefined){
	 alert('Unable to generate excel..');
       }else{
	 location.href = '../temporary/'+data;
       }
      });
  }
  
  function reset(){
    document.cookie = "collectionDate=";
    document.cookie = "pivotDS=";
    document.cookie = "region=";
    document.cookie = "facilityName=";
    document.cookie = "age=";
    document.cookie = "status=";
    window.location.reload();
  }
  
  
 function exportAllExcel(){
	var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById("output");
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = 'Test_Summary.xls';
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if(navigator.msSaveOrOpenBlob){
    var blob = new Blob(['\ufeff', tableHTML], {
    type: dataType
    });
    navigator.msSaveOrOpenBlob( blob, filename);
    }else{

    downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

    downloadLink.download = filename;

    downloadLink.click();
    }
  }
</script>
 <?php
 include('../footer.php');
 ?>