<?php
require('../../config.php');
require_login();

global $DB;

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$userid = $data['userid'] ?? optional_param('userid', 0, PARAM_INT);
$cmid   = $data['id']     ?? optional_param('id', 0, PARAM_INT);

if (empty($userid)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Missing userid']);
    exit;
}

if (!empty($cmid)) {
    $cm = get_coursemodule_from_id('rfidattendance', $cmid, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
} else {
    $context = context_system::instance();
}

require_capability('mod/rfidattendance:addinstance', $context);

$table = 'rfidattendance_mapping';

$record = $DB->get_record($table, ['userid' => $userid], '*', IGNORE_MISSING);
if (!$record) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Mapping not found']);
    exit;
}

$ok = $DB->delete_records($table, ['userid' => $userid]);

header('Content-Type: application/json');
echo json_encode(['success' => $ok !== false]);
exit;