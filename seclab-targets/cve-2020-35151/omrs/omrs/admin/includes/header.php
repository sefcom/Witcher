 <div class="am-header">
      <div class="am-header-left">
        <a id="naviconLeft" href="" class="am-navicon d-none d-lg-flex"><i class="icon ion-navicon-round"></i></a>
        <a id="naviconLeftMobile" href="" class="am-navicon d-lg-none"><i class="icon ion-navicon-round"></i></a>
        <a href="index.html" class="am-logo">Online Marriage Registration System</a>
      </div><!-- am-header-left -->

      <div class="am-header-right">
        <div class="dropdown dropdown-notification">
          <a href="" class="nav-link pd-x-7 pos-relative" data-toggle="dropdown">
            <i class="icon ion-ios-bell-outline tx-24"></i>
            <!-- start: if statement -->
            <span class="square-8 bg-danger pos-absolute t-15 r-0 rounded-circle"></span>
            <!-- end: if statement -->
          </a>
          <div class="dropdown-menu wd-300 pd-0-force">
            <div class="dropdown-menu-header">
               <?php 
                        $sql ="SELECT * from  tblregistration where Status is null ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$totneworder=$query->rowCount();
?>
              <label>Notifications</label>
              <a href="new-marriage-application.php"><?php echo htmlentities($totneworder);?></a>
            </div><!-- d-flex -->

            <div class="media-list">
              <!-- loop starts here -->
              <a href="new-marriage-application.php" class="media-list-link read">
                <?php
foreach($results as $row)
{ 

  ?>
                             
                <div class="media pd-x-20 pd-y-15">
                  <img src="images/images.png" class="wd-40 rounded-circle" alt="">
                  <div class="media-body">
                    <p class="tx-13 mg-b-0"><strong class="tx-medium">New Application: <?php echo $row->RegistrationNumber;?></strong><label style="color: green;padding-left: 10px"> <?php echo $row->HusbandName;?></label></p>
                    <span class="tx-12"><?php echo $row->RegDate;?></span>
                  </div>
                </div><!-- media -->
                 <?php  } ?>
              </a>
              <!-- loop ends here -->
             
           
             
              <div class="media-list-footer">
                <a href="all-marriage-application.php" class="tx-12"><i class="fa fa-angle-down mg-r-5"></i> Show All Marriage Application</a>
              </div>
            </div><!-- media-list -->
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->
        <div class="dropdown dropdown-profile">
          <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
            <img src="../img/img3.jpg" class="wd-32 rounded-circle" alt="">
            <?php
$aid=$_SESSION['omrsaid'];
$sql="SELECT AdminName,Email from  tbladmin where ID=:aid";
$query = $dbh -> prepare($sql);
$query->bindParam(':aid',$aid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
            <span class="logged-name"><span class="hidden-xs-down"><?php  echo $row->AdminName;?></span> <i class="fa fa-angle-down mg-l-3"></i></span><?php $cnt=$cnt+1;}} ?>
          </a>
          <div class="dropdown-menu wd-200">
            <ul class="list-unstyled user-profile-nav">
              <li><a href="admin-profile.php"><i class="icon ion-ios-person-outline"></i> Edit Profile</a></li>
              <li><a href="change-password.php"><i class="icon ion-ios-gear-outline"></i> Settings</a></li>
              <li><a href="logout.php"><i class="icon ion-power"></i> Sign Out</a></li>
            </ul>
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->
      </div><!-- am-header-right -->
    </div><!-- am-header -->