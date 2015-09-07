<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$limit = 10;
$offset = 0;
if (isset($_GET['offset']) && $_GET['offset'] != ""){
	$offset = $_GET['offset'];
}

$sel_evs = "SELECT id_evento, abstract, testo, data_evento, data_inserimento, data_modifica, rb_utenti.nome AS nome, rb_utenti.cognome AS cg, pubblico, modificabile FROM rb_com_eventi, rb_utenti WHERE rb_com_eventi.owner = rb_utenti.uid ORDER BY data_evento DESC, id_evento DESC ";

if(!isset($_GET['second'])){
	$res_evs = $db->execute($sel_evs);
	//print $sel_links;
	$count = $res_evs->num_rows;
	$_SESSION['count_evs'] = $count;
}
else{
	$sel_evs .= "LIMIT $limit OFFSET $offset";
	$res_evs = $db->execute($sel_evs);
}

if($offset == 0)
	$page = 1;
else
	$page = ($offset / $limit) + 1;

$pagine = ceil($_SESSION['count_evs'] / $limit);
if($pagine < 1)
	$pagine = 1;

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_evs";
$row_class = "manager_row";
$row_class_menu = " manager_row_menu";
$drawer_label = "Elenco eventi";

include "events.html.php";

