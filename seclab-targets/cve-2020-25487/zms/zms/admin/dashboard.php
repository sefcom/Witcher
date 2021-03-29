<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['zmsaid']==0)) {
  header('location:logout.php');
  } else{
  ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Zoo Management System || Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <?php include_once('includes/sidebar.php');?>
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
     
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
           <?php include_once('includes/header.php');?>
            <!-- header area end -->
            <!-- page title area start -->
             <?php include_once('includes/pagetitle.php');?>
            <!-- page title area end -->
            <div class="main-content-inner">
                <!-- sales report area start -->
                <div class="sales-report-area sales-style-two">
                    <div class="row">
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//todays indian adult visitors
 $query=mysqli_query($con,"select sum(NoAdult) as totaladult from tblticindian where date(PostingDate)=CURDATE()");
$result=mysqli_fetch_array($query);
$count_today_visitors=$result['totaladult'];
 ?>  
                                        <h3 class="header-title mb-0">Today Normal Adult Visitor</h3>
                                       <p style="font-size: 20px;color: red"><?php echo $count_today_visitors?></p>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//todays indian children visitors
 $query=mysqli_query($con,"select sum(NoChildren) as totalchild from tblticindian where date(PostingDate)=CURDATE()");
$result=mysqli_fetch_array($query);
$count_today_cvisitors=$result['totalchild'];
 ?>  
                                        <h4 class="header-title mb-0">Today Normal Children Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_today_cvisitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6  mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//Yesterday indian adult visitors
 $query=mysqli_query($con,"select sum(NoAdult) as totaladulty from tblticindian where date(PostingDate)=CURDATE()-1");
$result=mysqli_fetch_array($query);
$count_Yest_visitors=$result['totaladulty'];
 ?>
                                        <h4 class="header-title mb-0">Yesterday Normal Adult Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_Yest_visitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//Yesterday indian children visitors
 $query=mysqli_query($con,"select sum(NoChildren) as totalchildy from tblticindian where date(PostingDate)=CURDATE()-1");
$result=mysqli_fetch_array($query);
$count_Yest_cvisitors=$result['totalchildy'];
 ?>
                                        <h4 class="header-title mb-0">Yesterday Normal Child Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_Yest_cvisitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="sales-report-area sales-style-two">
                    <div class="row">
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//todays Foreigner adult visitors
 $query=mysqli_query($con,"select sum(NoAdult) as totaladult from tblticforeigner where date(PostingDate)=CURDATE()");
$result=mysqli_fetch_array($query);
$count_today_visitors=$result['totaladult'];
 ?>  
                                        <h3 class="header-title mb-0">Today Foreigner Adult Visitor</h3>
                                       <p style="font-size: 20px;color: red"><?php echo $count_today_visitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//todays Foreigner children visitors
 $query=mysqli_query($con,"select sum(NoChildren) as totalchild from tblticforeigner where date(PostingDate)=CURDATE()");
$result=mysqli_fetch_array($query);
$count_today_cvisitors=$result['totalchild'];
 ?>  
                                        <h4 class="header-title mb-0">Today Foreigner Children Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_today_cvisitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6  mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//Yesterday Foreigner adult visitors
 $query=mysqli_query($con,"select sum(NoAdult) as totaladulty from tblticforeigner where date(PostingDate)=CURDATE()-1");
$result=mysqli_fetch_array($query);
$count_Yest_visitors=$result['totaladulty'];
 ?>
                                        <h4 class="header-title mb-0">Yesterday Foreigner Adult Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_Yest_visitors?></p>
                                    </div>
                                </div>
            
                            </div>
                        </div>
                        <div class="col-xl-3 col-ml-3 col-md-6 mt-5">
                            <div class="single-report">
                                <div class="s-sale-inner pt--30 mb-3">
                                    <div class="s-report-title d-flex justify-content-between">
                                        <?php
//Yesterday Foreigner children visitors
 $query=mysqli_query($con,"select sum(NoChildren) as totalchildy from tblticforeigner where date(PostingDate)=CURDATE()-1");
$result=mysqli_fetch_array($query);
$count_Yest_cvisitors=$result['totalchildy'];
 ?>
                                        <h4 class="header-title mb-0">Yesterday Foreigner Child Visitor</h4>
                                        <p style="font-size: 20px;color: red"><?php echo $count_Yest_cvisitors?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- sales report area end -->
             
                
            </div>

        </div>
        <!-- main content area end -->
        <!-- footer area start-->
       <?php include_once('includes/footer.php');?>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- offset area start -->
 
    <!-- offset area end -->
    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>

    <!-- start chart js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <!-- start highcharts js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- start zingchart js -->
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script>
    zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
    </script>
    <!-- all line chart activation -->
    <script src="assets/js/line-chart.js"></script>
    <!-- all bar chart activation -->
    <script src="assets/js/bar-chart.js"></script>
    <!-- all pie chart -->
    <script src="assets/js/pie-chart.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>
<?php }  ?>