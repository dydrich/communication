<?php

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";
require_once "../../lib/data_source.php";

check_session();

$uid = $_SESSION['__user__']->getUid();

$thread = $_SESSION['thread'];
$thread->restoreThread(new MySQLDataLoader($db));
$users = $thread->getUsers();
$target_user = null;
if ($users[0]->getUid() == $_SESSION['__user__']->getUid()){
	$target_user = $users[1];
}
else {
	$target_user = $users[0];
}

try{
	$thread->readAll($_SESSION['__user__']);
	$_SESSION['thread'] = $thread;
} catch (MySQLException $ex){
	echo $ex->getMessage();
}

include "thread.html.php";