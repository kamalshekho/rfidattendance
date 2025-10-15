<?php
require('../../config.php'); 
global $DB;

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['uid']) || !isset($input['courseid']) || !isset($input['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

date_default_timezone_set('Europe/Berlin');
$now = time(); 

$session = $DB->get_record_sql("
    SELECT * FROM {rfidattendance_sessions} 
    WHERE courseid = ? AND active = 1
    ORDER BY sessiondate DESC
    LIMIT 1
", [$input['courseid']]);

if (!$session) {
    http_response_code(400);
    echo json_encode(['error' => 'No active session today']);
    exit;
}

$existing = $DB->get_record('rfidattendance_logs', [
    'userid' => $input['userid'],
    'courseid' => $input['courseid'],
    'sessiondate' => $session->sessiondate
]);

if ($existing) {
    if (!$existing->checkouttime) {
        $existing->checkouttime = time();
        $DB->update_record('rfidattendance_logs', $existing);
        echo json_encode(['success' => true, 'action' => 'checkout']);
        exit;
    } else {
        echo json_encode(['success' => true, 'action' => 'already_done']);
        exit;
    }
}

$record = (object)[
    'userid' => $input['userid'],
    'courseid' => $input['courseid'],
    'sessiondate' => $session->sessiondate,
    'checkintime' => $now,
    'uid' => $input['uid']
];

$DB->insert_record('rfidattendance_logs', $record);

echo json_encode(['success' => true, 'action' => 'checkin']);
