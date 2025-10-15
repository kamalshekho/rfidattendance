<?php
// api_remove_all_uids.php
require('../../config.php');
require_login();

global $DB;

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$cmid = (int)($data['id'] ?? optional_param('id', 0, PARAM_INT));

if (!empty($cmid)) {
    $cm = get_coursemodule_from_id('rfidattendance', $cmid, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
} else {
    $context = context_system::instance();
}

require_capability('mod/rfidattendance:addinstance', $context);

$table = 'rfidattendance_mapping';

$DB->execute("UPDATE {{$table}} SET uid = ''");

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
