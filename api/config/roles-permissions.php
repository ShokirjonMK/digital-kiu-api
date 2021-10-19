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
];