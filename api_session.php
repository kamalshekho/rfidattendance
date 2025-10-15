<?php
require('../../config.php');
require_login();

date_default_timezone_set('Europe/Berlin');

global $DB;

$id       = required_param('id', PARAM_INT);    
$courseid = required_param('courseid', PARAM_INT);
$sessiondate = required_param('sessiondate', PARAM_TEXT);
$starttime   = required_param('starttime', PARAM_TEXT);   
$endtime     = required_param('endtime', PARAM_TEXT);     

$cm = get_coursemodule_from_id('rfidattendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/rfidattendance:addinstance', $context);

$record = new stdClass();
$record->courseid    = $courseid;
$record->sessiondate = strtotime($sessiondate); 
$record->starttime   = strtotime($starttime);
$record->endtime     = strtotime($endtime);
$record->active      = 1;

$DB->insert_record('rfidattendance_sessions', $record);

$redirecturl = new moodle_url('/mod/rfidattendance/view.php', ['id' => $id]);
redirect($redirecturl);
