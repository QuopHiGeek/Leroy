<?php
//session_start();
$title = "eLabMessenger | Manage Batch";
//var_dump($_SESSION["siteID"]);die;
//echo'<pre>';print_r($_SESSION);die;
include('../header.php');
//var_dump($_SESSION)
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-edit"></i>Search Patient</h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Search Patients</li>
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
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="0" id="iCol0" data-showhide="batch_code" class="showhideCheckBox" /> <label for="iCol0">Batch ID</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol1" data-showhide="''" class="showhideCheckBox" /> <label for="iCol1">No. Of Specimen</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="2" id="iCol2" data-showhide="request_created_datetime" class="showhideCheckBox"  /> <label for="iCol2">Created On</label>
			  </div>
			  <div class="col-md-4">
				  <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="3" id="iCol3" data-showhide="batch_status" class="showhideCheckBox"  /> <label for="iCol3">Status</label> <br>
			  </div>
		      </div>
		  </div>
	      </span>

            <div class="box-header with-border">
	      
	      <!--<button class="btn btn-primary pull-right" style="margin-right: 1%;" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>-->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="batchCodeDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>User ID</th>
                  <th>Username</th>
                  <th>Login Time</th>
                  <th>Logout Time</th>
                  <th>IP Address</th>
                  <th>Date</th>
		  <?php if(isset($_SESSION['privileges']) && in_array("editBatch.php", $_SESSION['privileges'])){ ?>
                <!--  <th>Action</th> -->
		  <?php } ?>
                </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="3" class="dataTables_empty">Loading data from server</td>
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
  $(document).ready(function() {
    $.blockUI();
        oTable = $('#batchCodeDataTable').dataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bInfo": true,
            "bScrollCollapse": true,
            //"bStateSave" : true,
            "bRetrieve": true,
            "aoColumns": [
                {"sClass":"center"},
	        {"sClass":"center","bSortable":false},
	        {"sClass":"center","bSortable":false},
	        {"sClass":"center","bSortable":false},
	        {"sClass":"center","bSortable":false},
	        {"sClass":"center","bSortable":false},
                {"sClass":"center"},
		<?php if(isset($_SESSION['privileges']) && in_array("editBatch.php", $_SESSION['privileges'])){ ?>
                {"sClass":"center","bSortable":false},
		<?php } ?>
            ],
            "aaSorting": [[ 6, "desc" ]],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getBatchCodeDetails.php",
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
  
  function generateBarcode(bId){
    $.post("generateBarcode.php",{id:bId},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate barcode');
	  }else{
	      window.open('.././uploads/barcode/'+data,'_blank');
	  }
	  
      });
  }

  function generateQRcode(bId){
     $.blockUI();
     $.post("generateQRcode.php",{id:bId},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate QR code');
	  }else{
	      window.open('../uploads/qrcode/'+data,'_blank');
	  }
	  $.unblockUI();
      });
  }



  function notifyGHPost(bId,value) {
      $.blockUI();
      var id = bId;
      var url = "http://artsites.bigdataghana.com/routinginfo/?faciltyid="+id;
      $.ajax({
          crossOrigin: true,
          url: url,
          success: function(data) {
              var obj = JSON.parse(data);
            if(obj == "pass"){
                alert("GhanaPost Notified");
                updateStatus(value,1)
            }else{
                alert("GhanaPost server down!!!");
            }



          }
      });

  }


 function updateStatus(id,value){

     $.ajax({
         url: "updateBatchStatus1.php",
         type: "POST",
         data: {"id":id,"value":value},
         success: function(data){
             data = JSON.toString(data);
             location.reload();
         }
     });

   }
 
</script>
 <?php
 include('../footer.php');
 ?>
