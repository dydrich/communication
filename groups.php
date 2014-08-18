<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/14
 * Time: 11.15
 */
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$navigation_label = "messaggi";

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

$threads = $_SESSION['threads'];

include 'groups.html.php';
