<?php

/**
 * load the requested module
 */

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$module_code = $_REQUEST['module'];

$sel_module = "SELECT * FROM rb_modules WHERE code_name = '{$module_code}'";
$res_module = $db->execute($sel_module);
$module = $res_module->fetch_assoc();

$_SESSION['__modules__'][$module_code]['home'] = $module['home'];
$_SESSION['__modules__'][$module_code]['lib_home'] = $module['lib_home'];
$_SESSION['__modules__'][$module_code]['front_page'] = $module['front_page'];
$_SESSION['__modules__'][$module_code]['path_to_root'] = $module['path_to_root'];

$_SESSION['__mod_area__'] = $_REQUEST['area'];

if ($module_code == "com"){
	unset($_SESSION['threads']);
	unset($_SESSION['thread']);
}

$user_type = "";
if ($_SESSION['__user__'] instanceof SchoolUserBean){
	$user_type = "school";
}
else if ($_SESSION['__user__'] instanceof ParentBean){
	$user_type = "parent";
}
else {
	$user_type = "student";
}
$_SESSION['user_type'] = $user_type;
$uniqID = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$uid} AND type = '{$user_type}'");
$uid = $_SESSION['__user__']->getUid();
$sel_th = "SELECT rb_com_threads.* FROM rb_com_threads, rb_com_utenti_thread WHERE tid = thread AND utente = {$uniqID} ORDER BY last_message DESC";
$res_th = $db->execute($sel_th);
$rb = RBUtilities::getInstance($db);
if ($res_th->num_rows > 0){
	$threads = array();
	while ($th = $res_th->fetch_assoc()){
		if ($th['owner'] != "") {
			$owner = $rb->loadUserFromUniqID($th['owner']);
			//$other_user = $u2;
		}
		else {
			$owner = "";
		}
		$thread = new Thread($th['tid'], new MySQLDataLoader($db), $th['creation']);
		if ($th['type'] == 'G') {
			$thread->setName($th['name']);
			$thread->setType('G');
		}
		$threads[$th['tid']] = $thread;
	}
	$_SESSION['threads'] = $threads;
}

if (isset($_REQUEST['page'])){
	header("Location: {$_REQUEST['page']}.php");
}
else {
	header("Location: {$module['front_page']}");
}
