<?php
// rollar va dostup
return [

    'student' => [
        'user_me',
        'user_logout',

        'password_index',
        'password_update',
        
        'faculty_view',
        'direction_view',
        'subject_view',
        'languages_view',
        'building_view',
        'room_view',
        'kafedra_view',
        'para_view',
        'edu-year_view',
        'subject-category_view',
        'exams-type_view',
        'edu-type_view',
        'edu-plan_view',
        'edu-semestr_index',
        'edu-semestr_view',
        'edu-semestr-subject_index',
        'edu-semestr-subject_view',
        'edu-semestr-subject-category-time_view',

        'time-table_index',
        'time-table_view',
        'week_index',
        'week_view',
        'region_view',
        'area_view',
        'country_view',
        'semestr_index',
        'semestr_view',
        'course_index',
        'course_view',
        'subject-type_index',
        'subject-type_view',

        'student-time-table_index',
        'student-time-table_create',
        'student-time-table_update',
        'student-time-table_view',
        'student-time-table_delete',

        'student-exam_index',
        'student-exam_create',
        'student-exam_update',
        'student-exam_view',
        'student-exam_delete',

        'exam-student-answer_index',
        'exam-student-answer_create',
        'exam-student-answer_update',
        'exam-student-answer_view',
        'exam-student-answer_delete',
       

    ],

    'teacher' => [
        'password_index',
        'password_update',
        'exam-teacher-check_index',
        'exam-teacher-check_update',


        'subject-topic_index',
        'subject-topic_create',
        'subject-topic_update',
        'subject-topic_view',
        'subject-topic_delete',
    ],

    'chair_admin' => [
        'password_index',
        'password_update',
        'subject_index',
        'subject_create',
        'subject_update',
        'subject_view',
        'subject_delete',
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
