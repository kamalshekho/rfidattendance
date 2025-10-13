<?php
require('../../config.php');
require_login();

use mod_rfidattendance\mapping;

global $DB, $OUTPUT, $PAGE;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('rfidattendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/rfidattendance:view', $context);

$PAGE->set_url('/mod/rfidattendance/view.php', ['id' => $id]);
$PAGE->set_title(get_string('pluginname', 'rfidattendance'));
$PAGE->set_heading($course->fullname);

require_once($CFG->dirroot.'/mod/rfidattendance/classes/mapping.php');

echo $OUTPUT->header();

echo '<ul class="nav nav-tabs" id="rfidTabs" role="tablist">';
$tabs = [
    'sessions'     => 'Sessions',
    'dashboard'    => 'Dashboard',
    'participants' => 'Participants / UID',

];
$first = true;
foreach ($tabs as $idtab => $label) {
    echo '<li class="nav-item" role="presentation">';
    echo '<a class="nav-link '.($first?'active':'').'" id="'.$idtab.'-tab" data-bs-toggle="tab" href="#'.$idtab.'" role="tab">'.$label.'</a>';
    echo '</li>';
    $first = false;
}
echo '</ul>';

echo '<div class="tab-content mt-3" id="rfidTabsContent">';

echo '<div class="tab-pane fade show active" id="participants" role="tabpanel">';
$participants = mapping::get_all_for_course($course->id);

echo '<table class="table table-striped table-bordered">';
echo '<thead><tr><th>Name</th><th>UID</th><th>Action</th></tr></thead>';
echo '<tbody>';
foreach ($participants as $u) {
    $uid = $u->uid ?? '';
    $button = $uid ? '' : '<button class="btn btn-success btn-sm assign-uid" data-userid="'.$u->id.'">Scan New Card</button>';
    echo '<tr>';
    echo '<td>'.$u->firstname.' '.$u->lastname.'</td>';
    echo '<td>'.$uid.'</td>';
    echo '<td>'.$button.'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '</div>';

echo '<div class="tab-pane fade" id="sessions" role="tabpanel">';
echo '<h5>Create New Session</h5>';
echo '<form method="post" action="api_session.php" class="row g-3 mb-3">';
echo '<input type="hidden" name="id" value="'.$cm->id.'">';        
echo '<input type="hidden" name="courseid" value="'.$course->id.'">';
echo '<div class="col-md-3"><input type="date" name="sessiondate" class="form-control" required></div>';
echo '<div class="col-md-3"><input type="time" name="starttime" class="form-control" required></div>';
echo '<div class="col-md-3"><input type="time" name="endtime" class="form-control" required></div>';
echo '<div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Save Session</button></div>';
echo '</form>';

$sessions = $DB->get_records('rfidattendance_sessions', ['courseid'=>$course->id], 'sessiondate DESC');

echo '<table class="table table-striped table-bordered">';
echo '<thead><tr><th>Date</th><th>Start</th><th>End</th><th>Status</th><th>Action</th></tr></thead><tbody>';
foreach ($sessions as $s) {
    $status = $s->active ? 'Active' : 'Inactive';
    echo '<tr>';
    echo '<td>'.date('Y-m-d',$s->sessiondate).'</td>';
    echo '<td>'.date('H:i',$s->starttime).'</td>';
    echo '<td>'.date('H:i',$s->endtime).'</td>';
    echo '<td>'.$status.'</td>';
    echo '<td><a href="view_session.php?id='.$s->id.'" class="btn btn-sm btn-info">Open</a></td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '</div>';


echo '<div class="tab-pane fade" id="dashboard" role="tabpanel">';
echo '<p>Statistics and charts will appear here.</p>';
echo '</div>';

echo '</div>';


$js = <<<JS
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.assign-uid').forEach(button=>{
        button.addEventListener('click', ()=>{
            const userid = button.dataset.userid;
            const uid = prompt('Scan or enter UID:');
            if(uid){
                fetch('api_assign.php',{
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({userid:userid,uid:uid})
                }).then(res=>res.json()).then(data=>{
                    if(data.success){
                        alert('UID assigned successfully!');
                        location.reload();
                    }else{
                        alert('Error: '+data.error);
                    }
                });
            }
        });
    });
});
JS;
$PAGE->requires->js_init_code($js);

echo $OUTPUT->footer();
