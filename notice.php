<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM);

$drawer_label = "Gestione avviso";

$action = 1;
$idnotice = 0;
$params = "";
if($_REQUEST['idn'] != 0){
	$action = 3;
	$sel_notice = "SELECT * FROM rb_com_avvisi WHERE id = ".$_REQUEST['idn'];
	try{
		$res_notice = $db->executeQuery($sel_notice);
	} catch (MySQLException $ex){
		print "Impossibile recuperare l'avviso: ".$ex->getMessage();
		exit;
	}
	$notice = $res_notice->fetch_assoc();
	$idnotice = $_REQUEST['idn'];
	$params = "WHERE id_tipo = ".$notice['tipo'];
}

$sel_classi = "SELECT id_classe, anno_corso, sezione, codice, nome 
			  FROM rb_classi, rb_tipologia_scuola, rb_sedi 
			  WHERE id_sede = sede 
			  AND rb_classi.ordine_di_scuola = id_tipo 
			  ORDER BY rb_classi.ordine_di_scuola, sezione, anno_corso";
$res_classi = $db->executeQuery($sel_classi);
$classe = array();
while ($row = $res_classi->fetch_assoc()){
	$classi[$row['id_classe']] = array("id" => $row['id_classe'], "classe" => $row['anno_corso'].$row['sezione'], "sede" => $row['nome']);
}

$tipo_avviso = [];
$sel_tipi = "SELECT id_tipo, tipo FROM rb_com_tipo_avviso $params ORDER BY id_tipo";
$res_tipi = $db->executeQuery($sel_tipi);
while ($row = $res_tipi->fetch_assoc()) {
	$tipo_avviso[] = $row;
}

include "notice.html.php";
