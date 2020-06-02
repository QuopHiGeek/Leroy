<?php
$title = "eLabMessenger | User Log Details";
include('../header.php');
//include('../includes/MysqliDb.php');
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
?>
<style>
  .select2-selection__choice{
    color:#000000 !important;
  }
  .center{
    text-align:center;
  }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-edit"></i>User Log Details </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Daily Logs</li>
      </ol>
    </section>

     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
	    <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;">
		<tr>
		    <td><b>Date&nbsp;:</b></td>
		    <td>
        <input type="text" id="sampleTestDate" name="sampleTestDate" class="form-control" placeholder="Select Sample Test Date" readonly style="width:220px;background:#fff;"/>
		     <!-- <input type="text" id="sampleCollectionDate" name="sampleCollectionDate" class="form-control" placeholder="Select Collection Date" readonly style="width:220px;background:#fff;"/> -->
		    </td>
		    <td><b>Username&nbsp;:</b></td>
		    <td>
		      <select class="form-control" id="batchCode" name="batchCode" title="Please select batch code" style="width:220px;">
		        <option value=""> -- Select -- </option>
			 <?php
			 foreach($batResult as $code){
			  ?>
			  <option value="<?php echo $code['batch_code'];?>"><?php echo $code['batch_code'];?></option>
			  <?php
			 }
			 ?>
		      </select>
		    </td>
		
		    <td><b>IP Address&nbsp;:</b></td>
		    <td>
		      <select style="width:220px;" class="form-control" id="sampleType" name="sampleType" title="Please select sample type">
		      <option value="7"></option>
		      </select>
		    </td>
		</tr>
		
		<tr>
		  <td colspan="6">&nbsp;<input type="button" onclick="searchVlRequestData();" value="Search" class="btn btn-success btn-sm">
		    &nbsp;<button class="btn btn-danger btn-sm" onclick="document.location.href = document.location"><span>Reset</span></button>
		    &nbsp;<button class="btn btn-default btn-sm" onclick="convertSearchResultToPdf('');"><span>Result PDF</span></button>
			&nbsp;<button class="btn btn-success" type="button" onclick="exportAllExcel()"><i class="fa fa-cloud-download" aria-hidden="true"></i> Export to excel</button>
			&nbsp;<button class="btn btn-primary btn-sm" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>
			<a href="offSiteResultRecords.php" class="btn btn-primary btn-sm" target ="_blank"style="display:<?php if($_SESSION['roleId']==2) {echo 'inline-block';} else {echo 'none';}	?>;"	></i>Search External Requests Records</a>
 <a href="vlEditPrintResult.php" class="btn btn-primary btn-sm" target ="" style="display:<?php if($_SESSION['userId']==20) {echo 'inline-block';} else {echo 'none';}	?>;"></i>Modify Results</a>
		    
		    </td>
		</tr>
		
	    </table>
	    <span style="display: none;position:absolute;z-index: 9999 !important;color:#000;padding:5px;" id="showhide" class="">
			<div class="row" style="background:#e0e0e0;float: right !important;padding: 15px;margin-top: -30px;">
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
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="7" id="iCol7" data-showhide="last_modified_datetime"  class="showhideCheckBox" /> <label for="iCol8">Last Modified On</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="8" id="iCol8" data-showhide="status_name"  class="showhideCheckBox" /> <label for="iCol7">Status</label>
				    </div>
				    
				</div>
			    </div>
			</span>
           
            <!-- /.box-header -->
            <div class="box-body" style="margin-top:-30px;">
              <table id="vlRequestDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
		  <th>Date</th>
                  <th>User ID</th>
                  <th>Log In Time</th>
                  <th>Log out Time</th>
				  <th>IP Address</th>
				 
		  
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
     $("#facility").select2({placeholder:"Select Sites"});
     $('#sampleCollectionDate,#sampleTestDate').daterangepicker({
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
     $('#sampleCollectionDate,#sampleTestDate').val("");
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
  
  function fnShowHide(iCol){
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
	    "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100, "All"]],
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bInfo": true,
            "bScrollCollapse": true,
            "bStateSave" : true,
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
                {"sClass":"center"},
                {"sClass":"center","bSortable":false},
            ],
            "aaSorting": [[ 7, "desc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlTestResultDetailsDBS.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
			      aoData.push({"name": "batchID", "value": $("#batchCode").val()});
			      aoData.push({"name": "specimenCollectionDate", "value": $("#sampleCollectionDate").val()});
			      aoData.push({"name": "siteName", "value": $("#facility").val()});
			      aoData.push({"name": "specimenType", "value": $("#sampleType").val()});
			      aoData.push({"name": "vlPrint", "value": 'print'});
			      aoData.push({"name": "gender", "value": $("#gender").val()});
			      aoData.push({"name": "artNo", "value": $("#artNo").val()});
			      aoData.push({"name": "specimenTestDate", "value": $("#sampleTestDate").val()});
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
  
  function exportAllExcel(){
	$("#vlRequestDataTable").table2excel({
		exclude: ".noExl",
		name: "Export Data",
		filename: "VL Export Data",
		fileext: ".xls",
		exclude_img: true,
		exclude_links: true,
		exclude_inputs: true
		
	});
}
  
  function searchVlRequestData(){
    $.blockUI();
    oTable.fnDraw();
    $.unblockUI();
  }
  
  function convertResultToPdf(id){
      $.blockUI();
      <?php
      $path = '';
      if($configFormResult[0]['value'] == 3){
	$path = '../result-pdf/vlRequestDrcSearchResultPdf.php';
      }else {
	$path = '../result-pdf/vlRequestSearchResultPdfDBS.php'; 
      }
      ?>
      $.post("<?php echo $path; ?>", { source:'print', id : id},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      $.unblockUI();
	      alert('Unable to generate download');
	  }else{
	      $.unblockUI();
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
    $.post("<?php echo $path; ?>", { source:'print',id : id},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      $.unblockUI();
	      alert('Unable to generate download');
	  }else{
	      $.unblockUI();
	      window.open('../uploads/'+data,'_blank');
	  }
	  
      });
	  
	  

  }

</script>
 <?php
 include('../footer.php');
 ?>