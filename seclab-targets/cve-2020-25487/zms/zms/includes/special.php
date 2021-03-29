<div class="specials-section">
					<div class="container">
						<div class="specials-grids">
							
							<div class="col-md-4 specials1">
								<h3> details</h3>
								<ul>
									<li><a href="about.php">About Us</a></li>
									<li><a href="index.php">Home</a></li>
									<li><a href="contact.php">Contact</a></li>
									<li><a href="admin/index.php">Admin</a></li>
								</ul>
							</div>
							<div class="col-md-4 specials1">
								<h3>contact</h3>
								<?php 
 $query=mysqli_query($con,"select * from  tblpage where PageType='contactus'");
 while ($row=mysqli_fetch_array($query)) {


 ?>
								<address>
									<p>Email : <?php  echo $row['Email'];?></p>
								 <p>Phone : <?php  echo $row['MobileNumber'];?></p>
								 <p><?php  echo $row['PageDescription'];?></p>
								</address><?php } ?>
							</div>
							<div class="col-md-4 specials1">
								<h3>social</h3>
								<ul>
									<li><a href="#">facebook</a></li>
									<li><a href="#">twitter</a></li>
									<li><a href="#">google+</a></li>
									<li><a href="#">vimeo</a></li>
								</ul>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>