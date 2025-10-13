<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT); // Course module ID.
$cm = get_coursemodule_from_id('simplebutton', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_capability('mod/simplebutton:view', $context);

$PAGE->set_url('/mod/simplebutton/view.php', ['id' => $id]);
$PAGE->set_title(get_string('pluginname', 'simplebutton'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();



echo html_writer::tag('button',
  get_string('pressme', 'simplebutton'),
  ['id' => 'simplebutton', 'class' => 'btn btn-primary']
);

$js = <<<JS
  document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("simplebutton");
    btn.addEventListener("click", function() {
      const original = btn.textContent;
      btn.textContent = "Success!";
      setTimeout(() => {
        btn.textContent = original;
      }, 1500);
    });
  });
JS;

$PAGE->requires->js_init_code($js);

echo $OUTPUT->footer();
