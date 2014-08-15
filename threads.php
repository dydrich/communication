<?php

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$navigation_label = "messaggi";

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

if (isset($_SESSION['threads'])){
	$threads = $_SESSION['threads'];
	$ordered_threads = array();
	$last_tid = 0;
	$last_msg = 0;
	foreach ($threads as $th){
		$th->restoreThread(new MySQLDataLoader($db));
		if (count($th->getMessages()) == 0) {
			$ordered_threads[$th->getCreationDate()] = $th;
		}
		else {
			$ordered_threads[$th->getLastMessage()->getSendTimestamp()] = $th;
			if ($th->getLastMessage()->getID() > $last_msg) {
				$last_msg = $th->getLastMessage()->getID();
			}
		}
		if ($th->getTid() > $last_tid) {
			$last_tid = $th->getTid();
		}
	}
	krsort($ordered_threads);
	
	$th_ids = array_keys($_SESSION['threads']);

}
else {
	$last_tid = 0;
	$last_msg = 0;
}

include 'threads.html.php';
