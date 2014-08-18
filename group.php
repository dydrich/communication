<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/14
 * Time: 11.47
 */
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";
require_once "../../lib/data_source.php";

check_session();

$uniqID = $_SESSION['__user__']->getUniqID();

$now = date("Y-m-d H:i:s");
$thread = $_SESSION['threads'][$_REQUEST['tid']];

include "group.html.php";
