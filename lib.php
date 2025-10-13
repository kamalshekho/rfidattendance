
<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Returns information about the module.
 */
function simplebutton_supports($feature) {
  switch($feature) {
    case FEATURE_MOD_INTRO: return true;
    default: return null;
  }
}

/**
 * Add a new simplebutton instance.
 */
function simplebutton_add_instance($data, $mform = null) {
  global $DB;
  $data->timecreated = time();
  return $DB->insert_record('simplebutton', $data);
}

/**
 * Update a simplebutton instance.
 */
function simplebutton_update_instance($data, $mform = null) {
  global $DB;
  $data->id = $data->instance;
  $data->timemodified = time();
  return $DB->update_record('simplebutton', $data);
}

/**
 * Delete a simplebutton instance.
 */
function simplebutton_delete_instance($id) {
  global $DB;
  return $DB->delete_records('simplebutton', ['id' => $id]);
}