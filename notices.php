<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM);

$drawer_label = "Elenco avvisi";

$zone = 0;
$params = '';
if (isset($_REQUEST['zone'])) {
	$zone = $_REQUEST['zone'];
}

if($_SESSION['__role__'] == 'DSGA') {
	$zone = 4;
}

if ($zone != 0) {
	$params = 'WHERE gruppi = '.$zone;
}

$sel_notices = "SELECT * FROM rb_com_avvisi $params ORDER BY data_scadenza DESC ";

try{
	$res_notices = $db->executeQuery($sel_notices);
} catch (MySQLException $ex){
	$ex->redirect();
}

include "notices.html.php";
