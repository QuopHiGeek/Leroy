   <?php
   include('../header.php');
   //include ('https://www.koachie.org/wp-load.php');
//   if (!have_posts()) { header('HTTP/1.1 200 OK'); }
  // $facilityQuery="SELECT * FROM facility_details where facility_type = 1 AND status='active'";
   $facilityQuery="SELECT * FROM facility_details where  status='active'";
   $facilityResult = $db->rawQuery($facilityQuery);
   //print_r($facilityResult);die();
   ?>
   <link href="../assets/css/multi-select.css" rel="stylesheet" />
    <style>
        .ms-container{
          width:100%;
        }
        .select2-selection__choice{
          color:#000000 !important;
        }
	table.valign-mid td{
		vertical-align:middle !important;
	}
    </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-calendar-check-o" aria-hidden="true"></i> VL Lab Weekly Report
      <!--<ol class="breadcrumb">-->
      <!--  <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>-->
      <!--  <li class="active">Export Result</li>-->
      <!--</ol>-->
      </h1>
    </section>
     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
				<div class="widget">
					<div class="widget-content">
						<div class="bs-example bs-example-tabs">
							<ul id="myTab" class="nav nav-tabs">
								<li class="active"><a href="#labReport" data-toggle="tab">VL Lab Weekly Report</a></li>
								<li><a href="#femaleReport" data-toggle="tab">VL Lab Weekly Report - Female</a></li>
							</ul>
							<div id="myTabContent" class="tab-content table-responsive" >
								<div class="tab-pane fade in active" id="labReport">
									<table class="table valign-mid" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;">
										<tr>
											<td style=""><b>Specimen Test<br>Date Range&nbsp;:</b></td>
											<td style="width:20% !important;">
											  <input type="text" id="sampleTestDate" name="sampleTestDate" class="form-control" placeholder="Specimen Test Date Range" readonly style="background:#eee;font-size:0.9em"/>
											</td>
											<td><b>ART Centre&nbsp;:</b></td>
											<td style="width:32%;">
												<select id="lab" name="lab" class="form-control" title="Please select lab" multiple>
												 <option value=""> -- Select -- </option>
													<?php
													foreach($facilityResult as $lab){
													 ?>
													   <option value="<?php echo $lab['facility_id'];?>"><?php echo ucwords($lab['facility_name']."-".$lab['facility_code']);?></option>
													 <?php
													}
													?>
											  </select>
											</td>
											<td style="width:28%;">
												&nbsp;<input type="button" onclick="searchDataOverall();" value="Search" class="btn btn-success btn-sm">
												&nbsp;<button class="btn btn-danger btn-sm" onclick="document.location.href = document.location"><span>Reset</span></button>
												&nbsp;<button class="btn btn-info btn-sm" type="button" onclick="exportAllExcelWeekly()">Excel Export</button>
												&nbsp;<button class="btn btn-default btn-sm" type="button" onclick="exportVLWeeklyReportPdf()"><i class="fa fa-file-text"></i> PDF </button>
											</td>
										</tr>
									</table>
									<table id="vlWeeklyReportDataTable" class="table table-bordered table-striped">
										<thead>
										  <tr>
											<th rowspan="2">Region</th>
											<th rowspan="2">District</th>
											<th rowspan="2">Site Name</th>
											<!-- <th rowspan="2">IPSL</th> -->
											<th rowspan="2">No. of Rejections</th>
											<th colspan="2" style="text-align:center;">Viral Load Results - Peds</th>
											<th colspan="4" style="text-align:center;">Viral Load Results - Adults</th>
											<th colspan="2" style="text-align:center;">Viral Load Results - Pregnant/Breastfeeding Female</th>
											<th colspan="2" style="text-align:center;">Age/Sex Unknown</th>
											<th colspan="2" style="text-align:center;">Totals</th>
											<th rowspan="2">Total Test per Clinic</th>
										  </tr>
										  <tr>
											<th><= 15 y &amp; <=1000 cp/ml</th>
											<th><= 15  y &amp; >1000 cp/ml</th>
											<th>> 15  y &amp; Male <=1000 cp/ml</th>
											<th>> 15  y &amp; Male >1000 cp/ml</th>
											<th>> 15  y &amp; Female <=1000 cp/ml</th>
											<th>> 15  y &amp; Female >1000 cp/ml</th>
											<th><=1000 cp/ml</th>
											<th>>1000 cp/ml</th>
											<th>Unknown Age/Sex <=1000 cp/ml</th>
											<th>Unknown Age/Sex >1000 cp/ml</th>
											<th><=1000 cp/ml</th>
											<th>>1000 cp/ml</th>
										  </tr>
										</thead>
										<tbody>
										  <tr>
											<td colspan="19" class="dataTables_empty">Loading data from server</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="femaleReport">
									<table class="table valign-mid" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width:98%;">
										<tr>
											<td style="width:10%;"><b>Specimen Test<br>Date Range&nbsp;:</b></td>
											<td style="width:20% !important;">
											  <input type="text" id="femaleSampleTestDate" name="femaleSampleTestDate" class="form-control" placeholder="Specimen Test Date Range" readonly style="background:#eee;font-size:0.9em"/>
											</td>
											<td style="width: 10%;"><b>ART Centre&nbsp;:</b></td>
											<td>
												<select id="femaleLab" name="femaleLab" class="form-control" title="Please select lab" multiple>
												 <option value=""> -- Select -- </option>
													<?php
													foreach($facilityResult as $lab){
													 ?>
													   <option value="<?php echo $lab['facility_id'];?>"><?php echo ucwords($lab['facility_name']."-".$lab['facility_code']);?></option>
													 <?php
													}
													?>
											  </select>
											</td>
											<td style="width:40%;">
												&nbsp;<input type="button" onclick="searchDataFemale();" value="Search" class="btn btn-success btn-sm">
												&nbsp;<button class="btn btn-danger btn-sm" onclick="document.location.href = document.location"><span>Reset</span></button>
												&nbsp;<button class="btn btn-info btn-sm" type="button" onclick="exportAllExcelWeeklyFemale()">Excel Export</button>
											</td>
										</tr>
									</table>
									<table id="vlWeeklyFemaleReportDataTable" class="table table-bordered table-striped">
										<thead>
										  <tr>
											<th>Region</th>
											<th>District</th>
											<th>Site Name</th>
											<th>Total Female</th>
											<th>Pregnant <=1000 cp/ml</th>
											<th>Pregnant >1000 cp/ml</th>
											<th>Breastfeeding <=1000 cp/ml</th>
											<th>Breastfeeding >1000 cp/ml</th>
											<th>Age > 15 <=1000 cp/ml</th>
											<th>Age > 15 >1000 cp/ml</th>
											<th>Age Unknown <=1000 cp/ml</th>
											<th>Age Unknown >1000 cp/ml</th>
											<th>Age <=15 <=1000 cp/ml </th>
											<th>Age <=15 >1000 cp/ml</th>
										  </tr>
										</thead>
										<tbody>
										  <tr>
											<td colspan="13" class="dataTables_empty">Loading data from server</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
              
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
    var oTable = null;
    var oTableFemale = null;
    $(document).ready(function() {
        $('#lab').select2({placeholder:"All Labs"});
        $('#femaleLab').select2({width:'190px',placeholder:"All Labs"});
        $('#sampleTestDate,#femaleSampleTestDate').daterangepicker({
            format: 'DD-MMM-YYYY',
	    separator: ' to ',
            startDate: moment('2000-10-10T00:000000'),
            endDate: moment('2020-10-10T00:000000'),
            maxDate: moment('2020-10-10T00:000000'),
            ranges: {
                'Today': [moment('2000-10-10T00:000000'), moment('2020-10-10T00:000000')],
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
       loadDataTable();
       loadFemaleDataTable();
    } );
  
   function loadDataTable(){

       oTable = $('#vlWeeklyReportDataTable').dataTable({
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
            "iDisplayLength": 10,
            "bRetrieve": true,                        
            "aoColumns": [
                {"sClass":"center"},
                {"sClass":"center"},
                // {"sClass":"center"},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false}
            ],
            "aaSorting": [[ 2, "asc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlWeeklyReport.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push({"name": "sampleTestDate", "value": $("#sampleTestDate").val()});
                aoData.push({"name": "lab", "value": $("#lab").val()});
              $.ajax({
                  "dataType": 'json',
                  "type": "POST",
                  "url": sSource,
                  "data": aoData,
                  "success": fnCallback
              })
            }
        });

    }
	function loadFemaleDataTable(){
       oTableFemale = $('#vlWeeklyFemaleReportDataTable').dataTable({
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
            "iDisplayLength": 10,
            "bRetrieve": true,                        
            "aoColumns": [
				{"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false},
                {"sClass":"center","bSortable":false}
            ],
            //"aaSorting": [[ 0, "asc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlWeeklyFemaleReport.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push({"name": "sampleTestDate", "value": $("#femaleSampleTestDate").val()});
                aoData.push({"name": "lab", "value": $("#femaleLab").val()});
              $.ajax({
                  "dataType": 'json',
                  "type": "POST",
                  "url": sSource,
                  "data": aoData,
                  "success": fnCallback
              })
            }
        });
    }
    
    function searchDataOverall(){
       $.blockUI();
       oTable.fnDraw();
       //oTableFemale.fnDraw();
       $.unblockUI(); 
    }
	
	 function searchDataFemale(){
       $.blockUI();
      // oTable.fnDraw();
       oTableFemale.fnDraw();
       $.unblockUI(); 
    }
    
    
    function exportVLWeeklyReport(){
       $.blockUI();
       $.post("generateVlWeeklyReportExcel.php",{reportedDate:$("#sampleTestDate").val(),lab:$("#lab").val(),searchData:$('.dataTables_filter input').val()},
       function(data){
	     $.unblockUI();
	     if(data == "" || data == null || data == undefined){
		 alert('Unable to generate excel..');
	     }else{
	        $.unblockUI();
		location.href = '../temporary/'+data;
	     }
       });
    }
	function exportFemaleVLWeeklyReport(){
       $.blockUI();
       $.post("generateVlWeeklyFemaleReportExcel.php",{sample_test_date:$("#femaleSampleTestDate").val(),lab:$("#femaleLab").val(),searchData:$('.dataTables_filter input').val()},
       function(data){
	     $.unblockUI();
	     if(data == "" || data == null || data == undefined){
		 alert('Unable to generate excel..');
	     }else{
	        $.unblockUI();
		location.href = '../temporary/'+data;
	     }
       });
    }
    
    function exportVLWeeklyReportPdf(){
      $.blockUI();
       $.post("getVlWeeklyReportPdf.php",{reportedDate:$("#sampleTestDate").val(),lab:$("#lab").val(),searchData:$('.dataTables_filter input').val()},
       function(data){
	     $.unblockUI();
	     if(data == "" || data == null || data == undefined){
		 alert('Unable to generate pdf..');
	     }else{
	        $.unblockUI();
		window.open(
		  '../uploads/'+data,
		  '_blank' // <- This is what makes it open in a new window.
		);

	     }
       });
    }
function exportAllExcelWeekly(){
	$("#vlWeeklyReportDataTable").table2excel({
		exclude: ".noExl",
		name: "VL Weekly Report",
		filename: "VL Weekly Report",
		fileext: ".xls",
		exclude_img: true,
		exclude_links: true,
		exclude_inputs: true
		
	});
}

function exportAllExcelWeeklyFemale(){
	$("#vlWeeklyFemaleReportDataTable").table2excel({
		exclude: ".noExl",
		name: "VL Weekly Female Report",
		filename: "VL Female Weekly Report",
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