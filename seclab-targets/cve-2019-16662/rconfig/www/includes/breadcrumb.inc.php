<div id="breadcrumb">
    <h2>
        <?php
        // set pageType
        $pageType = 'breadcrumb';
        // get data from functions.inc.php, which is a query to the menupages table in the DB
        echo pageTitles($config_page, $pageType);
        ?>
    </h2>
</div>
