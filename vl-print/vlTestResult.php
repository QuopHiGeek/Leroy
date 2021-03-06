<?php
$title = "eLabMessenger | Enter Result";
include('../header.php');
$tsQuery="SELECT * FROM r_sample_status";
$tsResult = $db->rawQuery($tsQuery);
$configFormQuery="SELECT * FROM global_config WHERE name ='vl_form'";
$configFormResult = $db->rawQuery($configFormQuery);
$sQuery="SELECT * FROM r_sample_type where status='active'";
$sResult = $db->rawQuery($sQuery);
$fQuery="SELECT * FROM facility_details where status='active'";
$fResult = $db->rawQuery($fQuery);
$batQuery="SELECT batch_code FROM batch_details where batch_status='completed'";
$batResult = $db->rawQuery($batQuery);
//check filters
$collectionDate = '';
$batchCode = '';
$sampleType = '';
$facilityName = array();
$gender = '';
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
  $sampleType=(isset($_COOKIE['sampleType']) && $_COOKIE['sampleType']!='') ? $_COOKIE['sampleType'] :  '';
  $facilityName=(isset($_COOKIE['facilityName']) && $_COOKIE['facilityName']!='')? explode(',',$_COOKIE['facilityName']) :  array();
  $gender=(isset($_COOKIE['gender']) && $_COOKIE['gender']!='') ? $_COOKIE['gender'] :  '';
  $status=(isset($_COOKIE['status']) && $_COOKIE['status']!='') ? $_COOKIE['status'] :  '';
}
?>
  <style>
    .select2-selection__choice{
      color:black !important;
    }
  </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-edit"></i> Enter Result</h1>
      <ol class="breadcrumb">
        <li><a href="../dashboard/index.php"><i class="fa fa-dashboard"></i> Home <?php echo $lastUrl1;?></a></li>
        <li class="active">Enter Result</li>
      </ol>
    </section>
     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
	    <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;margin-bottom: 0px;">
		<tr>
		    <td><b>Specimen Collection Date&nbsp;:</b></td>
		    <td>
		      <input type="text" id="sampleCollectionDate" name="sampleCollectionDate" class="form-control" placeholder="Select Collection Date" readonly style="width:220px;background:#fff;" value="<?php echo $collectionDate;?>"/>
		    </td>
		    <td>&nbsp;<b>Batch ID&nbsp;:</b></td>
		    <td>
		      <select class="form-control" id="batchCode" name="batchCode" title="Please select batch code" style="width:220px;">
		        <option value=""> -- Select -- </option>
			 <?php
			 foreach($batResult as $code){
			  ?>
			  <option value="<?php echo $code['batch_code'];?>"<?php echo ($batchCode==$code['batch_code'])?"selected='selected'":""?> ><?php echo $code['batch_code'];?></option>
			  <?php
			 }
			 ?>
		      </select>
		    </td>
		
		    <td><b>Specimen Type&nbsp;:</b></td>
		    <td>
		      <select style="width:220px;" class="form-control" id="sampleType" name="sampleType" title="Please select specimen type">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($sResult as $type){
			 ?>
			 <option value="<?php echo $type['sample_id'];?>"<?php echo ($sampleType==$type['sample_id'])?"selected='selected'":""?>><?php echo ucwords($type['sample_name']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		</tr>
		<tr>
		    <td><b>Site Name :</b></td>
		    <td>
		      <select class="form-control" id="facilityName" name="facilityName" title="Please select site name" multiple="multiple" style="width:220px;">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($fResult as $name){
			 ?>
			 <option value="<?php echo $name['facility_id'];?>"<?php echo (in_array($name['facility_id'],$facilityName))?"selected='selected'":""?>><?php echo ucwords($name['facility_name']."-".$name['facility_code']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		    
		
		  <td><b>Gender&nbsp;:</b></td>
		  <td>
		    <select name="gender" id="gender" class="form-control" title="Please choose gender" style="width:220px;">
		      <option value=""> -- Select -- </option>
		      <option value="male"<?php echo ($gender=='male')?"selected='selected'":""?>>Male</option>
		      <option value="female"<?php echo ($gender=='female')?"selected='selected'":""?>>Female</option>
		      <option value="not_recorded"<?php echo ($gender=='not_recorded')?"selected='selected'":""?>>Not Recorded</option>
		    </select>
		  </td>
		  <td><b>Status&nbsp;:</b></td>
		  <td>
		      <select style="width: 220px;" name="status" id="status" class="form-control" title="Please choose status">
			<option value="">-- Select --</option>
			<option value="7"<?php echo ($status=='7')?"selected='selected'":""?>>Accepted</option>
			<option value="4"<?php echo ($status=='4')?"selected='selected'":""?>>Rejected</option>
		      </select>
		    </td>
		</tr>
		<tr>
		  <td colspan="6">&nbsp;<input type="button" onclick="searchVlRequestData();" value="Search" class="btn btn-default btn-sm">
		    &nbsp;<button class="btn btn-danger btn-sm" onclick="reset();"><span>Reset</span></button>
			<!-- &nbsp;<button class="btn btn-default btn-sm" onclick="convertSearchResultToPdf('');"><span>Result PDF</span></button> -->
			&nbsp;<a class="btn btn-success btn-sm" href="javascript:void(0);" onclick="exportAllExcel();"><i class="fa fa-cloud-download" aria-hidden="true"></i> Export Excel</a>
			&nbsp;<button class="btn btn-primary btn-sm" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>
			
		    </td>
		</tr>
	    </table>
            <span style="display: none;position:absolute;z-index: 9999 !important;color:#000;padding:5px;" id="showhide" class="">
			<div class="row" style="background:#e0e0e0;padding: 15px;margin-top: -5px;">
			    <div class="col-md-12" >
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="0" id="iCol0" data-showhide="sample_code"  class="showhideCheckBox" /> <label for="iCol0">Sample Code</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol1" data-showhide="batch_code" class="showhideCheckBox" /> <label for="iCol1">Batch Code</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="2" id="iCol2" data-showhide="patient_art_no" class="showhideCheckBox"  /> <label for="iCol2">Art No</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="3" id="iCol3" data-showhide="patient_first_name" class="showhideCheckBox"  /> <label for="iCol3">Patient's Name</label> <br>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="4" id="iCol4" data-showhide="facility_name" class="showhideCheckBox"  /> <label for="iCol4">Facility Name</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="5" id="iCol5" data-showhide="sample_name" class="showhideCheckBox" /> <label for="iCol5">Sample Type</label> <br>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="6" id="iCol6" data-showhide="result"  class="showhideCheckBox" /> <label for="iCol6">Result</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="7" id="iCol7" data-showhide="modified_on"  class="showhideCheckBox" /> <label for="iCol7">Modified On</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="8" id="iCol8" data-showhide="status_name"  class="showhideCheckBox" /> <label for="iCol8">Status</label>
				    </div>
				    
				</div>
			    </div>
			</span>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="vlRequestDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
		  <th>Specimen ID</th>
                  <th>Batch ID</th>
                  <th>Unique ART No</th>
                  <th>Patient's Name</th>
				  <th>Gender</th>
				  <th>Age</th>
		  <th>Site Name</th>
                  <th>Specimen Type</th>
		  <th>Specimen Collection Date</th>
		 <th>Specimen Recieved Date At Lab</th>
		  <th>Specimen Testing Date</th>
		  <th>Results Dispatched Date</th>
		 <th>Unique Identifier</th>
                  <th>Result</th>
                  <th>Modified On</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="10" class="dataTables_empty">Loading data from server</td>
                </tr>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <script type="text/javascript" src="../assets/plugins/daterangepicker/moment.min.js"></script>
  <script type="text/javascript" src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
  <script type="text/javascript">
   var startDate = "";
   var endDate = "";
   var selectedTests=[];
   var selectedTestsId=[];
   var oTable = null;
  $(document).ready(function() {
     $("#facilityName").select2({placeholder:"Select Sites"});
     $('#sampleCollectionDate').daterangepicker({
            format: 'DD-MMM-YYYY',
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
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
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
        
        for(colNo=0;colNo <9;colNo++){
            $("#iCol"+colNo).attr("checked",oTable.fnSettings().aoColumns[parseInt(colNo)].bVisible);
            if(oTable.fnSettings().aoColumns[colNo].bVisible){
                $("#iCol"+colNo+"-sort").show();    
            }else{
                $("#iCol"+colNo+"-sort").hide();    
            }
        }
  } );
  
  function fnShowHide(iCol)
    {
        var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
        oTable.fnSetColumnVis( iCol, bVis ? false : true );
    }
  function loadVlRequestData(){
    $.blockUI();
     oTable = $('#vlRequestDataTable').dataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
			dom: 'Blfrtip',
			buttons: [
				'excel',
			],
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bInfo": true,
            "bScrollCollapse": true,
            //"bStateSave" : true,
            "iDisplayLength": 100,
            "bRetrieve": true,                             
            "aoColumns": [
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
		{"sClass":"center"},
		{"sClass":"center"},
                {"sClass":"center"},
		{"sClass":"center"},
		{"sClass":"center"},
		{"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
		{"sClass":"center"},                
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
            ],
            "aaSorting": [[ 7, "desc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlTestResultDetails.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
		aoData.push({"name": "batchID", "value": $("#batchCode").val()});
		aoData.push({"name": "specimenCollectionDate", "value": $("#sampleCollectionDate").val()});
		aoData.push({"name": "siteName", "value": $("#facilityName").val()});
	        aoData.push({"name": "specimenType", "value": $("#sampleType").val()});
		aoData.push({"name": "status", "value": $("#status").val()});
		aoData.push({"name": "gender", "value": $("#gender").val()});
		aoData.push({"name": "from", "value": "enterresult"});
              $.ajax({
                  "dataType": 'json',
                  "type": "POST",
                  "url": sSource,
                  "data": aoData,
                  "success": fnCallback
              });
            }
        });
     $.unblockUI();
  }
  
  function searchVlRequestData(){
    $.blockUI();
    oTable.fnDraw();
    document.cookie = "collectionDate="+$("#sampleCollectionDate").val();
    document.cookie = "batchCode="+$("#batchCode").val();
    document.cookie = "sampleType="+$("#sampleType").val();
    document.cookie = "facilityName="+$("#facilityName").val();
    document.cookie = "gender="+$("#gender").val();
    document.cookie = "status="+$("#status").val();
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
    document.cookie = "batchCode=";
    document.cookie = "sampleType=";
    document.cookie = "facilityName=";
    document.cookie = "gender=";
    document.cookie = "status=";
    window.location.reload();
  }
  
  
 function exportAllExcel(){
	$("#vlRequestDataTable").table2excel({
		exclude: ".noExl",
		name: "VL Pending Results Entry",
		filename: "Pending Results Entry Doc",
		fileext: ".xls",
		exclude_img: true,
		exclude_links: true,
		exclude_inputs: true
		
	});
}
</script>
 <?php
 include('../footer.php');
 ?>