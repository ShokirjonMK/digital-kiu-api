<?php

function _checkRole($roleName)
{
    $user_id = current_user_id();
    $roles = (object)\Yii::$app->authManager->getRoles();

    if (property_exists($roles, $roleName)) {
        return true;
    } else {
        return false;
    }
}

function _eduRoles()
{
    $rolesPermissions = include '../../api/config/roles-permissions.php';
    $data = [];
    foreach ($rolesPermissions as $role => $permissions) {
        $roleExplode = explode('_', $role);
        if ($roleExplode[0] == 'edu')
            $data[] = $role;
    }
    return $data;
}
