<?php
require_once('../../config.php');
require_login();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
$courseid = required_param('courseid', PARAM_INT);
$status = required_param('status', PARAM_INT);

$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

require_once($CFG->libdir . '/modinfolib.php');
require_once($CFG->dirroot . '/course/lib.php');

$modinfo = get_fast_modinfo($courseid);
$hide = $status == 1;


$tablename = 'local_mode_ujian_hidden';

if ($hide) {

    foreach ($modinfo->get_cms() as $cm) {
        if (!$cm->uservisible || !$cm->visible) {
            continue; 
        }

        if ($cm->modname === 'quiz') {
            $quiz = $DB->get_record('quiz', ['id' => $cm->instance], 'name', IGNORE_MISSING);
            if ($quiz && preg_match('/\b(uts|uas|exam|ujian)\b/i', $quiz->name)) {
                continue; 
            }
        }

        set_coursemodule_visible($cm->id, 0);
        $DB->insert_record($tablename, (object)[
            'courseid' => $courseid,
            'coursemoduleid' => $cm->id,
            'timecreated' => time()
        ]);
    }

} else {
    
    $hiddenrecords = $DB->get_records($tablename, ['courseid' => $courseid]);

    foreach ($hiddenrecords as $record) {
        set_coursemodule_visible($record->coursemoduleid, 1);
    }

    $DB->delete_records($tablename, ['courseid' => $courseid]);
}


$record = $DB->get_record('local_mode_ujian', ['courseid' => $courseid], '*', IGNORE_MISSING);
if ($record) {
    $record->enabled = $status;
    $record->timemodified = time();
    $DB->update_record('local_mode_ujian', $record);
} else {
    $newrecord = (object)[
        'courseid' => $courseid,
        'enabled' => $status,
        'timecreated' => time(),
        'timemodified' => time(),
    ];
    $DB->insert_record('local_mode_ujian', $newrecord);
}
cache_helper::purge_by_event('changesincourse');
rebuild_course_cache($courseid);
echo json_encode(['success' => true]);
