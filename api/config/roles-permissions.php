<?php
// rollar va dostup
return [
    'chair_admin' => [
        'subject_index',
        'subject_create',
        'subject_update',
        'subject_view',
        'subject_delete',
    ],

    'teacher' => [
        'subject-topic_index',
        'subject-topic_create',
        'subject-topic_update',
        'subject-topic_view',
        'subject-topic_delete',
    ],

    'directory_editor' => [
        'reference_index',
        'reference_create',
        'reference_update',
        'reference_view',
        'reference_delete',

    ],

    'hr' => [
        'department_index',
        'department_create',
        'department_update',
        'department_view',
        'department_delete',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'hr_viewer' => [
        'department_index',
        'department_create',
        'department_update',
        'department_view',
        'department_delete',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'rector' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'prorector' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'dekan' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'zamdekan' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'mudir' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'student' => [
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'edu_admin' => [
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_moderator' => [
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_editor' => [
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_viewer' => [
        'time-table_index',
        'job_create',
        'job_update',
        'time-table_view',
        'job_delete',
    ],
];