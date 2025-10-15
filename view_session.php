<?php
require('../../config.php');
require_login();

date_default_timezone_set('Europe/Berlin');

global $DB, $OUTPUT, $PAGE;

$id = required_param('id', PARAM_INT);
$session = $DB->get_record('rfidattendance_sessions', ['id'=>$id], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$session->courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);
require_capability('mod/rfidattendance:view', $context);

$PAGE->set_url('/mod/rfidattendance/view_session.php', ['id'=>$id]);
$PAGE->set_title('Session: '.date('Y-m-d',$session->sessiondate));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Participants for this session
$participants = $DB->get_records_sql("
    SELECT u.id, u.firstname, u.lastname, u.email, m.uid
    FROM {user} u
    LEFT JOIN {rfidattendance_mapping} m ON m.userid=u.id
    JOIN {user_enrolments} ue ON ue.userid=u.id
    JOIN {enrol} e ON e.id=ue.enrolid
    WHERE e.courseid=?
    ORDER BY u.lastname, u.firstname
", [$course->id]);

echo '<h4>Session Attendance</h4>';
echo '<table class="table table-striped table-bordered">';
echo '<thead><tr><th>Name</th><th>Email</th><th>UID</th><th>Check-In</th><th>Check-Out</th><th>Status</th><th>Action</th></tr></thead>';
echo '<tbody>';

foreach($participants as $u){
    $uid = $u->uid ?? '';
    $log = $DB->get_record('rfidattendance_logs', [
        'sessiondate'=>$session->sessiondate,
        'userid'=>$u->id,
        'courseid'=>$course->id
    ]);

    $checkin = $log->checkintime ? date('H:i',$log->checkintime) : '';
    $checkout = $log->checkouttime ? date('H:i',$log->checkouttime) : '';
    $status = $log && $log->checkintime ? 'Present' : 'Absent';
    $statusClass = $status === 'Present' ? 'text-success' : 'text-danger';

    $button = $uid ? '' : '<button class="btn btn-success btn-sm assign-uid" data-userid="'.$u->id.'">Scan New Card</button>';

    echo '<tr>';
    echo '<td>'.$u->firstname.' '.$u->lastname.'</td>';
    echo '<td>'.$u->email.'</td>';
    echo '<td>'.$uid.'</td>';
    echo '<td>'.$checkin.'</td>';
    echo '<td>'.$checkout.'</td>';
    echo '<td class="'.$statusClass.'">'.$status.'</td>';
    echo '<td>'.$button.'</td>';
    echo '</tr>';
}
echo '</tbody></table>';

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
