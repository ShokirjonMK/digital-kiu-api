<?php
// rollar va dostup
return [

    /* Rector */
    'rector' => [
        'password_index',
        'password_update',

        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    /* Pro rektor */
    'prorector' => [
        'password_index',
        'password_update',
        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    /* O'quv bo'limi Admin */
    'edu_admin' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    /* O'quv bo'limi kontenthisi */
    'edu_moderator' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    /* O'quv bo'limi nazoratchi */
    'edu_editor' => [
        'password_index',
        'password_update',
        'time-table_index',
        'time-table_create',
        'time-table_update',
        'time-table_view',
        'time-table_delete',
    ],

    /* O'quv bo'limi yordamchi */
    'edu_viewer' => [
        'password_index',
        'password_update',
        'time-table_index',
        'job_create',
        'job_update',
        'time-table_view',
        'job_delete',
    ],

    /* Dekan huquqlari */
    'dean' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',


        'faculty_update',
        'faculty_view',
        'area_index',
        'region_index',
        'country_index',

        'direction_index',
        'direction_create',
        'direction_update',
        'direction_view',
        'direction_delete',

        'edu-plan_index',
        'edu-plan_create',
        'edu-plan_update',
        'edu-plan_view',
        'edu-plan_delete',

        'edu-semestr-subject_index',
        'edu-semestr-subject_create',
        'edu-semestr-subject_update',
        'edu-semestr-subject_view',
        'edu-semestr-subject_delete',

        'edu-semestr_index',
        'edu-semestr_create',
        'edu-semestr_update',
        'edu-semestr_view',
        'edu-semestr_delete',

        'exam_index',
        'exam_create',
        'exam_update',
        'exam_view',
        'exam_delete',

        'kafedra_index',
        'kafedra_create',
        'kafedra_update',
        'kafedra_view',
        'kafedra_delete',

        'student_index',
        'student_create',
        'student_update',
        'student_view',
        'student_delete',


        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],
    /* Dekan huquqlari */

    /* Zam Dekan huquqlari */
    'dean_deputy' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',

        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    /* tutor tarbiyachi huquqlari */
    'tutor' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',

        'user_status-list',
        'languages_index',
        'area_index',
        'countrie_index',
        'region_index',
        'course_index',
        'direction_index',
        'edu-year_index',
        'edu-plan_index',
        'edu-type_index',

        'student_index',
        'student_create',
        'student_update',
        'student_view',
        'student_delete',
    ],

    /* Kafedra mudiri */
    'mudir' => [
        'password_index',
        'password_update',

        'area_index',
        'region_index',
        'country_index',

        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],

    /* Bo'lim boshlig'i */
    'dep_lead' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',

        'department_update',
        'department_view',


        'job_index',
        'job_create',
        'job_update',
        'job_view',
        'job_delete',
    ],


    /* Kadr */
    'hr' => [
        'password_index',
        'password_update',

        'department_index',
        'department_create',
        'department_update',
        'department_view',
        'department_delete',
    ],

    /* Kadr yordamchi */
    'hr_viewer' => [
        'password_index',
        'password_update',
        'department_index',

        'department_view',
    ],

    /* Teacher */
    'teacher' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',

        'exam-teacher-check_index',
        'exam-teacher-check_update',

        'exam-teacher-check_random-students',

        'subject-topic_index',
        'subject-topic_create',
        'subject-topic_update',
        'subject-topic_view',
        'subject-topic_delete',
    ],

    /* Student  */
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

        'exam_index',

        'time-table_index',
        'time-table_view',
        'time-table_parent-null',

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

        'exam-student-answer_get-question',


    ],

];
