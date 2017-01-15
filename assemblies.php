<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 12/28/16
 * Time: 6:49 PM
 * assemblies
 */
require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$sel_ass = "SELECT * FROM rb_com_assemblee ORDER BY fine DESC";

$res_ass = $db->execute($sel_ass);
$count = $res_ass->num_rows;
$_SESSION['count_ass'] = $count;

$drawer_label = "Elenco assemblee";

include "assemblies.html.php";