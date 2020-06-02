<?php
$title = "eLabMessenger | Sites";
include('../header.php');
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-gears"></i> Sites</h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Sites</li>
      </ol>
    </section>

     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
	    <span style="display: none;position:absolute;z-index: 9999 !important;color:#000;padding:5px;margin-left: 325px;" id="showhide" class="">
	      <div class="row" style="background:#e0e0e0;padding: 15px;">
		  <div class="col-md-12" >
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="0" id="iCol0" data-showhide="facility_code" class="showhideCheckBox" /> <label for="iCol0">Site Code</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol1" data-showhide="facility_name" class="showhideCheckBox" /> <label for="iCol1">Site Name</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="2" id="iCol2" data-showhide="facility_type" class="showhideCheckBox"  /> <label for="iCol2">Site Type</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="3" id="iCol3" data-showhide="status" class="showhideCheckBox"  /> <label for="iCol3">Status</label> <br>
			  </div>
		      </div>
		  </div>
	      </span>
            <div class="box-header with-border">
	      <?php if(isset($_SESSION['privileges']) && in_array("addSite.php", $_SESSION['privileges'])){ ?>
              <a href="addSite.php" class="btn btn-primary pull-right"> <i class="fa fa-plus"></i> Add Site</a>
	      <?php } ?>
	      <!--<button class="btn btn-primary pull-right" style="margin-right: 1%;" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="facilityDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
		  <th>Site Code</th>
                  <th>Site Name</th>
                  <th>Site Type</th>
                  <th>Status</th>
		  <?php if(isset($_SESSION['privileges']) && in_array("editSite.php", $_SESSION['privileges'])){ ?>
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
    //$("#example1").DataTable();
  });
  $(document).ready(function() {
    $.blockUI();
        oTable = $('#facilityDataTable').dataTable({	
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
		<?php if(isset($_SESSION['privileges']) && in_array("editSite.php", $_SESSION['privileges'])){ ?>
                {"sClass":"center","bSortable":false},
		<?php } ?>
            ],
            "aaSorting": [[ 0, "asc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getSiteDetails.php",
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
