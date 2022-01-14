<?php

use yii\db\Migration;

/**
 * Class m200605_201020_init_rbac
 */
class m200605_201020_init_rbac extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add "department_admin" role
        $department_admin = $auth->createRole('department_admin');
        $department_admin->description = 'Department admin';
        $auth->add($department_admin);

        // add "rector" role
        $rector = $auth->createRole('rector');
        $rector->description = 'Rector';
        $auth->add($rector);

        // add "vice_rector" role
        $vice_rector = $auth->createRole('vice_rector');
        $vice_rector->description = 'Vice rector';
        $auth->add($vice_rector);
        
        // add "dean" role
        $dean = $auth->createRole('dean');
        $dean->description = 'Dean of the faculty';
        $auth->add($dean);

        // add "dean_deputy" role
        $dean = $auth->createRole('dean_deputy');
        $dean->description = 'Deputy dean of the faculty';
        $auth->add($dean);
        
        // add "employee" role
        $employee = $auth->createRole('employee');
        $employee->description = 'Employee';
        $auth->add($employee);
        
        // add "student" role
        $student = $auth->createRole('student');
        $student->description = 'Student';
        $auth->add($student);
        
        // add "master" role
        $master = $auth->createRole('master');
        $master->description = 'Master';
        $auth->add($master);
        
        // add "directory_editor" role
        $directory_editor = $auth->createRole('directory_editor');
        $directory_editor->description = 'Directory editor';
        $auth->add($directory_editor);

        // add "chair_admin" role
        $chair_admin = $auth->createRole('chair_admin');
        $chair_admin->description = 'chair_admin';
        $auth->add($chair_admin);

        // add "dean_admin" role
        $dean_admin = $auth->createRole('dean_admin');
        $dean_admin->description = 'dean_admin';
        $auth->add($dean_admin);

        // add "edu_admin" role
        $edu_admin = $auth->createRole('edu_admin');
        $edu_admin->description = 'edu_admin';
        $auth->add($edu_admin);

        // add "teacher" role
        $teacher = $auth->createRole('teacher');
        $teacher->description = 'teacher';
        $auth->add($teacher);

        // add "hr" role
        $hr = $auth->createRole('hr');
        $hr->description = 'hr';
        $auth->add($hr);


        // add "admin" role and give this role the "backendView" permission
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($admin, 1);
        $auth->assign($admin, 2);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
