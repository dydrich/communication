<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 12/28/16
 * Time: 6:53 PM
 */
require_once "./lib/Assembly.php";
require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);
require_once "../../lib/start.php";
header("Content-type: application/json");
$response = array("status" => "ok", "message" => "Operazione completata");

switch ($_POST['action']){
	case 'change_time':
		$assembly = $_SESSION['assembly'];
		$assembly->setDatasource(new MySQLDataLoader($db));
		$cls = $_POST['cls'];
		$field = $_POST['field'];
		$value = $_POST['value'];
		try{
			$realID = $assembly->insertClassTimetableChange($cls, $field, $value);
			$response['id'] = $realID;
			$response['notice'] = $assembly->getNotice($cls);
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Errore nella registrazione dei dati";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		break;
	case 'delete_time':
		$assembly = $_SESSION['assembly'];
		$assembly->setDatasource(new MySQLDataLoader($db));
		$cls = $_POST['cls'];
		$field = $_POST['field'];
		try{
			$ret = $assembly->deleteClassTimetableChange($cls, $field);
			$response['response'] = $ret;
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Errore nella registrazione dei dati";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		break;
	case 'insert':
		$date = format_date($_POST['data_evento'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
		$start = $_POST['starttime'].":00";
		$where = $db->real_escape_string($_POST['place']);
		$unions = $db->real_escape_string($_POST['associations']);
		$id = $realID = 0;
		$assembly = new \eschool\comm\Assembly($id, $date, $start, $start, $where, $unions, new MySQLDataLoader($db));
		try{
			$realID = $assembly->insert();
			$response['id'] = $realID;
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Errore nella registrazione dei dati";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		break;
	case 'update':
		$date = $_POST['data_evento'];
		$start = $_POST['starttime'];
		$where = $db->real_escape_string($_POST['place']);
		$unions = $db->real_escape_string($_POST['associations']);
		$assembly = $_SESSION['assembly'];
		$assembly->setWhere($where);
		$assembly->setDate(format_date($date, IT_DATE_STYLE, SQL_DATE_STYLE, "-"));
		$assembly->setStartTime($start);
		$assembly->setUnions($unions);
		$assembly->setDatasource(new MySQLDataLoader($db));
		try{
			$assembly->update();
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Errore nella registrazione dei dati";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		break;
	case 'delete':
		$id = $_POST['id'];
		$start = "00:00:00";
		$assembly = new \eschool\comm\Assembly($id, date("Y-m-d"), $start, $start, '', '', new MySQLDataLoader($db));
		try{
			$assembly->delete();
		} catch (MySQLException $ex){
			$response['status'] = "kosql";
			$response['dbg_message'] = "Query: {$ex->getQuery()} ------ Errore: {$ex->getMessage()}";
			$response['message'] = "Errore nella registrazione dei dati";
			$res = json_encode($response);
			echo $res;
			exit;
		}
		break;
}

$res = json_encode($response);
echo $res;
exit;