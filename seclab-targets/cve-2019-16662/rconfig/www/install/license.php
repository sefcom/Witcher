<?php
require('includes/head.php');
?>	
<div id="content">
    <h2>License Agreement</h2>
    <h3>rConfig License Information</h3>
    <hr/>
    <div id="section">
        <p>
            Please read the terms of your license carefully below. By continuing with the installation of rConfig, you declare that you agree to the terms of this license agreement in full
        </p>
        <div id="licenseDiv">
            <p>
                <?php
                $licence = file_get_contents("/home/rconfig/www/LICENSE.txt");
                echo nl2br($licence);
                ?>
            </p>				
        </div>		
    </div>
    <div class="spacer"></div>
    <label>Accept License:</label>
    <input type="checkbox" name="acceptLicenseChkBox" id="acceptLicenseChkBox" value="0" onclick="acceptLicense();" class="checkbox"/>
    <div class="spacer"></div>
    <div class="clear"></div>

</div>
<!-- JS script Include -->
<script type="text/JavaScript" src="js/preinstall.js"></script>
<script type="text/JavaScript" src="js/license.js"></script>
<div id="footer">
    <div id="footerNav">
        <div id="last"><a href="preinstall.php"><< Last</a></div>
        <div id="next"><a href="#" id="next_a">Next >></a></div>
    </div>
</div>	
</div>
</body>
</html>