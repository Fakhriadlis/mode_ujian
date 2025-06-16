<?php
namespace local_mode_ujian\output;

defined('MOODLE_INTERNAL') || die();

use html_writer;

class renderer extends \plugin_renderer_base {
    /**
     * Render toggle mode ujian.
     *
     * @param int $courseid
     * @param int $status (0 atau 1)
     * @param bool $hascap
     * @return string HTML
     */
    public function render_mode_ujian_toggle($courseid, $status, $hascap = false) {
        global $PAGE;

        if (!$hascap) {
            return '';
        }

      
        $PAGE->requires->js_call_amd('local_mode_ujian/toggle', 'init', [$courseid, $status, $hascap]);
       
        $html = '
        <div class="toggle-inline">
            <span class="toggle-label">Mode Ujian</span>
            <label class="toggle-option">
                <input type="radio" name="exam_mode" value="0" ' . ($status == 0 ? 'checked' : '') . '>
                <span>OFF</span>
            </label>
            <label class="toggle-option">
                <input type="radio" name="exam_mode" value="1" ' . ($status == 1 ? 'checked' : '') . '>
                <span>ON</span>
            </label>
        </div>
        ';

        return html_writer::div($html, '', ['id' => 'mode-ujian-toggle']);
    }
}
