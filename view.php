<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT); 
$cm = get_coursemodule_from_id('rfidattendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_capability('mod/rfidattendance:view', $context);

$PAGE->set_url('/mod/rfidattendance/view.php', ['id' => $id]);
$PAGE->set_title(get_string('pluginname', 'rfidattendance'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();



echo html_writer::tag('button',
  'Neuer Text ohne String!',
  ['id' => 'rfidattendance', 'class' => 'btn btn-primary']
);

$js = <<<JS
  document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("rfidattendance");
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
