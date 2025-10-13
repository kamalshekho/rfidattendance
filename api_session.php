<?php
require('../../config.php');
require_login();

global $DB;

$courseid = required_param('courseid', PARAM_INT);
$sessiondate = required_param('sessiondate', PARAM_INT);
$starttime   = required_param('starttime', PARAM_INT);
$endtime     = required_param('endtime', PARAM_INT);

$context = context_course::instance($courseid);
require_capability('mod/rfidattendance:addinstance', $context);

$record = new stdClass();
$record->courseid = $courseid;
$record->sessiondate = $sessiondate;
$record->starttime = $starttime;
$record->endtime = $endtime;
$record->active = 1;

$DB->insert_record('rfidattendance_sessions', $record);

$redirecturl = new moodle_url('/mod/rfidattendance/view.php', ['id' => required_param('id', PARAM_INT)]);
redirect($redirecturl);
