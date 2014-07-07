<?php

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$navigation_label = "messaggi";

$user_type = $_SESSION['user_type'];
$uid = $_SESSION['__user__']->getUid();

if (isset($_SESSION['threads'])){
	$threads = $_SESSION['threads'];
	$ordered_threads = array();
	foreach ($threads as $th){
		$ordered_threads[$th->getLastMessage()->getSendTimestamp()] = $th;
	}
	krsort($ordered_threads);
	
	$last_tid = $db->executeCount("SELECT MAX(tid) FROM rb_com_threads WHERE ((user1 = {$uid} AND user1_group = '{$user_type}') OR (user2 = {$uid} AND user2_group = '{$user_type}'))");
	$last_msg = $db->executeCount("SELECT MAX(mid) FROM rb_com_messages WHERE sender = {$uid} OR target = {$uid}");
}
else {
	$last_tid = 0;
	$last_msg = 0;
}

include 'threads.html.php';