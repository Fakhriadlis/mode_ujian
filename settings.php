<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { 
    $settings = new admin_settingpage('local_mode_ujian', get_string('pluginname', 'local_mode_ujian'));

    $settings->add(new admin_setting_configcheckbox(
        'local_mode_ujian/enabled',
        get_string('modeujian', 'local_mode_ujian'),
        get_string('modedesc', 'local_mode_ujian'),
        1
    ));

    $ADMIN->add('localplugins', $settings);

}
