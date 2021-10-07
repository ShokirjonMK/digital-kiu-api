<?php 

$controllers = [
    'student',
    'employee',
    'user',
    'department',
    'job',
    'direction',
    'subject',
    'subject-topic',
    'nationality',
    'language',
    'residence-type',
    'science-degree',
    'scientific-title',
    'special-title',
    'basis-of-learning',
];

$controllerRoutes = [];

foreach ($controllers as $controller) {
    $rule = [
        'class' => 'yii\rest\UrlRule', 
        'controller' => $controller,
        'prefix' => '<lang:\w{2}>'
    ];
    if($controller == 'basis-of-learning'){
        $rule['pluralize'] = false;
    }
    $controllerRoutes[] = $rule;
}

$routes = [

    'POST <lang:\w{2}>/employees/<employee_id:\d+>/bind-subject' => 'employee/bind-subject', // Bind subjects to teacher
    'GET <lang:\w{2}>/employees/<employee_id:\d+>/subjects' => 'employee/subjects', // Get teacher subjects
    
    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login', 

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role

    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list
    // ***

    'GET <lang:\w{2}>/user-statuses' => 'user/status-list', // Get user statuses

    // Enums
    'GET <lang:\w{2}>/genders' => 'enum/genders',
    'GET <lang:\w{2}>/educations' => 'enum/educations',
    'GET <lang:\w{2}>/education-degrees' => 'enum/education-degrees',
    'GET <lang:\w{2}>/disability-groups' => 'enum/disability-groups',
    'GET <lang:\w{2}>/education-types' => 'enum/education-types',
    'GET <lang:\w{2}>/family-statuses' => 'enum/family-statuses',
    'GET <lang:\w{2}>/rates' => 'enum/rates',
    'GET <lang:\w{2}>/topic-types' => 'enum/topic-types',
    'GET <lang:\w{2}>/yesno' => 'enum/yesno',
    // ***

];

return array_merge($controllerRoutes, $routes);