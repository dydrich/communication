<?php

require_once "../../lib/start.php";
require_once "./lib/Notice.php";

check_session(AJAX_CALL);
check_permission(DIR_PERM|DSG_PERM);

ini_set("display_errors", 1);

$data_scadenza = $testo = $tipo = null;
$id = 0;
$classi = "";
$livello = "";

$id = $_REQUEST['_i'];
if($_POST['action'] != 2){
	$data_scadenza = format_date($_POST['data'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
	$testo = $db->real_escape_string(nl2br($_POST['testo']));
	$tipo = $_POST['tipo'];
	if(isset($_POST['level']) && $_POST['level'] != 0) {
		$livello = $_POST['level'];
	}
	if(isset($_POST['classes'])) {
		$classi = implode(",", $_POST['classes']);
	}
}

$groups = 2;
if(isset($_POST['zone'])) {
	$groups = $_POST['zone'];
}

$notice = new \eschool\comm\Notice($tipo, $data_scadenza, $testo, $groups, $livello, $classi, $id, new MySQLDataLoader($db));

try {
	switch($_POST['action']){
		case 1:     // inserimento
			$id = $notice->insert();
			$msg = "Avviso inserito correttamente";
			break;
		case 2:     // cancellazione
			$notice->delete();
			$msg = "Avviso cancellato";
			break;
		case 3:     // modifica
			$notice->update();
			$msg = "Avviso aggiornato correttamente";
			break;
	}
} catch (MySQLException $ex){
	$response['status'] = "kosql";
	$response['query'] = $ex->getQuery();
	$response['dbg_message'] = $ex->getMessage();
	$response['message'] = "Errore nella registrazione dei dati";
	$res = json_encode($response);
	echo $res;
	exit;
}
header("Content-type: application/json");
$response = array("status" => "ok", "message" => $msg);
echo json_encode($response);
