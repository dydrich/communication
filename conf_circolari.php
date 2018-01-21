<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 21/01/18
 * Time: 18.16
 */

require_once "../../lib/start.php";

ini_set("display_errors", DISPLAY_ERRORS);

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM|ATA_PERM);

$_SESSION['__path_to_root__'] = "../../";
$_SESSION['__path_to_reg_home__'] = "./";

if (isset($_SESSION['__user_config__']['ordine_circolari'])) {
	$active = $_SESSION['__user_config__']['ordine_circolari'][0];
}
else {
	$active = 0;
}

$drawer_label = "Configurazione registro personale: ordine di visualizzazione circolari";

include "conf_circolari.html.php";
