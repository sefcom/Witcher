 <div class="am-header">
      <div class="am-header-left">
        <a id="naviconLeft" href="" class="am-navicon d-none d-lg-flex"><i class="icon ion-navicon-round"></i></a>
        <a id="naviconLeftMobile" href="" class="am-navicon d-lg-none"><i class="icon ion-navicon-round"></i></a>
        <a href="dashboard.php" class="am-logo">Online Marriage Registration System</a>
      </div><!-- am-header-left -->

      <div class="am-header-right">
       
        <div class="dropdown dropdown-profile">
          <a href="user-profile.php" class="nav-link nav-link-profile" data-toggle="dropdown">
            <img src="img/images.png" class="wd-32 rounded-circle" alt="">
            <?php
$uid=$_SESSION['omrsuid'];
$sql="SELECT FirstName,MobileNumber from  tbluser where ID=:uid";
$query = $dbh -> prepare($sql);
$query->bindParam(':uid',$uid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
            <span class="logged-name"><span class="hidden-xs-down"><?php  echo $row->FirstName;?></span> <i class="fa fa-angle-down mg-l-3"></i></span><?php $cnt=$cnt+1;}} ?>
          </a>
          <div class="dropdown-menu wd-200">
            <ul class="list-unstyled user-profile-nav">
              <li><a href="user-profile.php"><i class="icon ion-ios-person-outline"></i> Edit Profile</a></li>
              <li><a href="change-password.php"><i class="icon ion-ios-gear-outline"></i> Settings</a></li>
              <li><a href="logout.php"><i class="icon ion-power"></i> Sign Out</a></li>
            </ul>
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->
      </div><!-- am-header-right -->
    </div><!-- am-header -->