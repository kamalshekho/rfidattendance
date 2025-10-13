<?php
require('../../config.php');
require_capability('mod/rfidattendance:addinstance', context_system::instance());

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['uid']) || !isset($input['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

global $DB;

// Prüfen ob UID schon existiert
$existing = $DB->get_record('rfidattendance_mapping', ['uid' => $input['uid']]);
if ($existing) {
    http_response_code(409);
    echo json_encode(['error' => 'UID already assigned']);
    exit;
}

// Mapping anlegen
$record = (object)[
    'userid' => $input['userid'],
    'uid' => $input['uid']
];
$DB->insert_record('rfidattendance_mapping', $record);

echo json_encode(['success' => true]);
