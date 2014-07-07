<?php

require_once "lib/Thread.php";
require_once "lib/Message.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

if ($_POST['upd'] == "msg"){
	$thread = $_SESSION['thread'];
	$thread->restoreThread(new MySQLDataLoader($db));
	$response = $thread->checkForUpdates();
	$_SESSION['thread'] = $thread;
}
else {
	/*
	 * threads update
	 * first step: new threads
	 */
	$last_msg = $_POST['lmsg'];
	$response = "";
	$sel_new_th = "SELECT * FROM rb_com_threads WHERE tid > {$_POST['tid']} AND user2 = {$_SESSION['__user__']->getUId()} AND user2_group = '{$_SESSION['user_type']}' ORDER BY tid ASC";
	$res_th = $db->executeQuery($sel_new_th);
	if ($res_th->num_rows < 1){
		$response = array();
		$response['status'] = "no_upd";
	}
	else {
		$rb = RBUtilities::getInstance($db);
		$tids = array();
		$response = array();
		while ($row = $res_th->fetch_assoc()){
			$user1 = $rb->loadUserFromUid($row['user1'], $row['user1_group']);
			$th = new Thread($row['tid'], $user1, $_SESSION['__user__'], new MySQLDataLoader($db));
			$_SESSION['threads'][$row['tid']] = $th;
			$last = $th->getLastMessage();
			list($d, $t) = explode(" ", $last->getSendTimestamp());
			if (date("Y-m-d") == $d){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$date .= " ".substr($t, 0, 5);
			//$last_msg = $last->getID();
			$tids[] = $row['tid'];
			array_unshift($response, array("type" => "new", "tid" => $row['tid'], "mid" => $last->getID(),  "user" => $user1->getFullName(1, 1), "count" => $th->getMessagesCount(), "datetime" => $date, "text" => $last->getText()));
		}
	}
	/*
	 * second step: new messages in existing threads 
	 */
	$ins = "";
	if (count($tids) > 0){
		$ins = implode(",", $tids);
	}
	$sel_new_msgs = "SELECT rb_com_messages.*, user1, user2, user1_group, user2_group FROM rb_com_messages, rb_com_threads WHERE rb_com_threads.tid = rb_com_messages.tid AND target = {$_SESSION['__user__']->getUId()} AND ((user1 = {$_SESSION['__user__']->getUId()} AND user1_group = '{$_SESSION['user_type']}') OR (user2 = {$_SESSION['__user__']->getUId()} AND user2_group = '{$_SESSION['user_type']}')) AND mid > {$last_msg}";
	//echo $sel_new_msgs;
	if ($ins != ""){
		$sel_new_msgs .= " AND rb_com_messages.tid NOT IN ({$ins})";
	}
	$res_new_msgs = $db->executeQuery($sel_new_msgs);
	if ($res_new_msgs->num_rows > 0){
		if ($response == ""){
			$response = array();
		}
		while ($row = $res_new_msgs->fetch_assoc()){
			$th = $_SESSION['threads'][$row[tid]];
			$th->restoreThread(new MySQLDataLoader($db));
			$user1 = $th->getOtherUser($_SESSION['__user__']->getUid());
			$msg = new Message($row['mid'], $th->getTid(), $user1, $_SESSION['__user__'], new MySQLDataLoader($db), $row);
			$msg->setText($row['text']);
			$th->addMessage($msg);
			$_SESSION['threads'][$row['tid']] = $th;
			
			list($d, $t) = explode(" ", $msg->getSendTimestamp());
			if (date("Y-m-d") == $d){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$date .= " ".substr($t, 0, 5);
			//$last_msg = $last->getID();
			array_unshift($response, array("type" => "del_new", "tid" => $row['tid'], "mid" => $msg->getID(), "user" => $user1->getFullName(1, 1), "count" => $th->getMessagesCount(), "datetime" => $date, "text" => $msg->getText()));
		}
	}
}

header("Content-type: application/json");
echo json_encode($response);
exit;