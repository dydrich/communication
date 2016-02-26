<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$drawer_label = "Elenco news";

$sel_news = "SELECT id_news, abstract, data, testo, ora, rb_utenti.nome AS nome, rb_utenti.cognome AS cg FROM rb_com_news, rb_utenti WHERE rb_com_news.utente = rb_utenti.uid ORDER BY data DESC, id_news DESC ";

try{
	$res_news = $db->executeQuery($sel_news);
} catch (MySQLException $ex){
	$ex->redirect();
}
//print $sel_links;
$count = $res_news->num_rows;
$_SESSION['count_news'] = $count;

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_news";

include "news_list.html.php";
