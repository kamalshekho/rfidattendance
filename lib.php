<?php
defined('MOODLE_INTERNAL') || die();

function rfidattendance_supports($feature) {
  switch($feature) {
    case FEATURE_MOD_INTRO: return true;
    default: return null;
  }
}

function rfidattendance_add_instance($data, $mform = null) {
  global $DB;
  $data->timecreated = time();
  return $DB->insert_record('rfidattendance', $data);
}

function rfidattendance_update_instance($data, $mform = null) {
  global $DB;
  $data->id = $data->instance;
  $data->timemodified = time();
  return $DB->update_record('rfidattendance', $data);
}

function rfidattendance_delete_instance($id) {
  global $DB;
  return $DB->delete_records('rfidattendance', ['id' => $id]);
}