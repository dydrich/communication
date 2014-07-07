<?php

require_once "lib/Thread.php";
require_once "lib/Message.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$action = $_REQUEST['do'];

switch ($action){
	case "send":
		$sender = $_SESSION['__user__'];
		$target = $_POST['targetID'];
		$target_type = $_POST['target_type'];
		$text = $db->real_escape_string($_POST['txt']);
		$th = null;
		$rb = RBUtilities::getInstance($db);
		if ($_REQUEST['tid'] == 0){
			$target_user = $rb->loadUserFromUid($target, $target_type);
			$th = new Thread(0, $sender, $target_user, new MySQLDataLoader($db));
			$th->save();
			//array_unshift($_SESSION['threads'], $th);
			$_SESSION['threads'][$th->getTid()] = $th;
		}
		else {
			$th = $_SESSION['thread'];
			$th->restoreThread(new MySQLDataLoader($db));
			$target_user = $th->getOtherUser($sender->getUid());
		}
		$msg = new Message(0, $th->getTid(), $sender, $target_user, new MySQLDataLoader($db), null);
		$msg->setText($text);
		$msg->send();
		$th->addMessage($msg);
		$_SESSION['threads'][$th->getTid()] = $th;
		krsort($_SESSION['threads']);
		list($date, $time) = explode(" ", $msg->getSendTimestamp());
		if ($_REQUEST['tid'] == 0){
			if (date("Y-m-d") == $date){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
			}
		}
		else {
			if (date("Y-m-d") == $date){
				$date = "Inviato oggi alle";
			}
			else {
				$date = "Inviato il ".format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
			}
		}
		$date .= " ".substr($time, 0, 5);
		if ($_REQUEST['tid'] != 0){
			$_SESSION['thread'] = $th;
		}
		header("Content-type: text/plain");
		echo "ok|".$th->getTid()."|".$th->isRead($sender)."|".$target_user->getFullName(1, 1)."|".$th->getMessagesCount()."|".$date."|".$text."|".$msg->getID();
		break;
	case "list_threads":
		unset($_SESSION['thread']);
		header("Location: threads.php");
		break;	
	case "show_thread":
		$tid = $_REQUEST['tid'];
		$thread = $_SESSION['threads'][$tid];
		$_SESSION['thread'] = $thread;
		header("Location: thread.php");
		break;
}