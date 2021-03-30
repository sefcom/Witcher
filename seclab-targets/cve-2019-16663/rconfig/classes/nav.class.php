<?php
/**
 * Description of nav class
 * Renders the top menu and sub menu navigation items for all pages
 * @author stephen stack
 */
class nav {
   // get all Parent Menu Items from the DB and sort
    private function getAllParentMenuItems(){
        require_once("../classes/db2.class.php");
        $db2 = new db2();
        $db2->query("SELECT menuName, pageName FROM menuPages WHERE topLevel = 1 ORDER BY menuSortId ASC"); // get top level menu items
        $topLevelResult = $db2->resultset();
        return $topLevelResult;
    }
    
    // get all submenu items from DB based on current page parentID (this output can also be a topLevel page as those entries must have the parentId fields set to themselves in the DB)
    private function getAllSubMenuItems($config_page){
        $db2 = new db2();
        $db2->query("SELECT topLevel, menuName, pageName FROM menuPages WHERE parentId = (SELECT parentId FROM menuPages WHERE pageName = :config_page) ORDER BY id "); // get top level menu items
        $db2->bind(':config_page', $config_page);
        $subMenuResult = $db2->resultset();
        return $subMenuResult;
    }
    
    // render topNav sction
    public function renderTopNav($config_page, $host, $ip){
        // flatten getAllParentMenuItems array for checking with in_array later
        $topLevelPageNames = array();
        foreach ($this->getAllParentMenuItems() as $row){
            $topLevelPageNames[] = $row['pageName'];
        }
        // flatten getAllSubMenuItems array for checking with in_array later
        $subMenuPageNames = array();
        foreach ($this->getAllSubMenuItems($config_page) as $row){
            $subMenuPageNames[] = $row['pageName'];
        }
        // if the current page is in the topLevel pages from the DB, render the menu OR if the current_page is a subitem of the TopLevel, render the TopMenu
        if(in_array($config_page, $topLevelPageNames) || in_array($config_page, $subMenuPageNames)){ 
            echo '<div id="navwrap">
                    <div id="nav">
                     <ul>';
            foreach ($this->getAllParentMenuItems() as $item=>$value){ // echo each <li>
                if ($config_page == $value['pageName']){ $activeClass = 'class="active';} else {$activeClass = '';} // set class="active CSS if current page
                echo '<li><a href="'.$value['pageName'].'" '.$activeClass.'">'.$value['menuName'].'</a></li>';
            }
            echo '</ul>
                  <div id="navtitle">'. $host . ':' . $ip .'</div>
                  </div>'; // end nav div
        }
    }
    
    // render subNav sction
    public function renderSubNav($config_page){
        $menuCount = count($this->getAllSubMenuItems($config_page)); // count array of menuPages returned
        if ($menuCount >1){ //check if more than one value returned. If only one menu returned, this is a toplevel menu without Submenus, and we should not display the submenus
             echo '<div id="nav2">';
              foreach ($this->getAllSubMenuItems($config_page) as $item=>$value){ // echo each <li>
                  if ($config_page == $value['pageName']){ $activeClass = "class='nav2Selected'";} else {$activeClass = '';} // set class="active CSS if current page
                  echo '<li><a href="'.$value['pageName'].'" '.$activeClass.'">'.$value['menuName'].'</a></li>';
              }            
             echo '</div>'; // end nav2 div
             echo '</div>'; // end navwrap div
        } else {
            // display final navwrap div tag for renderTopNav if no sub menu items per 'if ($menuCount >1){' so that the div renders correctly
            echo '</div>'; // end navwrap div
        }
    }
}
