<?php

$controllers = [
    'get-teacher',

    'student',
    'employee',
    'user',
    'department',
    'job',
    'subject',
    'subject-topic',
    'nationality',
    'languages',
    'residence-type',
    'science-degree',
    'scientific-title',
    'special-title',
    'basis-of-learning',
    'building',
    'course',
    'room',
    'course',
    'direction',
    'faculty',
    'kafedra',
    'para',
    'semestr',
    'edu-year',
    'subject',
    'subject-type',
    'subject-category',
    'exams-type',
    'edu-type',
    'edu-form',
    'edu-plan',
    'edu-semestr',
    'edu-semestr-exams-type',
    'edu-semestr-subject',
    'edu-semestr-subject-category-time',
    'teacher-access',
    'time-table',
    'password',
    'translate',
    'week',
    'region',
    'area',
    'student-time-table',
    'student-exam',
    'country',

    'exam',
    'exam-student',
    'exam-question',
    'exam-question-type',
    'exam-question-option',
    'exam-student-answer',
    'exam-teacher-check',

    'subject-sillabus',

    'question',
    'question-type',
    'question-option',
    'exam-semeta',

    'user-access-type',
    'user-access',

    'subject-access',

    'subject-topic',
    'subject-content',
    'citizenship',
    'notification',
    'notification-role',
    'nationality',
    'category-of-cohabitant',
    'residence-status',
    'social-category',
    'student-category',



    'teacher-checking-type',
    'statistic',




    'telegram',
    'test-get-data',
];

$controllerRoutes = [];

foreach ($controllers as $controller) {
    $rule = [
        'class' => 'yii\rest\UrlRule',
        'controller' => $controller,
        'prefix' => '<lang:\w{2}>'
    ];
    if ($controller == 'basis-of-learning') {
        $rule['pluralize'] = false;
    }
    $controllerRoutes[] = $rule;
}

$routes = [

    /* statistics all */
    // statistic student-count-by-faculty
    'GET <lang:\w{2}>/statistics/student-count-by-faculty' => 'statistic/student-count-by-faculty',
    // statistic QuestionsByKafedra
    'GET <lang:\w{2}>/statistics/questions-by-kafedra' => 'statistic/questions-by-kafedra',

    /* statistics all */

    // Question status update
    'PUT <lang:\w{2}>/questions/status-update/<id>' => 'question/status-update',

    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login',
    // Get me
    'GET <lang:\w{2}>/users/me' => 'user/me',
    // Log out
    'POST <lang:\w{2}>/auth/logout' => 'user/logout',

    // TimeTable parent null
    'GET <lang:\w{2}>/time-tables/parent-null' => 'time-table/parent-null',

    // Exam Passwords
    'POST <lang:\w{2}>/exams/get-passwords' => 'exam/get-passwords',
    // Exam Passwords
    'POST <lang:\w{2}>/exams/generate-passwords' => 'exam/generate-passwords',
    // exam Distribution
    'GET <lang:\w{2}>/exams/<id>/distribution' => 'exam/distribution',

    // Department type list
    'GET <lang:\w{2}>/departments/types' => 'department/types',

    // studentga savollarni random tushirish
    'POST <lang:\w{2}>/exam-student-answers/get-question' => 'exam-student-answer/get-question',

    // teacherga studentlarni random tushirish
    'POST <lang:\w{2}>/exam-teacher-check/random-students' => 'exam-teacher-check/random-students',

    // Subject Content Trash ( get Deleted Content)
    'GET <lang:\w{2}>/subject-contents/trash' => 'subject-content/trash',
    // Subject Content Delete from Trash ( get Deleted Content)  bazadan o'chirish
    'DELETE <lang:\w{2}>/subject-contents/trash/<id>' => 'subject-content/trash-delete',
    // Subject Content type list
    'GET <lang:\w{2}>/subject-contents/types' => 'subject-content/types',

    // Faculty UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/faculties/user-access' => 'faculty/user-access',
    // Kafedra UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/kafedras/user-access' => 'kafedra/user-access',
    // Department UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/departments/user-access' => 'department/user-access',


    /** Free teachers for time tables */
    'GET <lang:\w{2}>/teacher-accesses/free' => 'teacher-access/free',
    'POST <lang:\w{2}>/rooms/free' => 'room/free',
    /**  */

    // Student Import
    'POST <lang:\w{2}>/students/import' => 'student/import',
    // 'POST <lang:\w{2}>/students/read' => 'student/read',

    // My Notifications
    'GET <lang:\w{2}>/notifications/my' => 'notification/my',
    // Notifications Status list
    'GET <lang:\w{2}>/notifications/status-list' => 'notification/status-list',
    // Notifications Approved (tasdiqlavoring)
    'PUT <lang:\w{2}>/notifications/approved/<id>' => 'notification/approved',

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role
    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list
    // ***

    'GET <lang:\w{2}>/user-statuses' => 'user/status-list', // Get user statuses

    /* Enums */
    'GET <lang:\w{2}>/genders' => 'enum/genders',
    'GET <lang:\w{2}>/educations' => 'enum/educations',
    'GET <lang:\w{2}>/education-degrees' => 'enum/education-degrees',
    'GET <lang:\w{2}>/disability-groups' => 'enum/disability-groups',
    'GET <lang:\w{2}>/education-types' => 'enum/education-types',
    'GET <lang:\w{2}>/family-statuses' => 'enum/family-statuses',
    'GET <lang:\w{2}>/rates' => 'enum/rates',
    'GET <lang:\w{2}>/topic-types' => 'enum/topic-types',
    'GET <lang:\w{2}>/yesno' => 'enum/yesno',
    /* Enums */
];

return array_merge($controllerRoutes, $routes);
