<?php
// rollar va dostup
return [

    /* Rector */
    'rector' => [
        'password_index',
        'password_update',
        'user_logout',
        'user_me',
        'notification_approved',
        'notification_status-list',


        'area_view',
        'citizenship_view',
        'country_view',


        'notification_create',
        'notification_delete',
        'notification_index',
        'notification_my',

        'notification_update',
        'notification_view',

        'region_view',

        // 'user_view',

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

        // statistic
        'statistic_student-count-by-faculty',


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
        'user_logout',
        'user_me',
        'notification_approved',
        'notification_status-list',

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
        "access-control_permissions",
        "access-control_roles",

        "area_index",
        "area_view",

        "building_create",
        "building_index",
        "building_update",
        "building_view",

        "citizenship_index",

        "country_index",
        "country_view",

        "course_index",
        'course_view',
        'semestr_index',
        'semestr_view',

        "department_index",

        "direction_create",
        "direction_index",
        "direction_update",
        "direction_view",

        "edu-form_create",
        "edu-form_index",
        "edu-form_update",
        "edu-form_view",

        "edu-plan_create",
        "edu-plan_delete",
        "edu-plan_index",
        "edu-plan_update",
        "edu-plan_view",

        "edu-type_index",


        "edu-semestr-subject_create",
        "edu-semestr-subject_delete",
        "edu-semestr-subject_index",
        "edu-semestr-subject_update",
        "edu-semestr-subject_view",

        "edu-semestr_create",
        "edu-semestr_index",
        "edu-semestr_update",
        "edu-semestr_view",

        "edu-year_create",
        "edu-year_index",
        "edu-year_update",
        "edu-year_view",

        "enum_education-degrees",
        "enum_education-types",
        "enum_educations",
        "enum_family-statuses",
        "enum_genders",
        "enum_rates",
        "enum_topic-types",
        "enum_yesno",

        "exam-semeta_index",
        "exams-type_create",
        "exams-type_index",
        "exams-type_update",
        "exams-type_view",

        "exam_create",
        "exam_index",
        "exam_update",
        "exam_view",
        "exam_distribution",
        "exam_ad",

        "faculty_create",
        "faculty_index",
        "faculty_update",
        "faculty_view",

        "kafedra_create",
        "kafedra_index",
        "kafedra_update",
        "kafedra_view",

        "languages_index",

        "nationality_index",

        "notification_approved",
        "notification_index",
        "notification_my",
        "notification_status-list",
        "notification_view",

        "para_create",
        "para_index",
        "para_update",
        "para_view",

        "password_index",
        "password_update",

        "question-type_create",
        "question-type_index",
        "question-type_update",
        "question-type_view",
        "question_index",
        "question_status-update",
        "question_view",
        'question_status-list',

        "region_index",
        "region_view",

        "room_create",
        "room_free",
        "room_index",
        "room_update",
        "room_view",

        "student_create",
        "student_index",
        "student_update",
        "student_view",

        "subject-category_create",
        "subject-category_index",
        "subject-category_update",
        "subject-category_view",

        "subject-type_create",
        "subject-type_index",
        "subject-type_update",
        "subject-type_view",

        "subject_index",
        "subject_view",

        "subject-sillabus_create",
        "subject-sillabus_delete",
        "subject-sillabus_index",
        "subject-sillabus_update",
        "subject-sillabus_view",

        "teacher-access_free",
        "teacher-access_index",
        "teacher-access_view",

        "time-table_create",
        "time-table_delete",
        "time-table_index",
        "time-table_parent-null",
        "time-table_update",
        "time-table_view",

        "user-access-type_index",

        "user_create",
        "user_delete",
        "user_index",
        "user_logout",
        "user_me",
        "user_update",
        "user_view",

        "user_status-list",

        "week_index",
        "week_view"
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
        'notification_approved',
        'notification_status-list',


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
        'department_index',
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
        'notification_approved',
        'notification_status-list',


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
        'department_index',
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
        "access-control_permissions",
        "access-control_roles",
        "area_index",
        "area_view",
        "building_index",
        "building_view",
        "citizenship_index",
        "citizenship_view",
        "country_index",
        "country_view",
        "course_index",
        "department_index",
        "department_types",
        "direction_create",
        "direction_delete",
        "direction_index",
        "direction_update",
        "direction_view",
        "edu-form_index",
        "edu-plan_create",
        "edu-plan_delete",
        "edu-plan_index",
        "edu-plan_update",
        "edu-plan_view",
        "edu-semestr-subject_create",
        "edu-semestr-subject_delete",
        "edu-semestr-subject_index",
        "edu-semestr-subject_update",
        "edu-semestr-subject_view",
        "edu-semestr_create",
        "edu-semestr_delete",
        "edu-semestr_index",
        "edu-semestr_update",
        "edu-semestr_view",
        "edu-type_index",
        "edu-year_index",
        "enum_education-degrees",
        "enum_education-types",
        "enum_educations",
        "enum_family-statuses",
        "enum_genders",
        "enum_rates",
        "enum_topic-types",
        "enum_yesno",
        "exams-type_index",
        "exam_create",
        "exam_delete",
        "exam_index",
        "exam_update",
        "exam_view",
        "faculty_index",
        "faculty_view",
        "kafedra_create",
        "kafedra_delete",
        "kafedra_index",
        "kafedra_update",
        "kafedra_user-access",
        "kafedra_view",
        "languages_index",
        "nationality_index",
        "notification_approved",
        "notification_create",
        "notification_index",
        "notification_my",
        "notification_status-list",
        "notification_view",
        "para_index",
        "para_view",

        "password_index",
        "password_update",
        "password_view",

        "question_index",
        "question_status-update",
        "question_update",
        "question_view",

        'question_status-list',

        "region_index",
        "region_view",
        "room_free",
        "room_index",
        "room_view",

        "semestr_index",
        "semestr_update",
        "semestr_view",

        "student_create",
        "student_delete",
        "student_index",
        "student_update",
        "student_view",

        "subject-category_index",
        "subject-category_view",
        "subject-sillabus_create",
        "subject-sillabus_delete",
        "subject-sillabus_index",
        "subject-sillabus_update",
        "subject-sillabus_view",
        "subject-type_index",
        "subject-type_view",
        "subject_index",
        "subject_view",

        "teacher-access_free",
        "teacher-access_index",
        "teacher-access_view",

        "time-table_create",
        "time-table_delete",
        "time-table_index",
        "time-table_parent-null",
        "time-table_update",
        "time-table_view",

        "user-access-type_index",
        "user-access_create",
        "user-access_delete",
        "user-access_index",
        "user-access_update",
        "user-access_view",
        "user_create",
        "user_delete",
        "user_index",
        "user_logout",
        "user_me",
        "user_status-list",
        "user_update",
        "user_view",

        "week_index",
        "week_view"


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
        'notification_approved',
        'notification_status-list',


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

        'notification_create',
        'notification_index',
        'notification_my',

        'region_index',
        'region_view',

        'nationality_index',
        'nationality_view',

        'languages_index',
        'languages_view',

        'country_index',
        'country_view',

        'area_index',
        'area_view',

    ],

    /* Kafedra mudiri */
    'mudir' => [
        "access-control_permissions",
        "access-control_roles",
        "area_index",
        "area_view",
        "citizenship_view",
        "country_index",
        "country_view",
        "department_index",
        "enum_education-degrees",
        "enum_education-types",
        "enum_educations",
        "enum_family-statuses",
        "enum_genders",
        "enum_rates",
        "enum_topic-types",
        "enum_yesno",
        "faculty_index",
        "kafedra_index",
        "kafedra_view",
        "languages_index",

        "notification_approved",
        "notification_index",
        "notification_my",
        "notification_status-list",
        "notification_view",

        "password_index",
        "password_update",
        "password_view",

        "region_index",
        "region_view",

        "semestr_index",

        'course_index',
        'course_view',

        "subject-category_index",
        "subject-category_view",
        "subject-sillabus_create",
        "subject-sillabus_delete",
        "subject-sillabus_index",
        "subject-sillabus_update",
        "subject-sillabus_view",
        "subject-type_index",
        "subject-type_view",
        "subject_create",
        "subject_delete",
        "subject_index",
        "subject_update",
        "subject_view",

        "user-access-type_index",
        "user_create",
        "user_delete",
        "user_index",
        "user_logout",
        "user_me",
        "user_status-list",
        "user_update",
        "user_view",

        "question_view",
        "question_create",
        "question_index",
        "question_update",
        "question_delete",
        'question_status-list',

        "question-type_index",
        "question-type_view",
        'question_status-update',


        'exam_distribution',
        'exam_view',
        'exam_index',
        'exam-semeta_create',
        'exam-semeta_index',
        'exam-semeta_view',
        'exam-question_index',
        'exam-question_view',

    ],


    /* tutor tarbiyachi huquqlari */
    'tutor' => [
        'password_index',
        'password_update',
        'notification_approved',
        'notification_status-list',

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
        'department_index',
        'department_types',

        'user_logout',
        'user_me',


        "notification_index",
        "notification_my",


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


        'student_import',
    ],


    /* Bo'lim boshlig'i */
    'dep_lead' => [
        'password_index',
        'password_update',
        'notification_approved',
        'notification_status-list',

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
        'notification_approved',
        'notification_status-list',
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
        'notification_approved',
        'notification_status-list',
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
        'question_status-list',
        'course_index',
        'languages_index',

        'notification_index',
        'notification_approved',

        'question_status-update',
        'question-type_index',
        'question_create',
        'question_delete',
        'question_index',
        'question_update',
        'question_view',

        'semestr_index',
        'subject-sillabus_index',
        'subject_index',
        'user_logout',
        'user_me',

        'exam-semeta_index',
        'exam_index',
        'exam_view',

        'exam-student_index',
        'exam-student_update',
        'exam-student_view',

        'exam-teacher-check_index',
        'exam-teacher-check_view',

        // 'exam-student-answer_index',
        // 'exam-student-answer_view',
        // 'exam-student-answer_update',

        'exam-checking_index',
        'exam-checking_view',
        'exam-checking_update',


        // "access-control_roles",
        // "area_view",
        // "citizenship_view",
        // "country_view",
        // "course_index",
        // "course_view",
        // "enum_education-degrees",
        // "enum_education-types",
        // "enum_educations",
        // "enum_family-statuses",
        // "enum_genders",
        // "enum_rates",
        // "enum_topic-types",
        // "enum_yesno",
        // "exam-teacher-check_index",
        // "exam-teacher-check_random-students",
        // "exam-teacher-check_update",
        // "exams-type_index",
        // "exams-type_view",
        // "exam_create",
        // "exam_delete",
        // "exam_generate-passwords",
        // "exam_get-passwords",
        // "exam_index",
        // "exam_update",
        // "exam_view",
        // "languages_index",
        // "languages_view",
        // "notification_approved",
        // "notification_index",
        // "notification_my",
        // "notification_status-list",
        // "notification_view",
        // "password_index",
        // "password_update",
        // "question-option_create",
        // "question-option_delete",
        // "question-option_index",
        // "question-option_update",
        // "question-option_view",
        // "question-type_create",
        // "question-type_delete",
        // "question-type_index",
        // "question-type_update",
        // "question-type_view",
        // "question_create",
        // "question_delete",
        // "question_index",
        // "question_update",
        // "question_view",
        // "region_view",
        // "semestr_index",
        // "semestr_view",
        // "student_index",
        // "student_read",
        // "student_view",
        // "subject-category_index",
        // "subject-category_view",
        // "subject-content_create",
        // "subject-content_delete",
        // "subject-content_index",
        // "subject-content_trash",
        // "subject-content_trash-delete",
        // "subject-content_types",
        // "subject-content_update",
        // "subject-content_view",
        // "subject-sillabus_index",
        // "subject-sillabus_view",
        // "subject-topic_create",
        // "subject-topic_delete",
        // "subject-topic_index",
        // "subject-topic_update",
        // "subject-topic_view",
        // "subject-type_index",
        // "subject-type_view",
        // "subject_index",
        // "subject_view",
        // "time-table_index",
        // "time-table_parent-null",
        // "time-table_view",
        // "user_logout",
        // "user_me",
        // "user_view"
    ],

    /* Student  */
    'student' => [

        "notification_approved",
        "notification_index",
        "notification_my",
        "notification_status-list",
        "notification_view",

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

        'exam-student_index',
        'exam-student_update',
        'exam-student_view',
        //'exam-student-answer_delete',

        'exam-student-answer_get-question',

        'subject-topic_index',
        'subject-content_index',

        'subject-category_index',
    ],

];
