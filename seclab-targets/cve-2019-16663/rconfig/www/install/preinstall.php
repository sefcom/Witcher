<?php
require('includes/head.php');
?>	
<div id="content">
    <h2>Pre-Installation Check</h2>
    <p>
        If any of the following tests fail, you need to resolve them before installing rConfig or the installation may fail
    </p>
    <h3>Software Version Check</h3>
    <hr/>
    <div id="section">
        <div id="notes">
            <p>
                This section checks your server for recommended software versions compatible with this release of rConfig	<br />

            </p>
        </div>
        <div id="items">
            <div class="cell">
                <ul>
                    <li></li>
                    <li>PHP Version >= 5.3</li>
                    <li>MySQL Version >= 5.1</li>
                    <li>Apache Version >= 2.2</li>
                </ul>
            </div>	
            <div class="cellinside">
                <ul>
                    <li><div id="phpVersion"></div></li>
                    <li><div id="mysqlVersion"></div></li>
                    <li><div id="httpdVersion"></div></li>
                </ul>
            </div>						
        </div>		
    </div>
    <div class="clear"></div>

</div>
<!-- JS script Include -->
<script type="text/JavaScript" src="js/preinstall.js"></script>
<div id="footer">
    <div id="footerNav">
        <div id="last" class="disabled"><a href="#"><< Last</a></div>
        <div id="next"><a href="license.php">Next >></a></div>
    </div>
</div>
</div>
</body>
</html>