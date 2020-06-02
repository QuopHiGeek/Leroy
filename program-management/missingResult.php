<?php
$title = "eLabMessenger | Sample Status Report";
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
      <h1><i class="fa fa-book"></i> Specimen Status Report</h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Missing Result</li>
      </ol>
    </section>

     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
	    <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;">
		<tr>
		    <td><b>Specimen Collection Date&nbsp;:</b></td>
		    <td>
		      <input type="text" id="sampleCollectionDate" name="sampleCollectionDate" class="form-control" placeholder="Select Collection Date" readonly style="width:220px;background:#fff;"/>
		    </td>
		    <td>&nbsp;<b>Batch Code&nbsp;:</b></td>
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
		</tr>
		<tr>
		    <td>&nbsp;<b>Specimen Type&nbsp;:</b></td>
		    <td>
		      <select style="width:220px;" class="form-control" id="sampleType" name="sampleType" title="Please select sample type">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($sResult as $type){
			 ?>
			 <option value="<?php echo $type['sample_id'];?>"><?php echo ucwords($type['sample_name']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		
		    <td>&nbsp;<b>Site Name & ID&nbsp;:</b></td>
		    <td>
		      <select class="form-control" id="facilityName" name="facilityName" title="Please select site name" multiple="multiple" style="width:220px;">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($fResult as $name){
			 ?>
			 <option value="<?php echo $name['facility_id'];?>"><?php echo ucwords($name['facility_name']."-".$name['facility_code']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		    
		</tr>
		<tr>
		  <td colspan="4">&nbsp;<input type="button" onclick="searchResultData(),searchVlTATData();" value="Search" class="btn btn-success btn-sm">
		    &nbsp;<button class="btn btn-danger btn-sm" onclick="document.location.href = document.location"><span>Reset</span></button>
		  </td>
		</tr>
		
	    </table>
            <!-- /.box-header -->
            <div class="box-body" id="pieChartDiv">
              
            </div>
						<div class="box-body">
							<button class="btn btn-success pull-right" type="button" onclick="exportAllExcel()"><i class="fa fa-cloud-download" aria-hidden="true"></i> Export to excel</button>
              <table id="vlRequestDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
									<th>Specimen ID</th>
									<th>Specimen Collection Date</th>
                  <th>Specimen Received Date in Lab</th>
                  <th>Specimen Test Date</th>
                  <th>Specimen Print Date</th>
									<th>Specimen Email Date</th>
                </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
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
  <script src="../assets/js/highchart.js"></script>
  <script>
  $(function () {
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
     $('#sampleCollectionDate').val("");
     searchResultData();
     loadVlTATData();
     
  });
  function searchResultData()
  {
    $.blockUI();
    $.post("../includes/getMissingResult.php",{specimenCollectionDate:$("#sampleCollectionDate").val(),batchCode:$("#batchCode").val(),siteName:$("#facilityName").val(),specimenType:$("#sampleType").val()},
      function(data){
	  if(data!=''){
	    $("#pieChartDiv").html(data);
	  }
      });
    $.unblockUI();
  }
	function searchVlTATData(){
    $.blockUI();
    oTable.fnDraw();
    $.unblockUI();
  }
	function loadVlTATData(){
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
            "iDisplayLength": 10,
            "bRetrieve": true,
            "aoColumns": [
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
            ],
            "aaSorting": [[ 0, "asc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlSampleTATDetails.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
			  aoData.push({"name": "batchCode", "value": $("#batchCode").val()});
			  aoData.push({"name": "specimenCollectionDate", "value": $("#sampleCollectionDate").val()});
			  aoData.push({"name": "siteName", "value": $("#facilityName").val()});
			  aoData.push({"name": "specimenType", "value": $("#sampleType").val()});
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
	function exportInexcel() {
    $.blockUI();
    oTable.fnDraw();
    $.post("vlSampleTATDetailsExportInExcel.php",{Specimen_Collection_Date:$("#sampleCollectionDate").val(),Batch_Code:$("#batchCode  option:selected").text(),Specimen_Type:$("#sampleType  option:selected").text(),Site_Name:$("#facilityName  option:selected").text()},
    function(data){
	  if(data == "" || data == null || data == undefined){
	  $.unblockUI();
	      alert('Unable to generate excel..');
	  }else{
		$.unblockUI();
	     location.href = '../temporary/'+data;
	  }
    });
    
  }
  
function exportAllExcel(){
	$("#vlRequestDataTable").table2excel({
		exclude: ".noExl",
		name: "Sample Results",
		filename: "VL Sample Status",
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
