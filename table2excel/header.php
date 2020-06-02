
<?php
session_start();
include('../includes/MysqliDb.php');
$gQuery = "SELECT * FROM global_config";
$gResult=$db->query($gQuery);
$global = array();
// now we create an associative array so that we can easily create view variables
for ($i = 0; $i < sizeof($gResult); $i++) {
  $global[$gResult[$i]['name']] = $gResult[$i]['value'];
}
if(isset($global['default_time_zone']) && count($global['default_time_zone'])> 0){
  date_default_timezone_set($global['default_time_zone']);
}else{
  date_default_timezone_set("Europe/London");
}
$hideResult = '';$hideRequest='';
if(isset($global['instance_type']) && $global['instance_type']!=''){
    if($global['instance_type']=='Clinic/Lab'){
        $hideResult = "display:none;";
    }
    //else if($global['instance_type']=='Viral Load Lab'){
       // $hideRequest = "display:none;";
    //}
}
if(!isset($_SESSION['userId'])){
    header("location:../login.php");
}

$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
if(end($link_array)!='vl-dash.php' && end($link_array)!='vlResultUnApproval.php' && end($link_array)!='importedStatistics.php' && end($link_array)!='vlExportField.php'
&& end($link_array)!='editUserHelper.php' && end($link_array)!='users.php' && end($link_array)!='offSiteRequestRecords.php' && end($link_array)!='offSiteResultRecords.php'){
  if(isset($_SESSION['privileges']) && !in_array(end($link_array), $_SESSION['privileges'])){
    header("location:../dash/vl-dash.php");
  }
}
if(isset($_SERVER['HTTP_REFERER'])){
  $previousUrl = $_SERVER['HTTP_REFERER'];
  $urlLast = explode('/',$previousUrl);
  if(end($urlLast)=='importedStatistics.php'){
      $db->delete('temp_sample_import');
      unset($_SESSION['controllertrack']);
  }
}
if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('roles.php', 'physicians.php','sites.php','globalConfig.php','importConfig.php','otherConfig.php'))) {
  $allAdminMenuAccess = true;
}else{
  $allAdminMenuAccess = false;
}

if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('vlRequest.php' ,'addVlRequest.php','batchcode.php','vlRequestMail.php'))) {
  $requestMenuAccess = true;
}else{
  $requestMenuAccess = false;
}
//New code added for client
//NEW $requestMenuAccess
if(isset($_SESSION['privilegees']) && array_intersect($_SESSION['privilegees'], array('vlRequeest.php' ,'addVlRequest.php','batchcode.php','vlRequestMail.php'))) {
  $requestMenuAcceess = true;
}else{
  $requestMenuAcceess = false;
}
//END OF NEW $requestMenuAccess

if($_SESSION['loginId'] == 'ghana_site')
{
    $requestMenuAccess = true;
    $requestMenuAcceess = false;
}


// if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('vlRequest.php' ,'addVlRequest.php','batchcode.php','vlRequestMail.php'))) {
//   $requestMenuAccess = true;
// }else{
//   $requestMenuAccess = false;
// }

if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('addImportResult.php','vlTestResult.php','vlPivotTable.php'))) {
  $testResultMenuAccess = true;
}else{
  $testResultMenuAccess = false;
}

//NEW $testResultMenuAccess
if(isset($_SESSION['privilegees']) && array_intersect($_SESSION['privilegees'], array('addImportResult.php','vlTestResult.php','vlPivotTable.php'))) {
  $testResultMenuAcceess = true;
}else{
  $testResultMenuAcceess = false;
}
//END OF NEW $testResultMenuAcceess

if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('missingResult.php', 'vlResult.php','highViralLoad.php','vlPrintResult.php','vlPrintDBSResult.php','vlPivotTable.php'))) {
  $managementMenuAccess = true;
}else{
  $managementMenuAccess = false;
}

//NEW $managementMenuAccess
if(isset($_SESSION['privilegees']) && array_intersect($_SESSION['privilegees'], array('missingResult.php', 'vlResult.php','highViralLoad.php','vlPrintResult.php','vlPrintDBSResult.php','vlPivotTable.php'))) {
  $managementMenuAcceess = true;
}else{
  $managementMenuAcceess = false;
}
//END OF NEW $managementMenuAccess

if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('genericIDSR.php','laboratoryIDSR.php'))) {
    $idsrMenuAccess = true;
}else{
    $idsrMenuAccess = false;
}

//NEW $idsrMenuAccess
if(isset($_SESSION['privilegees']) && array_intersect($_SESSION['privilegees'], array('genericIDSR.php','laboratoryIDSR.php'))) {
    $idsrMenuAcceess = true;
}else{
    $idsrMenuAcceess = false;
}
//END OF NEW $idsrMenuAccess


if(isset($_SESSION['privileges']) && in_array(('index.php'),$_SESSION['privileges']))
{
  $dashBoardMenuAccess = true;
}else{
  $dashBoardMenuAccess = false;
}

// //NEW $dashBoardMenuAccess
if(isset($_SESSION['privilegees']) && in_array(('index.php'),$_SESSION['privilegees']))
{
  $dashBoardMenuAcceess = true;
}else{
  $dashBoardMenuAcceess = false;
}
// //END OF NEW $dashBoardMenuAccess

$formConfigQuery ="SELECT * from global_config where name='vl_form'";
$formConfigResult=$db->query($formConfigQuery);

//var_dump($_SESSION['privileges']);






?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<!--    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"/>-->
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible " content="IE=edge "/>

  <title><?php echo (isset($title) && $title != null && $title != "") ? $title : "eLabMessenger | Laboratory Information System" ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/assets/img/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/assets/img/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/assets/img/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="/assets/img/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png">
  <link rel="manifest" href="/assets/img/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/fonts.css" />

  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/jquery-ui.1.11.0.css" />
  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/jquery-ui-timepicker-addon.css" />

  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/css/font-awesome.min.4.5.0.css">

  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- DataTables -->
  <link rel="stylesheet" href=".././assets/plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->

  <link href="../assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" />

  <link href="../assets/css/select2.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="../assets/css/deforayModal.css" rel="stylesheet" />
  <link href="../assets/css/jquery.fastconfirm.css" rel="stylesheet" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesnt work if you view the page via file: -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery 2.2.3 -->

<script type="text/javascript" src="../assets/js/jquery.min.2.0.2.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>

<script type="text/javascript" src="/table2excel/dist/jquery.table2excel.min.js"></script>

<!--    <script-->
<!--            src="https://code.jquery.com/jquery-3.3.1.min.js"-->
<!--            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="-->
<!--            crossorigin="anonymous"></script>-->
 <!-- Latest compiled and minified JavaScript -->

<script type="text/javascript" src="../assets/js/jquery-ui.1.11.0.js"></script>
<script src="../assets/js/deforayModal.js"></script>
<script src="../assets/js/jquery.fastconfirm.js"></script>
  <!--<script type="text/javascript" src="assets/js/jquery-ui-sliderAccess.js"></script>-->

    <script type="text/javascript" src="../assets/js/jquery.ajax-cross-origin.min.js"></script>

<link href="https://unpkg.com/material-components-web@v4.0.0/dist/material-components-web.min.css" rel="stylesheet">
<script src="https://unpkg.com/material-components-web@v4.0.0/dist/material-components-web.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">


<script>
function blinker() {
	$('.blinking').fadeOut(500);
	$('.blinking').fadeIn(500);
}
setInterval(blinker, 1000);
</script>

<style>
  .dataTables_wrapper{
  position: relative;
    clear: both;
    overflow-x: scroll !important;
    overflow-y: visible !important;
    padding: 15px 0 !important;
  }


  .select2-selection__choice__remove{
    color: red !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    background-color: #00c0ef;
    border-color: #00acd6;
    color: #fff !important;
    font-family:helvetica, arial, sans-serif;
  }

</style>

<style>
    .src{
        border-radius:47%;
        width:300px;
        height:300px;
        margin:1px auto;
        display:block;
    }

</style>
<!-- Custom code for input process -->

<link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/LeaVerou/awesomplete/gh-pages/awesomplete.css">


</head>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo($dashBoardMenuAccess == true)?'../dashboard/index.php':'#'; ?>">
        <a href="<?php echo($dashBoardMenuAcceess == true)?'../dashboard/index.php':'#'; ?>">
      <!-- mini logo for sidebar mini 50x50 pixels -->
     <span class="logo"><b>eLabMessenger</b> </span>
      <!-- logo for regular state and mobile devices -->
   <!--   <span class="">eLabMessenger</span> -->
    </a>
    <!-- Logo -->
    <!-- <a href="<?php echo($dashBoardMenuAccess == true)?'../dashboard/index.php':'#'; ?>" class="logo">
      <span class="logo-mini"><b>eLabMessenger</b></span>
    </a> -->
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          
          

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle hidden-xs" data-toggle="dropdown">
              <span class="material-icons mdc-button__icon user-image" aria-hidden="true">account_circle</span>
              <span class=""><?php if(isset($_SESSION['userName'])){ echo $_SESSION['userName']; } ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Footer-->
              <li class="user-footer">
                  <a href="../logout.php" style="color:red" class="">Sign out</a>
              </li>
            <!--  <li class="user-footer">
                  <a href="../profile/users.php" style="color:green" class="">Reset password</a>
              </li> -->
			  <li class="user-footer">
                  <a href="http://issueslog.koachie.org/issue" target ="_blank" style="color:blue" class="">Having Issues? Report Here !!!</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    <?php
    
    if($_SESSION['roleId']==3){

    }else if($_SESSION['roleId']==1){
      $tdt = DATE('Y-m-d');
      //Get today tests
      $tdresults =  $db->rawQuery("select COUNT(vl_sample_id) as total FROM vl_request_form WHERE DATE(sample_tested_datetime)='$tdt'");
      foreach($tdresults as $t){
        echo "<a style='float:right;width: 50px;height: 50px;line-height: 1.33;border-radius: 25px;' href='#' class='btn btn-info btn-lg blinking'>
                  <span class='glyphicon glyphicon-bell' style='mergin-right:90px;'>".$t['total']."</span>
                </a>";
    }
  }else{
    $siteid = $_SESSION['siteID'];
      $tdt = DATE('Y-m-d');
      //Get today tests
      $tdresults =  $db->rawQuery("select COUNT(vl_sample_id) as total FROM vl_request_form WHERE DATE(sample_tested_datetime)='$tdt' AND facility_id='$siteid'");
      foreach($tdresults as $t){
        echo "<a style='float:right;width: 50px;height: 50px;line-height: 1.33;border-radius: 25px;' href='#' class='btn btn-info btn-lg blinking'>
                  <span class='glyphicon glyphicon-bell' style='mergin-right:90px;'>".$t['total']."</span>
                </a>";
      }
    }
     ?>




  </header>

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <!-- Sidebar user panel -->
      <?php
        if(isset($global['logo']) && trim($global['logo'])!="" && file_exists('uploads'. DIRECTORY_SEPARATOR . "logo" . DIRECTORY_SEPARATOR . $global['logo'])){
        ?>
      <div class="user-panel">
        <div align="center">
          <img src="../uploads/logo/<?php echo $global['logo']; ?>"  alt="Logo Image" style="max-width:120px;" >
        </div>

      </div>
      <?php } ?>
      <ul class="sidebar-menu">
	<?php
	if($dashBoardMenuAccess == true){ ?>
	    <li class="allMenu dashboardMenu active">
	      <a href="../dashboard/index.php">
		<i class="fa fa-dashboard"></i> <span>Dashboard</span>
	      </a>
	    </li>
	<?php }
	if($dashBoardMenuAcceess == true){ ?>
	    <li class="allMenu dashboardMenu active">
	      <a href="../dashboard/index.php">
		<i class="fa fa-dashboard"></i> <span>Dashboard</span>
	      </a>
	    </li>
	<?php }
	if($allAdminMenuAccess == true){ ?>
	    <li class="treeview manage">
	      <a href="#">
          <i class="fa fa-gears"></i>
          <span>Configurations</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
	      </a>
	      <ul class="treeview-menu">
          <?php if(isset($_SESSION['privileges']) && in_array("roles.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu roleMenu">
              <a href="../roles/roles.php"><i class="fa fa-circle-o"></i> Roles</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("users.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu userMenu">
              <a href="../users/users.php"><i class="fa fa-circle-o"></i> Users</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("physicians.php", $_SESSION['privileges'])){ ?>
              <li class="allMenu userMenu">
                  <a href="../physicians/physicians.php"><i class="fa fa-circle-o"></i> Clinicians</a>
              </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("sites.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu facilityMenu">
              <a href="../sites/sites.php"><i class="fa fa-circle-o"></i> Sites</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("globalConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu globalConfigMenu">
              <a href="../global-config/globalConfig.php"><i class="fa fa-circle-o"></i> General Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("importConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu importConfigMenu">
              <a href="../import-configs/importConfig.php"><i class="fa fa-circle-o"></i> Import Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("testRequestEmailConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu requestEmailConfigMenu">
              <a href="../request-mail/testRequestEmailConfig.php"><i class="fa fa-circle-o"></i>Request Email/SMS <br>Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("testResultEmailConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu resultEmailConfigMenu">
              <a href="../result-mail/testResultEmailConfig.php"><i class="fa fa-circle-o"></i>Result Email/SMS <br>Configuration</a>
            </li>
          <?php } ?>
	      </ul>
	    </li>
	<?php }
        if($requestMenuAccess == true){
        ?>
        <li class="treeview request" style="<?php echo $hideRequest;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>VL Requests</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php
               if(isset($_SESSION['privileges']) && in_array("vlRequest.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu vlRequestMenu">
                    <a href="../vl-request/vlRequest.php"><i class="fa fa-circle-o"></i> View Test Requests</a>
                  </li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("addVlRequest.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu addVlRequestMenu">
                    <a href="../vl-request/addVlRequest.php"><i class="fa fa-circle-o"></i> Add New Request</a>
                  </li>

              <?php }  if(isset($_SESSION['privileges']) && in_array("batchcode.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu batchCodeMenu">
                    <a href="../batch/batchcode.php"><i class="fa fa-circle-o"></i> Manage Batch</a>
                  </li>
              <?php } if(isset($_SESSION['privileges']) && in_array("vlRequestMail.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu vlRequestMailMenu">
                    <a href="../mail/vlRequestMail.php"><i class="fa fa-circle-o"></i> E-mail Test Request</a>
                  </li>
              <?php } if(isset($_SESSION['privileges']) && in_array("addImportTestResult.php", $_SESSION['privileges'])){ ?>
                  <!--<li class="allMenu importTestResultMenu">
                    <a href="../vl-request/addImportTestResult.php"><i class="fa fa-circle-o"></i> Import Test Result</a>
                  </li>-->
              <?php } ?>
            </ul>
        </li>


        <?php }
           if($requestMenuAcceess == true){// NEW $requestMenuAcceess
        ?>
        <li class="treeview request" style="<?php echo $hideRequest;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>Requests</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                  <li class="allMenu vlRequestMenu">
                    <a href="../vl-request/vlRequeest.php"><i class="fa fa-circle-o"></i> View Test Requests</a>
                  </li>
                  <li class="allMenu addVlRequestMenu">
                    <a href="../vl-request/addVlRequest.php"><i class="fa fa-circle-o"></i> Add New Request</a>
                  </li>
                  <li class="allMenu batchCodeMenu">
                    <a href="../batch/batchcode.php"><i class="fa fa-circle-o"></i> Manage Batch</a>
                  </li>
                  <li class="allMenu vlRequestMailMenu">
                    <a href="../mail/vlRequestMail.php"><i class="fa fa-circle-o"></i> E-mail Test Request</a>
                  </li>

            </ul>
        </li>
        <?php }


        if($testResultMenuAccess == true){
        ?>
        <li class="treeview test" style="<?php echo $hideResult;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>VL Results</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php if(isset($_SESSION['privileges']) && in_array("addImportResult.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu importResultMenu"><a href="../import-result/addImportResult.php"><i class="fa fa-circle-o"></i> Import Result</a></li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("vlTestResult.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlTestResultMenu"><a href="../vl-print/vlTestResult.php"><i class="fa fa-circle-o"></i> Enter Result</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("vlResultApproval.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlResultApprovalMenu"><a href="../vl-print/vlResultApproval.php"><i class="fa fa-circle-o"></i> Approve Results</a></li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("vlResultMail.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlResultMailMenu"><a href="../mail/vlResultMail.php"><i class="fa fa-circle-o"></i> E-mail Test Result</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("addImportTestRequest.php", $_SESSION['privileges'])){ ?>
                <!--<li class="allMenu importTestRequestMenu"><a href="../import-result/addImportTestRequest.php"><i class="fa fa-circle-o"></i> Import Test Request</a></li>-->
              <?php }?>
            </ul>
        </li>
        <?php }
          if($testResultMenuAcceess == true){//NEW $testResultMenuAcceess
        ?>
        <li class="treeview test" style="<?php echo $hideResult;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>Results</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="allMenu importResultMenu"><a href="../import-result/addImportResult.php"><i class="fa fa-circle-o"></i> Import Result</a></li>
                <li class="allMenu vlTestResultMenu"><a href="../vl-print/vlTestResult.php"><i class="fa fa-circle-o"></i> Enter Result</a></li>
                <li class="allMenu vlResultApprovalMenu"><a href="../vl-print/vlResultApproval.php"><i class="fa fa-circle-o"></i> Approve Results</a></li>
                <li class="allMenu vlResultMailMenu"><a href="../mail/vlResultMail.php"><i class="fa fa-circle-o"></i> E-mail Test Result</a></li>
            </ul>
        </li>
        <?php }
    if($idsrMenuAccess == true){
        ?>
        <li class="treeview test" style="<?php echo $hideResult;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>IDSR</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <?php if(isset($_SESSION['privileges']) && in_array("genericIDSR.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu importResultMenu"><a href="../idsr/genericIDSR.php"><i class="fa fa-circle-o"></i> View IDSR Request</a></li>
                <?php } if(isset($_SESSION['privileges']) && in_array("addGenericIDSR.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu importResultMenu"><a href="../idsr/addGenericIDSR.php"><i class="fa fa-circle-o"></i> Add IDSR Request</a></li>
                <?php }  if(isset($_SESSION['privileges']) && in_array("editGenericIDSR.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlTestResultMenu"><a href="../idsr/editGenericIDSR.php"><i class="fa fa-circle-o"></i> Edit IDSR Request</a></li>
                    <!--<li class="allMenu importTestRequestMenu"><a href="../import-result/addImportTestRequest.php"><i class="fa fa-circle-o"></i> Import Test Request</a></li>-->
                <?php }?>
            </ul>
        </li>
    <?php }
        if($idsrMenuAcceess == true){ //NEW $idsrMenuAccess
        ?>
        <li class="treeview test" style="<?php echo $hideResult;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>IDSR</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                    <li class="allMenu importResultMenu"><a href="../idsr/genericIDSR.php"><i class="fa fa-circle-o"></i> View IDSR Request</a></li>
                    <li class="allMenu importResultMenu"><a href="../idsr/addGenericIDSR.php"><i class="fa fa-circle-o"></i> Add IDSR Request</a></li>
                    <li class="allMenu vlTestResultMenu"><a href="../idsr/editGenericIDSR.php"><i class="fa fa-circle-o"></i> Edit IDSR Request</a></li>
            </ul>
        </li>
    <?php }
        if($managementMenuAccess == true){
        ?>
            <li class="treeview program">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>VL Reports</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if(isset($_SESSION['privileges']) && in_array("missingResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu missingResultMenu"><a href="../program-management/missingResult.php"><i class="fa fa-circle-o"></i> Specimen Status Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlControlReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlControlReport"><a href="../program-management/vlControlReport.php"><i class="fa fa-circle-o"></i> Control Report</a></li>
                      <?php } ?>
                    <!--<li><a href="#"><i class="fa fa-circle-o"></i> TOT Report</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> VL Suppression Report</a></li>-->
                    <?php if(isset($_SESSION['privileges']) && in_array("vlResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlResultMenu"><a href="../program-management/vlResult.php"><i class="fa fa-circle-o"></i> Export Results</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlPrintResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlPrintResultMenu"><a href="../vl-print/vlPrintResult.php"><i class="fa fa-circle-o"></i> Print Result</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("highViralLoad.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlHighMenu"><a href="../program-management/highViralLoad.php"><i class="fa fa-circle-o"></i> Periodic Reports</a></li>
                    <?php }  if(isset($_SESSION['privileges']) && in_array("patientList.php", $_SESSION['privileges'])){ ?>
                    <!--<li class="allMenu patientList"><a href="patientList.php"><i class="fa fa-circle-o"></i> Export Patient List</a></li>-->
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlWeeklyReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlWeeklyReport"><a href="../program-management/vlWeeklyReport.php"><i class="fa fa-circle-o"></i> VL Lab Weekly Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("sampleRejectionReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu sampleRejectionReport"><a href="../program-management/sampleRejectionReport.php"><i class="fa fa-circle-o"></i> Specimen Rejection Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlMonitoringReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlMonitoringReport"><a href="../program-management/vlMonitoringReport.php"><i class="fa fa-circle-o"></i> Specimen Monitoring Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlShipmentReport.php", $_SESSION['privileges'])) { ?>
                    <li class="allMenu vlShipmentReport"><a href="../program-management/vlShipmentReport.php"><i class="fa fa-circle-o"></i> Shipment Status</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlPivotTable.php", $_SESSION['privileges'])) { ?>
                    <li class="allMenu vlPivotTable"><a href="../vl-print/vlPivotTable.php"><i class="fa fa-circle-o"></i> Test Results Summary</a></li>
			
                    <?php } ?>
			
                </ul>
            </li>
        <?php
        }?>
        <?php
        if($managementMenuAcceess == true){ //NEW $managementMenuAcceess
        ?>
            <li class="treeview program">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Reports</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="allMenu missingResultMenu"><a href="../program-management/missingResult.php"><i class="fa fa-circle-o"></i> Specimen Status Report</a></li>
                    <li class="allMenu vlControlReport"><a href="../program-management/vlControlReport.php"><i class="fa fa-circle-o"></i> Control Report</a></li>
                    <li class="allMenu vlResultMenu"><a href="../program-management/vlResult.php"><i class="fa fa-circle-o"></i> Export Results</a></li>
                    <li class="allMenu vlPrintResultMenu"><a href="../vl-print/vlPrintResult.php"><i class="fa fa-circle-o"></i> Print Result</a></li>
                    <li class="allMenu vlHighMenu"><a href="../program-management/highViralLoad.php"><i class="fa fa-circle-o"></i> Periodic Reports</a></li>
                    <li class="allMenu vlWeeklyReport"><a href="../program-management/vlWeeklyReport.php"><i class="fa fa-circle-o"></i> VL Lab Weekly Report</a></li>
                    <li class="allMenu sampleRejectionReport"><a href="../program-management/sampleRejectionReport.php"><i class="fa fa-circle-o"></i> Specimen Rejection Report</a></li>
                    <li class="allMenu vlMonitoringReport"><a href="../program-management/vlMonitoringReport.php"><i class="fa fa-circle-o"></i> Specimen Monitoring Report</a></li>
                    <li class="allMenu vlShipmentReport"><a href="../program-management/vlShipmentReport.php"><i class="fa fa-circle-o"></i> Shipment Status</a></li>
                    <li class="allMenu vlPivotTableMenu"><a href="../vl-print/vlPivotTable.php"><i class="fa fa-circle-o"></i> Test Results Summary</a></li>


                </ul>
            </li>
<?php
        }?>
        <?php

        if(isset($global['enable_qr_mechanism']) && trim($global['enable_qr_mechanism']) == 'yes'){ ?>
          <li class="treeview qr">
            <a href="#">
                <i class="fa fa-qrcode"></i>
                <span>QR Code</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php if(isset($_SESSION['privileges']) && in_array("generate.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu generateQRCode"><a href="../qr-code/generate.php"><i class="fa fa-circle-o"></i> Generate QR Code</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("readQRCode.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu readQRCode"><a href="../qr-code/readQRCode.php"><i class="fa fa-circle-o"></i> Read QR Code</a></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
        <!---->
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <!-- content-wrapper -->
    <div id="dDiv" class="dialog">
        <div style="text-align:center"><span onclick="closeModal();" style="float:right;clear:both;" class="closeModal"></span></div>
        <iframe id="dFrame" src="" style="border:none;" scrolling="yes" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0">some problem</iframe>
    </div>
