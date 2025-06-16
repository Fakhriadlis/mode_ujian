<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Tampilkan toggle mode ujian sebelum footer halaman course.
 */
function local_mode_ujian_before_footer() {
    global $PAGE, $COURSE, $DB, $USER;

    if ($PAGE->pagelayout === 'course' && $COURSE->id != SITEID) {
        $context = context_course::instance($COURSE->id);
        $hascap = has_capability('local/mode_ujian:view', $context, $USER->id);


        $status = 0;
        if ($record = $DB->get_record('local_mode_ujian', ['courseid' => $COURSE->id], 'enabled', IGNORE_MISSING)) {
            $status = $record->enabled;
        }

        $renderer = $PAGE->get_renderer('local_mode_ujian');
        echo $renderer->render_mode_ujian_toggle($COURSE->id, $status, $hascap);
    }
}

/**
 * Batasi akses aktivitas sesuai mode ujian (dipanggil sebelum HTTP header).
 */
function local_mode_ujian_before_http_headers() {
    global $PAGE, $USER, $DB;

    if (!isset($PAGE->cm) || !isloggedin() || isguestuser()) {
        return;
    }
     $PAGE->requires->css('/local/mode_ujian/styles.css');
    $cm = $PAGE->cm;

    $mode = $DB->get_field('local_mode_ujian', 'enabled', ['courseid' => $cm->course], IGNORE_MISSING);
    if (!$mode) {
        return;
    }

    $context = context_module::instance($cm->id);


    if (has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    if ($cm->modname !== 'quiz') {
        return;
    }

 
    $modname = strtolower(trim($cm->name));
    $allowednames = ['uts', 'uas', 'exam', 'ujian'];
    if (!in_array($modname, $allowednames)) {
        print_error('activitynotavailable', 'error');
        exit;
    }
}
