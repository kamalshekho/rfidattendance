<?php
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_simplebutton_mod_form extends moodleform_mod {

  public function definition() {
    $mform = $this->_form;

    // Module name
    $mform->addElement('text', 'name', get_string('modulename', 'simplebutton'));
    $mform->setType('name', PARAM_TEXT);
    $mform->addRule('name', null, 'required', null, 'client');

    // Intro/description
    $this->standard_intro_elements();

    // Standard grading, visibility, etc.
    $this->standard_coursemodule_elements();

    $this->add_action_buttons();
  }
}
