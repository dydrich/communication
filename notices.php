<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM);

$navigation_label = "avvisi";

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
}
else {
	$offset = $_REQUEST['offset'];
}

$limit = 10;

$sel_notices = "SELECT * FROM rb_com_avvisi ORDER BY data_scadenza DESC ";

if(!isset($_GET['second'])){
	try{
		$res_notices = $db->executeQuery($sel_notices);
	} catch (MySQLException $ex){
		$ex->redirect();
	}
	//print $sel_links;
	$count = $res_notices->num_rows;
	$_SESSION['count_notices'] = $count;
}
else{
	$sel_notices .= "LIMIT $limit OFFSET $offset";
	$res_notices = $db->execute($sel_notices);
}

if ($offset == 0) {
	$page = 1;
}
else {
	$page = ($offset / $limit) + 1;
}

$pagine = ceil($_SESSION['count_notices'] / $limit);
if ($pagine < 1) {
	$pagine = 1;
}

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_notices";
$row_class = "manager_row";
$row_class_menu = " manager_row_menu";

include "notices.html.php";

?>