<?php
$title = "eLabMessenger | Clinicians";
include('../header.php');

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-gears"></i> Clinicians</h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Clinicians</li>
      </ol>
    </section>

     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          

          <div class="box">
	   
	    <span style="display: none;position:absolute;z-index: 9999 !important;color:#000;padding:5px;margin-left: 450px;" id="showhide" class="">
			<div class="row" style="background:#e0e0e0;padding: 15px;">
			    <div class="col-md-12" >
				    <div class="col-md-4">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="0" id="iCol0" data-showhide="physician_name" class="showhideCheckBox" /> <label for="iCol0">Clinician Name</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol1" data-showhide="physician_phone" class="showhideCheckBox" /> <label for="iCol1">Contact</label>
				    </div>
                    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol2" data-showhide="physician_email" class="showhideCheckBox" /> <label for="iCol1">Email</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="2" id="iCol3" data-showhide="physician_site" class="showhideCheckBox"  /> <label for="iCol2">Site</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="3" id="iCol4" data-showhide="status" class="showhideCheckBox"  /> <label for="iCol3">Status</label> <br>
				    </div>
				</div>
			    </div>
			</span>
            <div class="box-header with-border">
	      
              <?php if(isset($_SESSION['privileges']) && in_array("addPhysician.php", $_SESSION['privileges'])){ ?>
              <a href="addPhysician.php" class="btn btn-primary pull-right"> <i class="fa fa-plus"></i> Add Clinician</a>
	      <?php } ?>
	      <!--<button class="btn btn-primary pull-right" style="margin-right: 1%;" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>-->
            </div>
	    
            <!-- /.box-header -->
            <div class="box-body">
              <table id="physicianDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Clinician Name</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th>Site</th>
                  <th>Status</th>
		  <?php if(isset($_SESSION['privileges']) && in_array("editPhysician.php", $_SESSION['privileges'])){ ?>
                  <th>Action</th>
		  <?php } ?>
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
  <script>
  var oTable = null;
  $(function () {
   
  });
   
  $(document).ready(function() {
	$.blockUI();
        oTable = $('#physicianDataTable').dataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bInfo": true,
            "bScrollCollapse": true,
            "bStateSave" : true,
            "bRetrieve": true,                        
            "aoColumns": [
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
		<?php if(isset($_SESSION['privileges']) && in_array("editPhysician.php", $_SESSION['privileges'])){ ?>
                {"sClass":"center","bSortable":false},
		<?php } ?>
            ],
            "aaSorting": [[ 0, "asc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getPhysicianDetails.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
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
	} );
  
</script>
 <?php
 include('../footer.php');
 ?>
