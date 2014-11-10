<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$drawer_label = "Elenco news";

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
}
else {
	$offset = $_REQUEST['offset'];
}

$limit = 10;

$sel_news = "SELECT id_news, abstract, data, testo, ora, rb_utenti.nome AS nome, rb_utenti.cognome AS cg FROM rb_com_news, rb_utenti WHERE rb_com_news.utente = rb_utenti.uid ORDER BY data DESC, id_news DESC ";

if(!isset($_GET['second'])){
	try{
		$res_news = $db->executeQuery($sel_news);
	} catch (MySQLException $ex){
		$ex->redirect();
	}
	//print $sel_links;
	$count = $res_news->num_rows;
	$_SESSION['count_news'] = $count;
}
else{
	$sel_news .= "LIMIT $limit OFFSET $offset";
	$res_news = $db->execute($sel_news);
}

if ($offset == 0) {
	$page = 1;
}
else {
	$page = ($offset / $limit) + 1;
}

$pagine = ceil($_SESSION['count_news'] / $limit);
if ($pagine < 1) {
	$pagine = 1;
}

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_news";
$row_class = "manager_row";
$row_class_menu = " manager_row_menu";

include "news_list.html.php";
