<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 1/10/17
 * Time: 5:22 PM
 * controllo variazioni d'orario per assemblea
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

$drawer_label = "Variazioni d'orario per assemblea sindacale";
$aid = $_REQUEST['aid'];
$rb = RBUtilities::getInstance($db);

$sel_date = "SELECT DATE(inizio) as _date, TIME(inizio) AS _start, TIME(fine) AS _end, luogo, sigle FROM rb_com_assemblee WHERE id = {$aid}";
$res = $db->executeQuery($sel_date);
$data = $res->fetch_assoc();

$assembly = new \eschool\comm\Assembly($aid, $data['_date'], $data['_start'], $data['_end'], $data['luogo'], $data['sigle'], new MySQLDataLoader($db));
$_SESSION['assembly'] = $assembly;
$variazioni = $assembly->getTimetablesChanges();

include "timetable_changes.html.php";