<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['omrsuid']==0)) {
  header('location:logout.php');
  } else{


  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
   

    <title>View Certificate</title>

    <!-- vendor css -->
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="lib/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="lib/jquery-toggles/toggles-full.css" rel="stylesheet">
    <link href="lib/highlightjs/github.css" rel="stylesheet">
    <link href="lib/select2/css/select2.min.css" rel="stylesheet">

    <!-- Amanda CSS -->
    <link rel="stylesheet" href="css/amanda.css">
    <script type="text/javascript">     
    function PrintDiv() {    
       var divToPrint = document.getElementById('divToPrint');
       var popupWin = window.open('', '_blank', 'width=300,height=300');
       popupWin.document.open();
       popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
 </script>
  </head>

  <body>
 <?php include_once('includes/header.php');
include_once('includes/sidebar.php');

 ?>

 

    <div class="am-pagetitle">
      <h5 class="am-title">Marraige Registration</h5>

    </div><!-- am-pagetitle -->

    <div class="am-mainpanel">
      <div class="am-pagebody">

      

        <div class="row row-sm mg-t-20">
          <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40 form-layout form-layout-4" id="divToPrint">
              <h2 style="text-align: center;color: red">Certificate of Marriage</h2>
              <?php
                               $vid=$_GET['viewid'];

$sql="SELECT tblregistration.*,tbluser.FirstName,tbluser.LastName,tbluser.MobileNumber,tbluser.Address from  tblregistration join  tbluser on tblregistration.UserID=tbluser.ID where tblregistration.ID=:vid";
$query = $dbh -> prepare($sql);
$query-> bindParam(':vid', $vid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>


<table class="table table-hover table-bordered mg-b-0" border="1">
              <thead class="bg-info">
                <tr>
                  <th>Certificate Number:</th>
                  <th><?php  echo $row->RegistrationNumber;?></th>
                
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Issue Date:</td>
                  <td><?php  echo $row->UpdationDate;?></td>
                </tr>
              </tbody>
            </table>

<table class="table table-hover table-bordered table-primary mg-b-0" style="margin-top:1%" border="1">
              <thead>
                <tr>
                
                  <th colspan="3">1. Husband Details</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>Name</th>
                  <td>Mr. <?php  echo $row->HusbandName;?></td>
                  <td rowspan="4" style="text-align:center;"><img src="images/<?php echo $row->HusImage;?>" width="250" height="200"><br />
Photo of Groom
                  </td>
                </tr>
<tr>
<th>Resident at:</th>  
<td><?php  echo $row->HusbandAdd;?>,<?php  echo $row->HusbandZipcode;?>,<?php  echo $row->HusbandState;?></td>
</tr>

<tr>
<th>Date of Birth:</th>
<td><?php  echo $row->Husbanddob;?></td>  
</tr>

<tr>
<th>Aadhar Number:</th>
<td><?php  echo $row->HusbandAdharno;?></td>  
</tr>
       
              </tbody>
            </table>


<table class="table table-hover table-bordered table-purple mg-b-0" style="margin-top:1%" border="1">
              <thead>
                <tr>
                  <th colspan="3">2 WIFE DETAILS</th>
                </tr>
              </thead>
              <tbody>
            
             <tr>
               <th>Name</th>
               <td>Mrs.<?php  echo $row->WifeName;?></td>
               <td rowspan="4" style="text-align:center;"><img src="images/<?php echo $row->WifeImage;?>" width="250" height="200"> <br />
               Photo of Bride</td>
             </tr>
                <tr>
               <th>Resident at:</th>
               <td> <?php  echo $row->WifeAdd;?>,<?php  echo $row->WifeZipcode;?>,<?php  echo $row->WifeState;?></td>
             </tr>
     <tr>
               <th>Date of Birth:</th>
               <td> <?php  echo $row->Wifedob;?></td>
             </tr>

  <tr>
               <th>Aadhar Number:</th>
               <td> <?php  echo $row->WifeAdharNo;?></td>
             </tr>


              </tbody>
            </table>
<p style="margin-top:1%; font-size:16px;">Having been Solemnized at XYZ(State) on <?php  echo $row->DateofMarriage;?> according to the custom praticed by parties duly witness by:</p>
<table class="table table-hover table-bordered mg-b-0" border="1" width="100%">
              <thead class="bg-danger">
                <tr>
 <th>#</th>
<th>Witness Name</th>
<th>Witness Address</th>            </tr>
              </thead>
              <tbody>
                <tr>
                  <th>1.</th>
                  <td><?php  echo $row->WitnessNamefirst;?></td>
                  <td><?php  echo $row->WitnessAddressFirst;?></td>
                </tr>
            
<tr>
                  <th>2.</th>
                  <td><?php  echo $row->WitnessNamesec;?></td>
                  <td><?php  echo $row->WitnessAddresssec;?></td>
                </tr>

                <tr>
                  <th>3.</th>
                  <td><?php  echo $row->WitnessAddressthird;?></td>
                  <td><?php  echo $row->WitnessAddressthird;?></td>
                </tr>


              </tbody>
            </table>


<p style="margin-top:1%; font-size:16px;">Has been duly registred on <?php  echo $row->UpdationDate;?> at the office of maariage officer.</p>
<p style="color#000; font-weight:bold">Name of Groom: <?php  echo $row->HusbandName;?></p>
<p style="color#000; font-weight:bold">Name of Bride: <?php  echo $row->WifeName;?></p>
   <?php }} ?>
              <form>
              <p style="text-align: center;color: blue"><input type="button" value="print" class="btn btn-primary" onclick="PrintDiv();" /></p></form>
              </div>

            </div><!-- card -->
          </div><!-- col-6 -->
        
        </div><!-- row -->


      
     <?php include_once('includes/footer.php');?>
    </div><!-- am-mainpanel -->

    <script src="lib/jquery/jquery.js"></script>
    <script src="lib/popper.js/popper.js"></script>
    <script src="lib/bootstrap/bootstrap.js"></script>
    <script src="lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
    <script src="lib/jquery-toggles/toggles.min.js"></script>
    <script src="lib/highlightjs/highlight.pack.js"></script>
    <script src="lib/select2/js/select2.min.js"></script>

    <script src="js/amanda.js"></script>
    <script>
      $(function(){
        'use strict';

        $('.select2').select2({
          minimumResultsForSearch: Infinity
        });
      })
    </script>
  </body>
</html>
<?php }  ?>