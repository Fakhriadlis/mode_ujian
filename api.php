<?php
namespace local_mode_ujian;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_value;
use external_single_structure;
use external_api;

class api extends external_api {

    // Fungsi untuk ambil status mode ujian
    public static function get_status_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
        ]);
    }

    public static function get_status($courseid) {
        global $DB;

        self::validate_parameters(self::get_status_parameters(), ['courseid' => $courseid]);
        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('moodle/course:view', $context);

        $record = $DB->get_record('local_mode_ujian', ['courseid' => $courseid]);

        return ['status' => $record ? (int)$record->enabled : 0];
    }

    public static function get_status_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_INT, '0 = nonaktif, 1 = aktif'),
        ]);
    }

    // Fungsi untuk set mode ujian
    public static function set_exam_mode_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID course'),
            'status' => new external_value(PARAM_INT, '0 = nonaktif, 1 = aktif'),
        ]);
    }

    public static function set_exam_mode($courseid, $status) {
        global $DB;

        self::validate_parameters(self::set_exam_mode_parameters(), [
            'courseid' => $courseid,
            'status' => $status,
        ]);

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        if ($record = $DB->get_record('local_mode_ujian', ['courseid' => $courseid])) {
            $record->enabled = $status;
            $DB->update_record('local_mode_ujian', $record);
        } else {
            $DB->insert_record('local_mode_ujian', [
                'courseid' => $courseid,
                'enabled' => $status,
            ]);
        }

        return ['status' => $status];
    }

    public static function set_exam_mode_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_INT, 'Status setelah disimpan'),
        ]);
    }
}
