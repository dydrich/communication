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
		$text = $db->real_escape_string($_POST['txt']);
		$th = null;
		$rb = RBUtilities::getInstance($db);
		if ($_REQUEST['tid'] == 0){
			$th = new Thread(0, new MySQLDataLoader($db), array($sender->getUniqID(), $target));
			$th->save();
			$_SESSION['threads'][$th->getTid()] = $th;
		}
		else {
			$th = $_SESSION['thread'];
			$th->restoreThread(new MySQLDataLoader($db));
		}
		$msg = new Message(0, $th->getTid(), $sender, $th->getTid(), new MySQLDataLoader($db), null);
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
		$_SESSION['thread'] = $th;

		//header("Content-type: text/plain");
		//echo "ok|".$th->getTid()."|".$th->isRead($sender)."|".$target_user->getFullName(1, 1)."|".$th->getMessagesCount()."|".$date."|".$text."|".$msg->getID();

		header("Content-type: application/json");
		$t = $th->getOtherUser($sender->getUniqID());
		$target_name = "";
		if ($t instanceof Thread) {
			$target_name = $t->getName();
		}
		else {
			$target_name = $t->getFullName();
		}
		$response = array("status" => "ok", "message" => "", "thread" => $th->getTid(), "sender" => "Tu", "target" => $target_name, "count" => $th->getMessagesCount(), "date" => "{$date}", "text" => "{$msg->getText()}", "mid" => $msg->getID());
		$response['t_t'] = $th->getType();
		echo json_encode($response);
		exit;
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
