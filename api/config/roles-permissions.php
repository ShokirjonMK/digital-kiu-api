<?php
// rollar va dostup
return [

    /* Rector */
    'rector' => [
        'password_index',
        'password_update',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////



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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',
        /**
         * 
         */
        'languages_index',
        'teacher-access_index',
        'citizenship_index',
        'nationality_index',
        'course_index',
        'area_index',
        'region_index',
        'country_index',
        'area_view',
        'region_view',
        'country_view',

        'user-access-type_index',


        'building_index',
        'building_create',
        'building_update',
        'building_view',

        'room_index',
        'room_create',
        'room_update',
        'room_view',

        'edu-form_index',
        'edu-form_create',
        'edu-form_update',
        'edu-form_view',

        'para_index',
        'para_create',
        'para_update',
        'para_view',

        'exam_index',
        'exam_create',
        'exam_update',
        'exam_view',

        'exams-type_index',
        'exams-type_create',
        'exams-type_update',
        'exams-type_view',

        'subject-type_index',
        'subject-type_create',
        'subject-type_update',
        'subject-type_view',

        'subject-category_index',
        'subject-category_create',
        'subject-category_update',
        'subject-category_view',

        'edu-year_index',
        'edu-year_create',
        'edu-year_update',
        'edu-year_view',

        'edu-semestr_index',
        'edu-semestr_create',
        'edu-semestr_update',
        'edu-semestr_view',

        'question-type_index',
        'question-type_create',
        'question-type_update',
        'question-type_view',

        'direction_index',
        'direction_create',
        'direction_update',
        'direction_view',

        'faculty_index',
        'faculty_create',
        'faculty_update',
        'faculty_view',

        'kafedra_index',
        'kafedra_create',
        'kafedra_update',
        'kafedra_view',

        'student_index',
        'student_create',
        'student_update',
        'student_view',

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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////

        /**1 */


        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',
        /**
         * 
         */
        'languages_index',
        'teacher-access_index',
        'citizenship_index',
        'nationality_index',
        'course_index',
        'area_index',
        'region_index',
        'country_index',
        'area_view',
        'region_view',
        'country_view',

        'user-access-type_index',


        'building_index',
        'building_create',
        'building_update',
        'building_view',

        'room_index',
        'room_create',
        'room_update',
        'room_view',

        'edu-form_index',
        'edu-form_create',
        'edu-form_update',
        'edu-form_view',

        'para_index',
        'para_create',
        'para_update',
        'para_view',

        'exam_index',
        'exam_create',
        'exam_update',
        'exam_view',

        'exams-type_index',
        'exams-type_create',
        'exams-type_update',
        'exams-type_view',

        'subject-type_index',
        'subject-type_create',
        'subject-type_update',
        'subject-type_view',

        'subject-category_index',
        'subject-category_create',
        'subject-category_update',
        'subject-category_view',

        'edu-year_index',
        'edu-year_create',
        'edu-year_update',
        'edu-year_view',

        'edu-semestr_index',
        'edu-semestr_create',
        'edu-semestr_update',
        'edu-semestr_view',

        'question-type_index',
        'question-type_create',
        'question-type_update',
        'question-type_view',

        'direction_index',
        'direction_create',
        'direction_update',
        'direction_view',

        'faculty_index',
        'faculty_create',
        'faculty_update',
        'faculty_view',

        'kafedra_index',
        'kafedra_create',
        'kafedra_update',
        'kafedra_view',

        'student_index',
        'student_create',
        'student_update',
        'student_view',


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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////

        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',
        /**
         * 
         */
        'languages_index',
        'teacher-access_index',
        'citizenship_index',
        'nationality_index',
        'course_index',
        'area_index',
        'region_index',
        'country_index',
        'area_view',
        'region_view',
        'country_view',

        'user-access-type_index',


        'building_index',
        'building_create',
        'building_update',
        'building_view',

        'room_index',
        'room_create',
        'room_update',
        'room_view',

        'edu-form_index',
        'edu-form_create',
        'edu-form_update',
        'edu-form_view',

        'para_index',
        'para_create',
        'para_update',
        'para_view',

        'exam_index',
        'exam_create',
        'exam_update',
        'exam_view',

        'exams-type_index',
        'exams-type_create',
        'exams-type_update',
        'exams-type_view',

        'subject-type_index',
        'subject-type_create',
        'subject-type_update',
        'subject-type_view',

        'subject-category_index',
        'subject-category_create',
        'subject-category_update',
        'subject-category_view',

        'edu-year_index',
        'edu-year_create',
        'edu-year_update',
        'edu-year_view',

        'edu-semestr_index',
        'edu-semestr_create',
        'edu-semestr_update',
        'edu-semestr_view',

        'question-type_index',
        'question-type_create',
        'question-type_update',
        'question-type_view',

        'direction_index',
        'direction_create',
        'direction_update',
        'direction_view',

        'faculty_index',
        'faculty_create',
        'faculty_update',
        'faculty_view',

        'kafedra_index',
        'kafedra_create',
        'kafedra_update',
        'kafedra_view',

        'student_index',
        'student_create',
        'student_update',
        'student_view',

    ],

    /* Dekan huquqlari */
    'dean' => [
        'password_index',
        'password_update',
        'password_view',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',

        'languages_index',


        'exams-type_index',
        'teacher-access_index',
        'edu-type_index',
        'edu-form_index',
        'edu-year_index',
        'citizenship_index',
        'nationality_index',
        'course_index',
        'faculty_index',
        //'faculty_update',
        'faculty_view',
        'area_index',
        'region_index',
        'country_index',
        'area_view',
        'region_view',
        'country_view',

        'user-access-type_index',



        'direction_index',
        'direction_create',
        'direction_update',
        'direction_view',
        'direction_delete',


        'semestr_index',
        // 'semestr_create',
        'semestr_update',
        'semestr_view',
        // 'semestr_delete',

        'subject_index',
        'subject_create',
        'subject_update',
        'subject_view',
        'subject_delete',

        'subject-category_index',
        // 'subject-category_create',
        // 'subject-category_update',
        'subject-category_view',
        // 'subject-category_delete',

        'subject-type_index',
        // 'subject-type_create',
        // 'subject-type_update',
        'subject-type_view',
        // 'subject-type_delete',

        'subject-sillabus_index',
        'subject-sillabus_create',
        'subject-sillabus_update',
        'subject-sillabus_view',
        'subject-sillabus_delete',

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


        'user-access_index',
        'user-access_create',
        'user-access_update',
        'user-access_view',
        'user-access_delete',


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



    ],
    /* Dekan huquqlari */

    /* Zam Dekan huquqlari */
    'dean_deputy' => [
        'password_index',
        'password_update',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


        'user_logout',
        'user_me',

        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',

        'user-access_index',
        'user-access_create',
        'user-access_update',
        'user-access_view',
        'user-access_delete',


    ],

    /* Kafedra mudiri */
    'mudir' => [
        'password_index',
        'password_update',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        'user_logout',
        'user_me',
        ////


        'faculty_index',
        'kafedra_index',
        'kafedra_view',
        'languages_index',

        'user-access-type_index',
        'semestr_index',
        'subject-category_index',
        'subject-category_view',
        'subject-type_index',
        'subject-type_view',


        'area_index',
        'region_index',
        'country_index',


        'user_status-list',
        'user_index',
        'user_create',
        'user_update',
        'user_view',
        'user_delete',



        'subject_index',
        'subject_create',
        'subject_update',
        'subject_view',
        'subject_delete',

        'subject-sillabus_index',
        'subject-sillabus_create',
        'subject-sillabus_update',
        'subject-sillabus_view',
        'subject-sillabus_delete',


    ],


    /* tutor tarbiyachi huquqlari */
    'tutor' => [
        'password_index',
        'password_update',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////
        'faculty_index',

        'user_logout',
        'user_me',

        'user_status-list',
        'languages_index',
        'area_index',
        'country_index',
        'region_index',
        'course_index',
        'direction_index',
        'edu-year_index',
        'citizenship_index',
        'nationality_index',

        'edu-plan_index',
        'edu-type_index',
        'edu-form_index',

        'student_index',
        'student_create',
        'student_update',
        'student_view',
        'student_delete',


        'student_read',
    ],


    /* Bo'lim boshlig'i */
    'dep_lead' => [
        'password_index',
        'password_update',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


        'user_logout',
        'user_me',

        'department_update',
        'department_view',



    ],


    /* Kadr */
    'hr' => [
        'password_index',
        'password_update',
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////



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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


        'department_index',

        'department_view',
    ],

    /* Teacher */
    'teacher' => [
        'password_index',
        'password_update',
        // 'access-control_roles',
        // 'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////


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
        'access-control_roles',
        'access-control_permissions',
        'enum_education-degrees',
        'enum_education-types',
        'enum_educations',
        'enum_family-statuses',
        'enum_genders',
        'enum_rates',
        'enum_topic-types',
        'enum_yesno',
        ////



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
