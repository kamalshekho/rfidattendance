<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_rfidattendance_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025101002) {

        $table = new xmldb_table('rfidattendance_mapping');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('uid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('idx_uid', XMLDB_INDEX_UNIQUE, ['uid']);
            $table->add_index('idx_userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

            $dbman->create_table($table);
        }

        $table = new xmldb_table('rfidattendance_logs');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('sessiondate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('checkintime', XMLDB_TYPE_INTEGER, '10', null, null);
            $table->add_field('checkouttime', XMLDB_TYPE_INTEGER, '10', null, null);
            $table->add_field('uid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('idx_uid', XMLDB_INDEX_NOTUNIQUE, ['uid']);
            $table->add_index('idx_user_course_date', XMLDB_INDEX_NOTUNIQUE, ['userid','courseid','sessiondate']);

            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2025101002, 'rfidattendance');
    }

    return true;
}
