<?php
$functions = [
    'local_mode_ujian_get_status' => [
        'classname'   => 'local_mode_ujian\\api',
        'methodname'  => 'get_status',
        'description' => 'Ambil status mode ujian',
        'type'        => 'read',
        'capabilities' => 'moodle/course:view',
        'ajax'        => true,
    ],
    'local_mode_ujian_set_exam_mode' => [
        'classname'   => 'local_mode_ujian\\api',
        'methodname'  => 'set_exam_mode',
        'description' => 'Set status mode ujian',
        'type'        => 'write',
        'capabilities' => 'moodle/course:manageactivities',
        'ajax'        => true,
    ],
];

$services = [
    'mode ujian' => [
        'functions' => [
            'local_mode_ujian_get_status',
            'local_mode_ujian_set_exam_mode',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
