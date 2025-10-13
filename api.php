<?php
require('../../config.php');
require_capability('mod/rfidattendance:view', context_system::instance());

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['uid']) || !isset($input['courseid']) || !isset($input['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

global $DB;

$record = (object)[
    'userid' => $input['userid'],
    'courseid' => $input['courseid'],
    'sessiondate' => strtotime('today'),
    'checkintime' => time(),
    'uid' => $input['uid']
];

$DB->insert_record('rfidattendance_logs', $record);

echo json_encode(['success' => true]);
