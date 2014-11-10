<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

$drawer_label = "Gestione circolari";

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
}
else {
	$offset = $_REQUEST['offset'];
}

$limit = 10;
$query = "SELECT rb_com_circolari.*, nome, cognome FROM rb_com_circolari, rb_utenti WHERE owner = uid AND anno = ".$_SESSION['__current_year__']->get_ID()." ORDER BY anno DESC, progressivo DESC, data_circolare DESC";

if(!isset($_GET['second'])){
	try{
		$result = $db->executeQuery($query);
	} catch (MySQLException $ex){
		$ex->redirect();
	}
	//print $sel_links;
	$count = $result->num_rows;
	$_SESSION['count_c'] = $count;
}
else{
	$query .= " LIMIT $limit OFFSET $offset";
	$result = $db->execute($query);
}

if ($offset == 0) {
	$page = 1;
}
else {
	$page = ($offset / $limit) + 1;
}

$pagine = ceil($_SESSION['count_c'] / $limit);
if ($pagine < 1) {
	$pagine = 1;
}

// dati per la paginazione (navigate.php)
$colspan = 5;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_c";
$row_class = "manager_row";
$row_class_menu = " manager_row_menu";

include 'circolari.html.php';
