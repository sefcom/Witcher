<?php
/* Includes */
require_once("../classes/db2.class.php");
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
$db2 = new db2();

/* Get Row count from configcommands where NOT deleted */
$db2->query('SELECT COUNT(*) AS total FROM configcommands WHERE status = 1');
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
        <form name ="searchForm" method="GET" action="commands.php" onsubmit="return searchValidateForm()">
            <select name="searchColumn" id="searchColumn" class="paginate">
                <option value="command">Command</option>
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
            <br/>
            <button type="submit" class="paginate">Go!</button> <?php //search logic below in this script   ?>
            <button onClick="clearSearch()" type="button" class="paginate">Clear Search</button>
            <br />
            <font size="0.3em">use '*' as a wildcard</font>
        </form>
    </div> <!-- end searchForm -->	
</div>
<div class="spacer"></div>
<?php
echo $pages->display_pages();
echo "<span class=\"\">" . $pages->display_jump_menu() . $pages->display_items_per_page() . "</span>";
echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

/* Search functionality
  1. check if hidden search input is set (i.e. submit for search for was pressed)
  2. check all inputs and return errors if needed - to be done
  3. if search fields are complete, build the query with the search string, esle default query inc pagnation for use later
 */

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
    $db2->query("SELECT id, command FROM configcommands WHERE status = 1
		AND " . $searchColumn . " " . $searchOption . " '" . $searchField . "'
		$pages->limit");
    $db2->bind(':searchColumn', $searchColumn);
    $db2->bind(':searchOption', $searchOption);
    $db2->bind(':searchField', $searchField);
    $qRes = $db2->resultset();
} else {
    /* GET all nodes records from DB */
    $db2->query("SELECT id, command FROM configcommands WHERE status = 1 $pages->limit");
    $qRes = $db2->resultset();
}

// push rows to $items array
$items = array();
foreach ($qRes as $row) {
    array_push($items, $row);
}

/* Create Multidimensional array for use later */
$result["rows"] = $items;

$i = 0; # row counter  to enable alternate row coloring
?>

<table id="commandsTbl" class="tableSimple">
    <thead>
    <th><input type="checkbox" disabled="disabled"/></th>
    <th align="left">Command</th>
    <th align="left">Category</th>
</thead>
<tbody>
    <?php
    foreach ($result['rows'] as $rows):
        $cmdId = $rows['id'];
        $command = $rows['command'];
        $catName = array(); // set catName var as Array
        $db2->query('SELECT cct.nodeCatId, cat.categoryName
                        FROM cmdCatTbl AS cct 
                        LEFT JOIN categories AS cat ON cct.nodeCatId = cat.id
                        WHERE configCmdId = :cmdId');
        $db2->bind(':cmdId', $cmdId);
        $catQ = $db2->resultset();

        // get Query results and push to catName array then impode to string
        foreach ($catQ as $row) {
            // set the array and fill it
            array_push($catName, $row['categoryName']);
        }
        //check if array is full or empty and set var
        if (empty($catName)) {
            $catList = "<em><font color='red'>No categories Selected</font></em>";
        } else {
            $catList = implode(", ", $catName);
        }

        /* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring */
        echo '<tr class="row' . ($i++ % 2) . '">';
        echo '<td align="center"><input type="checkbox" name="tablecheckbox" id="' . $cmdId . '"></td>';
        echo '<td align="left">' . $command . '</td>';
        echo '<td align="left">' . $catList . '</td>';
        echo '</tr>';
    endforeach;
    ?>

</tbody>
</table>

<?php
echo $pages->display_pages();
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";
