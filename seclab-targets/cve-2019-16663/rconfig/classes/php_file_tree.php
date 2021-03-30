<?php

/*
  == PHP FILE TREE ==
  Let's call it...oh, say...version 1?
  == AUTHOR ==
  Cory S.N. LaViska
  http://abeautifulsite.net/
  == DOCUMENTATION ==
  For documentation and updates, visit http://abeautifulsite.net/notebook.php?article=21
 */

function array_sort_by_month(&$values) {
    $search_strings = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    $replace_string = array('0', '1', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
    $sort_key = array_map('ucfirst', $values);
    $sort_key = str_replace($search_strings, $replace_string, $sort_key);
    array_multisort($sort_key, SORT_DESC, SORT_STRING, $values);
}

/* SS NOTES
  added $chkOn argument, to append the file output <li> with a checkbox selection
  $chkOn = true, adds the checkbox
 */

function php_file_tree($directory, $return_link, $extensions = array(), $chkOn = false) {
    // SS - initialize $code as appears to throw an error for unknown reason
    $code = "";
    if (is_dir($directory) == true) {
        // Generates a valid XHTML list of all directories, sub-directories, and files in $directory
        // Remove trailing slash
        if (substr($directory, -1) == "/") {

            $directory = substr($directory, 0, strlen($directory) - 1);
        }

        $code .= php_file_tree_dir($directory, $return_link, $extensions, $chkOn);
    } else {

        $code = "<br/><br/><div class=\"spacer\"><font color=\"red\">No Data</font></div>";
    }
    return $code;
}

function php_file_tree_dir($directory, $return_link, $extensions = array(), $chkOn, $first_call = true) {
    // Recursive function called by php_file_tree() to list directories/files
    // Get and sort directories/files
    if (function_exists("scandir")) {
        $file = scandir($directory);
    } else {
        $file = php4_scandir($directory);
    }
    natcasesort($file);
    // Make directories first
    $files = $dirs = array();
    foreach ($file as $this_file) {
        if (is_dir("$directory/$this_file"))
            $dirs[] = $this_file;
        else
            $files[] = $this_file;
    }

    array_sort_by_month($dirs);
    // rsort($dirs); // ss - reverse sort years/days, by most recent first
    // echo '<pre>';
    // print_r($dirs);

    $file = array_merge($dirs, $files);
    // var_dump($dirs);die();
    // Filter unwanted extensions
    if (!empty($extensions)) {
        foreach (array_keys($file) as $key) {
            if (!is_dir("$directory/$file[$key]")) {
                $ext = substr($file[$key], strrpos($file[$key], ".") + 1);
                if (!in_array($ext, $extensions))
                    unset($file[$key]);
            }
        }
    }

// SS - initialize $php_file_tree as appears to throw an error sometimes for unknown reason
    $php_file_tree = '';
    if (count($file) > 2) { // Use 2 instead of 0 to account for . and .. "directories"
        $php_file_tree = "<ul";
        if ($first_call) {
            $php_file_tree .= " class=\"php-file-tree\"";
            $first_call = false;
        }
        $php_file_tree .= ">";
        foreach ($file as $this_file) {
            if ($this_file != "." && $this_file != "..") {
                if (is_dir("$directory/$this_file")) {
                    // Directory
                    $php_file_tree .= "<li class=\"pft-directory\"><a href=\"#\">" . htmlspecialchars($this_file) . "</a>";
                    $php_file_tree .= php_file_tree_dir("$directory/$this_file", $return_link, $extensions, $chkOn, false);
                    $php_file_tree .= "</li>";
                } else {
                    // File
                    // Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
                    $ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);
                    $link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
                    if ($chkOn === true) { // append checkbox
                        $php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\"><input type=\"checkbox\" value=\"$link\" name=\"CheckBxFile\" id=\"CheckBxFile\"/>" . htmlspecialchars($this_file) . "</li>";
                    } else {
                        $php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"#noLink\" $link>" . htmlspecialchars($this_file) . "</a></li>"; // #noLink used in URL href to avoid when click on file that the page jumps to top
                    }
                }
            }
        }
        $php_file_tree .= "</ul>";
    }
    return $php_file_tree;
}

// For PHP4 compatibility
function php4_scandir($dir) {
    $dh = opendir($dir);
    while (false !== ($filename = readdir($dh))) {
        $files[] = $filename;
    }
    sort($files);
    return($files);
}
