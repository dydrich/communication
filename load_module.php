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

$uid = $_SESSION['__user__']->getUid();
//$other_users = array();
//if (!$_SESSION['threads']){
	$sel_th = "SELECT * FROM rb_com_threads WHERE ((user1 = {$uid} AND user1_group = '{$user_type}') OR (user2 = {$uid} AND user2_group = '{$user_type}')) ORDER BY last_message DESC";
	//print $sel_th;
	$res_th = $db->execute($sel_th);
	$rb = RBUtilities::getInstance($db);
	if ($res_th->num_rows > 0){
		$threads = array();
		while ($th = $res_th->fetch_assoc()){
			$u1 = $u2 = null;
			if ($th['user1'] == $uid && $th['user1_group'] == $user_type){
				$u1 = $_SESSION['__user__'];
				$u2 = $rb->loadUserFromUid($th['user2'], $th['user2_group']);
				//$other_user = $u2;
			}
			else {
				$u2 = $_SESSION['__user__'];
				$u1 = $rb->loadUserFromUid($th['user1'], $th['user1_group']);
				//$other_user = $u1;
			}
			$thread = new Thread($th['tid'], $u1, $u2, new MySQLDataLoader($db));
			$threads[$th['tid']] = $thread;
			//$other_users[$th['tid']] = $other_user;
		}
		$_SESSION['threads'] = $threads;
	}
//}
/*
else {
	$threads = array();
	$threads = $_SESSION['threads'];
	foreach ($threads as $th){
		if ($th->getUser1()->getUid() == $uid){
			$u1 = $_SESSION['__user__'];
			//$other_user = $th->getUser2();
		}
		else {
			$u2 = $_SESSION['__user__'];
			//$other_user = $th->getUser1();
		}
		$other_users[$th->getTid()] = $other_user;
	}
}
*/

if (isset($_REQUEST['page'])){
	header("Location: {$_REQUEST['page']}.php");
}
else {
	header("Location: {$module['front_page']}");
}
