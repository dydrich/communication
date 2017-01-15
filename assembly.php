<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 12/28/16
 * Time: 6:53 PM
 */
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";
require_once "../../lib/ScheduleModule.php";
require_once "../../lib/ScheduleModuleDay.php";
require_once "../../lib/RBTime.php";
require_once "./lib/Assembly.php";

ini_set('display_errors', 1);

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$drawer_label = "Dettaglio assemblea sindacale";

// ordini di scuola
$sel_ord = "SELECT * FROM rb_tipologia_scuola WHERE has_admin = 1 AND attivo = 1";
$res_ord = $db->executeQuery($sel_ord);

$action = 1;
$idnews = 0;
if($_REQUEST['id'] != 0){
	$sel_date = "SELECT DATE(inizio) as _date, TIME(inizio) AS _start, TIME(fine) AS _end, luogo, sigle FROM rb_com_assemblee WHERE id = {$_REQUEST['id']}";
	$res = $db->executeQuery($sel_date);
	$data = $res->fetch_assoc();
	$assembly = new \eschool\comm\Assembly($_REQUEST['id'], $data['_date'], $data['_start'], $data['_end'], $data['luogo'], $data['sigle'], new MySQLDataLoader($db));
	$_SESSION['assembly'] = $assembly;
	print_r($assembly);
}
else{
	$_i = 0;
}

include "assembly.html.php";
