<?php
require('../../config.php');
require_login();

global $DB;

$id        = required_param('id', PARAM_INT);    
$courseid  = required_param('courseid', PARAM_INT);
$sessiondate = required_param('sessiondate', PARAM_TEXT);
$starttime   = required_param('starttime', PARAM_TEXT);   
$endtime     = required_param('endtime', PARAM_TEXT);     

$cm = get_coursemodule_from_id('rfidattendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/rfidattendance:addinstance', $context);

// Zeitzone setzen
date_default_timezone_set('Europe/Berlin');

$record = new stdClass();
$record->courseid    = $courseid;
$record->sessiondate = strtotime($sessiondate);  // Tagesbeginn
$record->starttime   = strtotime($sessiondate . ' ' . $starttime); // exakte Startzeit
$record->endtime     = strtotime($sessiondate . ' ' . $endtime);   // exakte Endzeit
$record->active      = 1;

$DB->insert_record('rfidattendance_sessions', $record);

// Zurück zur Übersicht
$redirecturl = new moodle_url('/mod/rfidattendance/view.php', ['id' => $id]);
redirect($redirecturl);
