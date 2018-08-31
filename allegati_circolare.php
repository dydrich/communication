<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

require_once "../../lib/MimeType.php";

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_reg_home__'] = "./";

ini_set("display_errors", "1");

$drawer_label = "Circolare: gestione allegati";

$idc = $_REQUEST['idc'];
$allegati = [];
$cd = $db->executeCount("SELECT data_circolare FROM rb_com_circolari WHERE id_circolare = $idc");
$nd = $db->executeCount("SELECT progressivo FROM rb_com_circolari WHERE id_circolare = $idc");
$fd = format_date($cd, SQL_DATE_STYLE, IT_DATE_STYLE, '/');
$sel_att = "SELECT * FROM rb_com_allegati_circolari WHERE id_circolare = $idc AND pdf_firmato IS NULL";
try {
	$res_att = $db->executeQuery($sel_att);
	if ($res_att->num_rows > 0) {
		while ($row = $res_att->fetch_assoc()) {
			$allegati[] = $row;
		}
	}
} catch (MySQLException $ex) {

}

include "allegati_circolare.html.php";
