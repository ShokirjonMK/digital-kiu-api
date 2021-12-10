<?php
// rollar va dostup
return [
    'chair_admin' => [
        'password_index',
        'password_update',
        'subject_index',
        'subject_create',
        'subject_update',
        'subject_view',
        'subject_delete',
    ],

    'teacher' => [
        'password_index',
        'password_update',
        'subject-topic_index',
        'subject-topic_create',
        'subject-topic_update',
        'subject-topic_view',
        'subject-topic_delete',
    ],

    'directory_editor' => [
        'password_index',
        'password_update',
        'reference_index',
        'reference_create',
        'reference_update',
        'reference_view',
        'reference_delete',

    ],

    'hr' => [
        'password_index',
        'password_update',
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
        'password_index',
        'password_update',
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
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'prorector' => [
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'dekan' => [
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'zamdekan' => [
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'mudir' => [
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    'student' => [
        'password_index',
        'password_update',
        'job_index',
        'time-table_index',
        'time-table_view',

    ],

    'edu_admin' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_moderator' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_editor' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    'edu_viewer' => [
        'password_index',
        'password_update',
        'time-table_index',
        'job_create',
        'job_update',
        'time-table_view',
        'job_delete',
    ],
];
