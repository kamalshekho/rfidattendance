
<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
  'mod/rfidattendance:addinstance' => [
    'captype' => 'write',
    'contextlevel' => CONTEXT_COURSE,
    'archetypes' => [
      'editingteacher' => CAP_ALLOW,
      'manager' => CAP_ALLOW,
    ],
  ],

  'mod/rfidattendance:view' => [
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'archetypes' => [
      'teacher' => CAP_ALLOW,
      'manager' => CAP_ALLOW,
    ],
  ],
];