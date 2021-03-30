<?php
/* Includes */
require_once("../classes/db2.class.php");
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
$db2 = new db2();

/* Get Row count from nodes where NOT deleted */
$db2->query('SELECT COUNT(*) AS total FROM nodes WHERE status = 1');
$row = $db2->resultsetCols();
$result["total"] = $row[0];
/* Instantiate Paginator Class */
$pages = new Paginator;
$pages->items_total = $result['total'];
$pages->mid_range = 7; // Number of pages to display. Must be odd and > 3
$pages->paginate();
?>
<!-- begin Search form -->
<div id="deviceActionDiv">
    <div id="searchForm"> 
        <legend>Search</legend>
        <form name ="searchForm" method="GET" action="devices.php" onsubmit="return searchValidateForm()">
            <select name="searchColumn" id="searchColumn" class="paginate">
                <option value="deviceName">Device Name</option>
                <option value="deviceIpAddr">IP Address</option>
            </select>
            <select name="searchOption" id="searchOption" class="paginate">
                <option value="contains" selected>Contains</option>
                <option value="notContains">Not Contains</option>
                <option value="equals">Equals</option>
            </select>
            <input type="text" id="searchField" name="searchField" placeholder="search text" class="paginate">
            <input type="hidden" id="search" value="search" name="search">
            <?php
            if (isset($errors['searchField'])) {
                echo "<span class=\"error\">" . $errors['searchField'] . "</span>";
            }
            ?>
            <button type="submit">Go!</button> <?php //search logic below in this script ?>
            <button onClick="clearSearch()" type="button">Clear Search</button>
            <br />
            <font size="0.3em">use '*' as a wildcard</font>
        </form>
    </div> <!-- end searchForm -->	

    <div id="sortForm">
        <legend>Sort</legend>
        <form method="POST" action="devices.php">
            <span class="paginate">Sort by:</span>
            <select name="sortBy" class="paginate">
                <option selected></option>
                <option name="vendorId" value="vendorId">Vendor</option>
                <option name="deviceName" value="deviceName">Device Name</option>
                <option name="deviceIpAddr" value="deviceIpAddr">IP Address</option>
            </select>
            <span class="paginate">Asc/Desc:</span>
            <select name="ascDesc" class="paginate">
                <option selected></option>
                <option name="asc" value="ASC">Ascending</option>
                <option name="desc" value="DESC">Descending</option>
            </select>
            <button type="submit">Go!</button>
        </form> 
    </div>
</div>
<?php
echo $pages->display_pages();
echo "<span class=\"\">" . $pages->display_jump_menu() . $pages->display_items_per_page() . "</span>";
echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

/* get Custom Column Names from Custom DB View TBL to complete full SELECT Query below */
$db2->query('SELECT * FROM customProperties');
$custColumns = $db2->resultsetCols();
$custProp_num_rows = $db2->rowCount();

// check if $custColumns is NOT empty and set to vars
if (!empty($custColumns)) {
    $customArray = array();
    foreach ($custColumns as $key => $value) {
        array_push($customArray, $value);
    }
    $dynQueryStr = implode(", ", $customArray) . ', ';
} else {
    $customArray = '';
    $dynQueryStr = '';
}

/* Search functionality
  1. set default query
  1. if search is set - nbuild new $query
  2. check all inputs and return errors if needed - to be done
  3. if search fields are complete, build the query with the search string, esle default query inc pagnation for use later
 */
$query = "SELECT 
		n.id,
		v.vendorName,
		v.vendorLogo,
		n.deviceName,
		c.categoryName,
		n.deviceIpAddr,
		n.deviceUsername,
		n.devicePassword,
		" . $dynQueryStr . "
		n.vendorId
		FROM nodes n
	LEFT OUTER JOIN vendors v ON n.vendorId = v.id
	LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
	WHERE n.status = 1
	ORDER BY deviceName ASC
	$pages->limit";

if (isset($_GET['search'])) {

    if (isset($_GET['searchColumn'])) {
        $searchColumn = $_GET['searchColumn'];
    }

    if (isset($_GET['searchOption'])) {
        switch ($_GET['searchOption']) {
            case "contains":
                $searchOption = "LIKE";
                break;
            case "equals":
                $searchOption = "=";
                break;
            case "notContains":
                $searchOption = "NOT LIKE";
                break;
        }
    }

    if (isset($_GET['searchField'])) {
        $searchField = $_GET['searchField'];
        $searchField = str_replace("*", "%", $searchField); // swap * for % for SQL query
        if ($searchOption == "LIKE" || $searchOption == "NOT LIKE") {
            $searchField = '%' . $searchField . '%';
        }
    }

    $query = "SELECT 
		n.id,
		v.vendorName,
		v.vendorLogo,
		n.deviceName,
		c.categoryName,
		n.deviceIpAddr,
		n.deviceUsername,
		n.devicePassword,
		" . $dynQueryStr . "
		n.vendorId
		FROM nodes n
	LEFT OUTER JOIN vendors v ON n.vendorId = v.id
	LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
	WHERE n.status = 1
	AND " . $searchColumn . " " . $searchOption . " '" . $searchField . "'
	$pages->limit";
} else { // end hidden search check 
    if (isset($_POST['sortBy'])) { // sort by query
        $column = $_POST['sortBy'];
        $ascDesc = $_POST['ascDesc'];

        $sortbyQuery = "ORDER BY " . $column . " " . $ascDesc . " ";
        $query = "SELECT 
			n.id,
			v.vendorName,
			v.vendorLogo,
			n.deviceName,
			c.categoryName,
			n.deviceIpAddr,
			n.deviceUsername,
			n.devicePassword,
			" . $dynQueryStr . "
			n.vendorId
			FROM nodes n
		LEFT OUTER JOIN vendors v ON n.vendorId = v.id
		LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
		WHERE n.status = 1
		$sortbyQuery
		$pages->limit";
    }
} // end search
/* GET all nodes records from DB */
$db2->query($query);
$qRes = $db2->resultset();

/* Create Multidimensional array for use later */
$result["rows"] = $qRes;
$result["custom"] = $customArray;
$result["cust_num_rows"] = $custProp_num_rows;

$i = 0; # row counter  to enable alternate row coloring
?>
<table id="devicesTbl" class="tableSimple">
    <thead>
    <th><input type="checkbox" disabled="disabled"/></th>
    <th align="left">Device Name</th>
    <th align="left">IP Address</th>
    <th align="left">Category</th>
    <th align="left">Vendor</th>
<?php
/* Create and add new Customer Properties Headers to Table */
if (!empty($customArray)) {
    foreach ($customArray as $customRows):
        // remove 'custom_' bit for display purposes
        $CustomHeader = substr($customRows, 7);
        ?>
            <th align="left">
            <?php echo $CustomHeader; ?>
            </th>
                <?php
            endforeach;
        } // if !empty
        ?>
</thead>
<tbody>
<?php
/* do a foreach on the $result['rows'] array to get devices key/value pairs */
foreach ($result['rows'] as $rows):
    $id = $rows['id'];

    /* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring */
    echo '<tr class="row' . ($i++ % 2) . '">';
    ?>
    <td align="center"><input type="checkbox" name="tablecheckbox" id="<?php echo $id; ?>"/></td>
    <td >
        <a href="devicemgmt.php?deviceId=<?php echo $rows['id'] ?>&device=<?php echo $rows['deviceName'] ?>" title="View <?php echo $rows['deviceName'] ?> Configurations"><?php echo $rows['deviceName'] ?>
    </td>
    <td align="left"><?php echo $rows['deviceIpAddr'] ?></td>
    <td align="left"><?php echo $rows['categoryName'] ?></td>
    <td align="left"><img src="<?php echo $rows['vendorLogo'] ?>" /> <?php echo $rows['vendorName'] ?></td>
    <?php
    /*  Block extracts key from array that partial matchs 'custom_' 
      When a match happens - output the html with the actual value $v.
      This ensures, that no matter how many custom properties, the values get printed
      for the corrcet column names.
     */
    foreach ($rows as $k => $v) {
        if (strpos($k, 'custom_') !== false) {
            echo "<td align=\"left\">" . $v . "</td>";
        } // end if
    } // end foreach
    ?>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
<?php
echo $pages->display_pages();
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";