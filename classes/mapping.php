<?php
namespace mod_rfidattendance;

defined('MOODLE_INTERNAL') || die();

class mapping {
    public static function get_all_for_course($courseid) {
        global $DB;
        // Alle Teilnehmer und Mapping
        $participants = $DB->get_records_sql("
            SELECT u.id, u.firstname, u.lastname
            FROM {user} u
            JOIN {user_enrolments} ue ON ue.userid = u.id
            JOIN {enrol} e ON e.id = ue.enrolid
            WHERE e.courseid = ?
            ORDER BY u.lastname, u.firstname
        ", [$courseid]);

        $uids = $DB->get_records('rfidattendance_mapping', null, '', 'userid, uid');

        foreach ($participants as $id => $user) {
            $user->uid = isset($uids[$id]) ? $uids[$id]->uid : '';
        }

        return $participants;
    }

    public static function save_uid($userid, $uid) {
        global $DB;

        $existing = $DB->get_record('rfidattendance_mapping', ['userid' => $userid]);
        if ($existing) {
            $existing->uid = $uid;
            $DB->update_record('rfidattendance_mapping', $existing);
        } else {
            $record = (object)[
                'userid' => $userid,
                'uid' => $uid
            ];
            $DB->insert_record('rfidattendance_mapping', $record);
        }
    }
}
