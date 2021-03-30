<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['omrsuid']==0)) {
  header('location:logout.php');
  } else{

if(isset($_POST['submit']))
  {
    $uid=$_SESSION['omrsuid'];
$regnumber=mt_rand(100000000, 999999999);
$dom=$_POST['dom'];
$nofhusband=$_POST['nofhusband'];
$hreligion=$_POST['hreligion'];
$hdob=$_POST['hdob'];
$hsbmarriage=$_POST['hsbmarriage'];
$haddress=$_POST['haddress'];
$hzipcode=$_POST['hzipcode'];
$hstate=$_POST['hstate'];
$hadharno=$_POST['hadharno'];
$nofwife=$_POST['nofwife'];
$wreligion=$_POST['wreligion'];
$wdob=$_POST['wdob'];
$wsbmarriage=$_POST['wsbmarriage'];
$waddress=$_POST['waddress'];
$wzipcode=$_POST['wzipcode'];
$wstate=$_POST['wstate'];
$wadharno=$_POST['wadharno'];
$witnessnamef=$_POST['witnessnamef'];
$waddressfirst=$_POST['waddressfirst'];
$witnessnames=$_POST['witnessnames'];
$waddresssec=$_POST['waddresssec'];
$witnessnamet=$_POST['witnessnamet'];
$waddressthird=$_POST['waddressthird'];
//husband image
$himg=$_FILES["husimage"]["name"];
$extension1 = substr($himg,strlen($himg)-4,strlen($himg));
//wife image
$wimg=$_FILES["wifeimage"]["name"];
$extension2 = substr($wimg,strlen($wimg)-4,strlen($wimg));
// allowed extensions
$allowed_extensions = array(".jpg","jpeg",".png",".gif");
// Validation for allowed extensions .in_array() function searches an array for a specific value.
if(!in_array($extension1,$allowed_extensions))
{
echo "<script>alert('Husband image has Invalid format. Only jpg / jpeg/ png /gif format allowed');</script>";
}
if(!in_array($extension2,$allowed_extensions))
{
echo "<script>alert('Wife image has Invalid format. Only jpg / jpeg/ png /gif format allowed');</script>";
}
else
{
//rename images
$husimg=md5($himg).time().$extension1;
$wifeimg=md5($wimg).time().$extension2;
move_uploaded_file($_FILES["husimage"]["tmp_name"],"images/".$husimg);
     move_uploaded_file($_FILES["wifeimage"]["tmp_name"],"images/".$wifeimg);
$ret="select HusbandAdharno,WifeAdharNo from tblregistration where HusbandAdharno=:hadharno || WifeAdharNo=:wadharno";
 $query= $dbh -> prepare($ret);
$query->bindParam(':hadharno',$hadharno,PDO::PARAM_STR);
$query->bindParam(':wadharno',$wadharno,PDO::PARAM_STR);

$query-> execute();
     $results = $query -> fetchAll(PDO::FETCH_OBJ);
     if($query -> rowCount() == 0)
{


$sql="insert into tblregistration(RegistrationNumber,UserID,DateofMarriage,HusbandName,HusImage,HusbandReligion,Husbanddob,HusbandSBM,HusbandAdd,HusbandZipcode,HusbandState,HusbandAdharno,WifeName,WifeImage,WifeReligion,Wifedob,WifeSBM,WifeAdd,WifeZipcode,WifeState,WifeAdharNo,WitnessNamefirst,WitnessAddressFirst,WitnessNamesec,WitnessAddresssec,WitnessNamethird,WitnessAddressthird)values(:regnumber,:uid,:dom,:nofhusband,:husimg,:hreligion,:hdob,:hsbmarriage,:haddress,:hzipcode,:hstate,:hadharno,:nofwife,:wifeimg,:wreligion,:wdob,:wsbmarriage,:waddress,:wzipcode,:wstate,:wadharno,:witnessnamef,:waddressfirst,:witnessnames,:waddresssec,:witnessnamet,:waddressthird)";
$query=$dbh->prepare($sql);
$query->bindParam(':regnumber',$regnumber,PDO::PARAM_STR);
$query->bindParam(':uid',$uid,PDO::PARAM_STR);
$query->bindParam(':dom',$dom,PDO::PARAM_STR);
$query->bindParam(':nofhusband',$nofhusband,PDO::PARAM_STR);
$query->bindParam(':husimg',$husimg,PDO::PARAM_STR);
$query->bindParam(':hreligion',$hreligion,PDO::PARAM_STR);
$query->bindParam(':hdob',$hdob,PDO::PARAM_STR);
$query->bindParam(':hsbmarriage',$hsbmarriage,PDO::PARAM_STR);
$query->bindParam(':haddress',$haddress,PDO::PARAM_STR);
$query->bindParam(':hzipcode',$hzipcode,PDO::PARAM_STR);
$query->bindParam(':hstate',$hstate,PDO::PARAM_STR);
$query->bindParam(':hadharno',$hadharno,PDO::PARAM_STR);
$query->bindParam(':nofwife',$nofwife,PDO::PARAM_STR);
$query->bindParam(':wifeimg',$wifeimg,PDO::PARAM_STR);
$query->bindParam(':wreligion',$wreligion,PDO::PARAM_STR);
$query->bindParam(':wdob',$wdob,PDO::PARAM_STR);
$query->bindParam(':wsbmarriage',$wsbmarriage,PDO::PARAM_STR);
$query->bindParam(':waddress',$waddress,PDO::PARAM_STR);
$query->bindParam(':wzipcode',$wzipcode,PDO::PARAM_STR);
$query->bindParam(':wstate',$wstate,PDO::PARAM_STR);
$query->bindParam(':wadharno',$wadharno,PDO::PARAM_STR);
$query->bindParam(':witnessnamef',$witnessnamef,PDO::PARAM_STR);
$query->bindParam(':waddressfirst',$waddressfirst,PDO::PARAM_STR);
$query->bindParam(':witnessnames',$witnessnames,PDO::PARAM_STR);
$query->bindParam(':waddresssec',$waddresssec,PDO::PARAM_STR);
$query->bindParam(':witnessnamet',$witnessnamet,PDO::PARAM_STR);
$query->bindParam(':waddressthird',$waddressthird,PDO::PARAM_STR);
 $query->execute();

   $LastInsertId=$dbh->lastInsertId();
   if ($LastInsertId>0) {

echo '<script>alert("Registration form has been filled successfully.")</script>';
  }
  else
    {
         echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

  

}

else
{

echo "<script>alert('Aadhar Number is  already exist. Please try again');</script>";
  
}
}
}
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
   

    <title>Online Marriage Registration System !! Form</title>

    <!-- vendor css -->
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="lib/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="lib/jquery-toggles/toggles-full.css" rel="stylesheet">
    <link href="lib/highlightjs/github.css" rel="stylesheet">
    <link href="lib/select2/css/select2.min.css" rel="stylesheet">
    <link href="lib/spectrum/spectrum.css" rel="stylesheet">
    <!-- Amanda CSS -->
    <link rel="stylesheet" href="css/amanda.css">
  </head>

  <body>
 <?php include_once('includes/header.php');
include_once('includes/sidebar.php');

 ?>

 

    <div class="am-pagetitle">
      <h5 class="am-title">Registration Form</h5>

    </div><!-- am-pagetitle -->

    <div class="am-mainpanel">
      <div class="am-pagebody">

      

        <div class="row row-sm mg-t-20">
          <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40 form-layout form-layout-4">
              <h3>Registration Form</h3>
               <form id="basic-form" method="post" enctype="multipart/form-data">
      
          

 <div class="row">
                <label class="col-sm-4 form-control-label">Date of Marriage: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <input type="text" class="form-control fc-datepicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="dom">
                </div>
              </div>


          <!-- wd-200 -->
              <h3  class="card-body-title" style="padding-top: 20px;color: red">1 Husband Details</h3>
              <hr />
              <div class="row">
                <label class="col-sm-4 form-control-label">Name of Husband: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="nofhusband" value="" class="form-control" required='true'>
                </div>
              </div>
             <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Photo: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="file" name="husimage" value="" class="form-control" required='true'>
                </div>
              </div><!-- row -->
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Religion: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="hreligion" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Date of Birth: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" class="form-control fc-datepicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" id="hdob" name="hdob">
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Marital Status Before Marriage: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <select type="text" name="hsbmarriage" value="" class="form-control" required='true'>
                    <option value="">Select Status</option>
                    <option value="Bachelor">Bachelor</option>
                    <option value="Married">Married</option>
                    <option value="Divorsee">Divorsee</option>
                    <option value="Widower">Widower</option>
                  </select>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <textarea type="file" name="haddress" value="" required="true" class="form-control"></textarea>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Zipcode: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="hzipcode" value=""  class="form-control" required='true' maxlength="6">
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">State: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="hstate" value=""  class="form-control" required='true'>
                </div>
              </div>
               <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Enter Adahar Number: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <input type="text" name="hadharno" value="" required="true" class="form-control" maxlength="12">
                </div>
              </div>
               <h3  class="card-body-title" style="padding-top: 20px;color: red">2 Wife Details</h3>
              <hr />
               <div class="row">
                <label class="col-sm-4 form-control-label">Name of Wife: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="nofwife" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Photo: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="file" name="wifeimage" value="" class="form-control" required='true'>
                </div>
              </div><!-- row -->
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Religion: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="wreligion" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Date of Birth: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <input type="text" class="form-control fc-datepicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" id="wdob" name="wdob">
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Marital Status Before Marriage: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <select type="text" name="wsbmarriage" value="" class="form-control" required='true'>
                    <option value="">Select Status</option>
                    <option value="Bachelor">Bachelor</option>
                    <option value="Married">Married</option>
                    <option value="Divorsee">Divorsee</option>
                    <option value="Widower">Widower</option>
                  </select>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <textarea type="text" name="waddress" value="" required="true" class="form-control"></textarea>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Zipcode: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="wzipcode" value=""  class="form-control" required='true' maxlength="6">
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">State: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="wstate" value=""  class="form-control" required='true'>
                </div>
              </div>
               <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Enter Adahar Number: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <input type="text" name="wadharno" value="" required="true" class="form-control" maxlength="12">
                </div>
              </div>
              <h3  class="card-body-title" style="padding-top: 20px;color: red">3 Witness Details</h3>
              <hr />
               <div class="row">
                <label class="col-sm-4 form-control-label">Full Name of Witness: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="witnessnamef" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <textarea type="text" name="waddressfirst" value="" required="true" class="form-control"></textarea>
                </div>
              </div>
              <hr />
              <div class="row">
                <label class="col-sm-4 form-control-label">Full Name of Witness: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="witnessnames" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <textarea type="text" name="waddresssec" value="" required="true" class="form-control"></textarea>
                </div>
              </div>
              <hr />
              <div class="row">
                <label class="col-sm-4 form-control-label">Full Name of Witness: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <input type="text" name="witnessnamet" value="" class="form-control" required='true'>
                </div>
              </div>
              <div class="row mg-t-20">
                <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                 <textarea type="text" name="waddressthird" value="" required="true" class="form-control"></textarea>
                </div>
              </div>
             <div class="form-layout-footer mg-t-30">
             <p style="text-align: center;"><button class="btn btn-info mg-r-5"  name="submit" id="submit">ADD</button></p>
                </form>
              </div><!-- form-layout-footer -->
            </div><!-- card -->
          </div><!-- col-6 -->
        
        </div><!-- row -->


      </div><!-- am-pagebody -->
     <?php include_once('includes/footer.php');?>
    </div><!-- am-mainpanel -->

    <script src="lib/jquery/jquery.js"></script>
   <script src="lib/jquery-ui/jquery-ui.js"></script>
    <script src="lib/popper.js/popper.js"></script>
    <script src="lib/bootstrap/bootstrap.js"></script>
    <script src="lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
    <script src="lib/jquery-toggles/toggles.min.js"></script>
    <script src="lib/highlightjs/highlight.pack.js"></script>
    <script src="lib/select2/js/select2.min.js"></script>
        <script src="lib/spectrum/spectrum.js"></script>

    <script src="js/amanda.js"></script>
    <script>
      $(function(){
        'use strict';

        $('.select2').select2({
          minimumResultsForSearch: Infinity
        });
      })

        // Datepicker
        $('.fc-datepicker').datepicker({
          showOtherMonths: true,
          selectOtherMonths: true
        });

$('#datepickerNoOfMonths').datepicker({
  showOtherMonths: true,
  selectOtherMonths: true,
  numberOfMonths: 2
})
$('.hdob').datepicker({
  multidate: true,
  format: 'yyyy-mm-dd'
});



    </script>
    
  </body>
</html>
<?php }  ?>