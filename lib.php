<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Returns information about the module.
 */
function rfidattendance_supports($feature) {
  switch($feature) {
    case FEATURE_MOD_INTRO: return true;
    default: return null;
  }
}

/**
 * Add a new rfidattendance instance.
 */
function rfidattendance_add_instance($data, $mform = null) {
  global $DB;
  $data->timecreated = time();
  return $DB->insert_record('rfidattendance', $data);
}

/**
 * Update a rfidattendance instance.
 */
function rfidattendance_update_instance($data, $mform = null) {
  global $DB;
  $data->id = $data->instance;
  $data->timemodified = time();
  return $DB->update_record('rfidattendance', $data);
}

/**
 * Delete a rfidattendance instance.
 */
function rfidattendance_delete_instance($id) {
  global $DB;
  return $DB->delete_records('rfidattendance', ['id' => $id]);
}