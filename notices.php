<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM);

$drawer_label = "Elenco avvisi";

if ($_SESSION['__role__'] == 'Dirigente scolastico') {
	$params = 'WHERE gruppi = 2';
}
else {
	$params = 'WHERE gruppi = 4';
}

$sel_notices = "SELECT * FROM rb_com_avvisi $params ORDER BY data_scadenza DESC ";

try{
	$res_notices = $db->executeQuery($sel_notices);
} catch (MySQLException $ex){
	$ex->redirect();
}
//print $sel_links;
$count = $res_notices->num_rows;
$_SESSION['count_notices'] = $count;

include "notices.html.php";
