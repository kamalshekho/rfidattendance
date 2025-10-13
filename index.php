<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
redirect(new moodle_url('/mod/rfidattendance/view.php', ['id' => $id]));
