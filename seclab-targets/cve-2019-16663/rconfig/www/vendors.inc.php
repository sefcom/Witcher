<?php
/* Includes */
require_once("../classes/db2.class.php");
include_once('../classes/paginator.class.php');

/* Instantiate DB Class */
$db2 = new db2();

/* Get Row count from vendors where NOT deleted */
$db2->query('SELECT COUNT(*) AS total FROM vendors WHERE status = 1');
$row = $db2->resultsetCols();
$result["total"] = $row[0];
/* Instantiate Paginator Class */
$pages = new Paginator;
$pages->items_total = $result['total'];
$pages->mid_range = 7; // Number of pages to display. Must be odd and > 3
$pages->paginate();
echo $pages->display_pages();
echo "<span class=\"\">" . $pages->display_jump_menu() . $pages->display_items_per_page() . "</span>";
echo "<div class=\"spacer\" style=\"padding-bottom:3px;\"></div>";

/* GET all vendor records from DB */
$db2->query("SELECT  id, vendorName, vendorLogo FROM vendors WHERE status = 1 $pages->limit");
$queryResult = $db2->resultset();
// push rows to $items array
$items = array();
foreach ($queryResult as $row) {
    array_push($items, $row);
}

/* Create Multidimensional array for use later */
$result["rows"] = $items;

$i = 0; # row counter  to enable alternate row coloring
?>

<table id="vendorsTbl" class="tableSimple">
    <thead>
    <th><input type="checkbox" disabled="disabled"/></th>
    <th align="left">Vendor Logo</th>
    <th align="left">Vendor Name</th>
</thead>
<tbody>
    <?php
    /* do a foreach on the $result['rows'] array */
    foreach ($result['rows'] as $rows):
        $id = $rows['id'];
        /* This bit just updates the class='row' bit with an alternating 1 OR 0 for alternative row coloring */
        echo '<tr class="row' . ($i++ % 2) . '">';
        ?>
    <td align="center"><input type="checkbox" name="tablecheckbox" id="<?php echo $id; ?>"/></td>
    <td align="center"><img src="<?php echo $rows['vendorLogo']; ?>" /></td>

    <td ><?php echo $rows['vendorName'] ?></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php
echo $pages->display_pages();
echo "<div class=\"spacer\"></div>";
echo "<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n";
