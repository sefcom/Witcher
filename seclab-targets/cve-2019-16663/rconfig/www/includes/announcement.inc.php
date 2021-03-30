<div id="announcement">
    <p class="notification loginInformation">
        <?php
        // set pageType
        $pageType = 'announcement';
        // get data from functions.inc.php, which is a query to the menupages table in the DB
        echo pageTitles($config_page, $pageType);
        ?>
    </p>
</div>
