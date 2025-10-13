
<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
  'mod/simplebutton:addinstance' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_COURSE,
    'archetypes' => [
      'editingteacher' => CAP_ALLOW,
      'manager' => CAP_ALLOW,
    ],
  ],

  'mod/simplebutton:view' => [
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'archetypes' => [
      'student' => CAP_ALLOW,
      'teacher' => CAP_ALLOW,
      'manager' => CAP_ALLOW,
    ],
  ],
];