<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|ATA_PERM);

$order = '';
$order_link = 1;
$order_string = "Ordina per lettura";
if (isset($_REQUEST['order']) && $_REQUEST['order'] == 1) {
	$order = "(CASE (COALESCE(rb_com_lettura_circolari.data_lettura, '')) WHEN '' THEN 0 ELSE 1 END),";
	$order_link = 0;
	$order_string = "Ordina per numero circolare";
}
else if (isset($_SESSION['__user_config__']['ordine_circolari']) && $_SESSION['__user_config__']['ordine_circolari'][0] == 1) {
	$order = "(CASE (COALESCE(rb_com_lettura_circolari.data_lettura, '')) WHEN '' THEN 0 ELSE 1 END),";
	$order_link = 0;
	$order_string = "Ordina per numero circolare";
}


$drawer_label = "Elenco circolari";

$query = "SELECT rb_com_circolari.*, COALESCE(rb_com_lettura_circolari.data_lettura, 0) AS dt
		  FROM rb_com_circolari 
		  	LEFT OUTER JOIN rb_com_lettura_circolari 
		  	ON(rb_com_circolari.id_circolare = rb_com_lettura_circolari.id_circolare) 
		  	AND docente = ".$_SESSION['__user__']->getUid()." 
		  WHERE anno = ".$_SESSION['__current_year__']->get_ID()."  
		  ORDER BY 
		  $order
		  progressivo DESC";

try{
	$result = $db->executeQuery($query);
} catch (MySQLException $ex){
	$ex->redirect();
}

include 'vedi_circolari.html.php';
