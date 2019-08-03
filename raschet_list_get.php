<?php
header('Access-Control-Allow-Origin: *');

ini_set('date.timezone', 'Europe/Moscow');

require_once(__DIR__ . '/../../lib/autoloader.php');

$connect = new ModelConnect();
$db_logist = $connect->DB_Logist();

$result = array('data' => [], 'error' => 0, 'error_text' => '');

$exp_type = 0;
if (isset($_GET) && isset($_GET['exp_type']) && is_numeric($_GET['exp_type']) && ($_GET['exp_type'] > 0)) {
	$exp_type = (int)$_GET['exp_type'];
}
if ($exp_type == 0) {
	$result['error'] = 1;
	$result['error_text'] = 'No params';
}

if ($db_logist->is_error()) {
	$result['error'] = 1;
	$result['error_text'] = 'Database error';
}

if ($result['error'] == 0) {
	$status = $db_logist->raschet_list_get($exp_type);
	if ($status === FALSE) {
		$result['error'] = 1;
		$result['error_text'] = 'Error load raschet list: ' . $status['error_text'];
	} else {
		$result['data'] = $status;
	}
}

unset($db_logist);
unset($connect);

print json_encode($result);
