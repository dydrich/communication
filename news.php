<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$navigation_label = "dettaglio news";

$action = 1;
$idnews = 0;
if($_REQUEST['idn'] != 0){
	$action = 3;
	$sel_news = "SELECT * FROM rb_com_news WHERE id_news = ".$_REQUEST['idn'];
	try{
		$res_news = $db->executeQuery($sel_news);
	} catch (MySQLException $ex){
		print "Impossibile recuperare la news: ".$ex->getMessage();
		exit;
	}
	$news = $res_news->fetch_assoc();
	$idnews = $_REQUEST['idn'];
}

include "news.html.php";

?>