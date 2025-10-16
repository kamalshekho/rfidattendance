<?php
require('../../config.php'); 
global $DB;

// JSON-Daten aus Anfrage holen
$input = json_decode(file_get_contents('php://input'), true);

// Prüfen, ob notwendige Parameter vorhanden sind
if (!isset($input['uid']) || !isset($input['courseid']) || !isset($input['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Zeitzone festlegen und aktuelle Zeit speichern
date_default_timezone_set('Europe/Berlin');
$now = time(); 

// Aktive Sitzung für den Kurs holen (neueste)
$session = $DB->get_record_sql("
    SELECT * FROM {rfidattendance_sessions} 
    WHERE courseid = ? AND active = 1
    ORDER BY sessiondate DESC
    LIMIT 1
", [$input['courseid']]);

// Wenn keine aktive Sitzung vorhanden ist -> Fehler
if (!$session) {
    http_response_code(400);
    echo json_encode(['error' => 'No active session today']);
    exit;
}

// Prüfen, ob Nutzer heute schon eingeloggt ist
$existing = $DB->get_record('rfidattendance_logs', [
    'userid' => $input['userid'],
    'courseid' => $input['courseid'],
    'sessiondate' => $session->sessiondate
]);

// Wenn bereits ein Eintrag existiert
if ($existing) {
    // Wenn noch kein Checkout -> jetzt auschecken
    if (!$existing->checkouttime) {
        $existing->checkouttime = time();
        $DB->update_record('rfidattendance_logs', $existing);
        echo json_encode(['success' => true, 'action' => 'checkout']);
        exit;
    } else {
        // Wenn Checkin & Checkout schon erledigt -> nichts tun
        echo json_encode(['success' => true, 'action' => 'already_done']);
        exit;
    }
}

// Wenn kein Eintrag existiert -> neuen Checkin erstellen
$record = (object)[
    'userid' => $input['userid'],
    'courseid' => $input['courseid'],
    'sessiondate' => $session->sessiondate,
    'checkintime' => $now,
    'uid' => $input['uid']
];

$DB->insert_record('rfidattendance_logs', $record);

// Erfolgsmeldung zurückgeben
echo json_encode(['success' => true, 'action' => 'checkin']);
