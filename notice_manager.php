<?php

require_once "../../lib/start.php";

check_session(AJAX_CALL);
check_permission(DIR_PERM|DSG_PERM);

if($_POST['action'] != 2){
	$data_scadenza = format_date($_POST['data'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
	$testo = $db->real_escape_string(nl2br($_POST['testo']));
}

$groups = 2;
if ($_SESSION['__role__'] == 'DSGA') {
	$groups = 4;
}

switch($_POST['action']){
	case 1:     // inserimento
		$statement = "INSERT INTO rb_com_avvisi (data_scadenza, data_inserimento, testo, gruppi) VALUES ('{$data_scadenza}', NOW(), '{$testo}', {$groups})";
		$msg = "Avviso inserito correttamente";
		break;
	case 2:     // cancellazione
		$statement = "DELETE FROM rb_com_avvisi WHERE id = ".$_REQUEST['_i'];
		$msg = "Avviso cancellato";
		break;
	case 3:     // modifica
		$statement = "UPDATE rb_com_avvisi SET data_scadenza = '{$data_scadenza}', testo = '{$testo}' WHERE id = ".$_REQUEST['_i'];
		$msg = "Avviso aggiornato correttamente";
		break;
}
header("Content-type: application/json");
try{
	$recordset = $db->executeUpdate($statement);
} catch (MySQLException $ex){
	$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
	echo json_encode($response);
	exit;
}

$response = array("status" => "ok", "message" => $msg);
echo json_encode($response);
