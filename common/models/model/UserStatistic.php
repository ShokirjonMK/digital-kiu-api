<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Profile;
use common\models\model\TeacherAccess;
use common\models\model\UserAccessType;

class UserStatistic extends User
{
    use ResourceTrait;

    public function fields()
    {
        $fields = [
            'id',
            'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'teacherAccess' => function ($model) {
                return $model->teacherAccess ?? [];
            },


            'status',
            // 'deleted'

        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields = [
            'created_at',
            'updated_at',
            'profile',
            'userAccess',
            'department',
            'here',

            'roles',
            'rolesAll',

            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',


            'teacherAccess',

        ];

        return $extraFields;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['user_id' => 'id']);
    }

    // UserAccess
    public function getDepartment()
    {
        $data = [];

        // return $this->userAccess;
        foreach ($this->userAccess as $userAccessOne) {
            $user_access_type = $this->userAccess ? UserAccessType::findOne($userAccessOne->user_access_type_id) : null;
            $data[$userAccessOne->user_access_type_id][] =
                $user_access_type ? $user_access_type->table_name::findOne(['id' => $userAccessOne->table_id]) : [];
        }
        return $data;
        // return $this->userAccess->user_access_type_id;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess[0]->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess[0]->table_id]) : [];
    }

    // Dep Kaf Fac
    public function getHere()
    {
        // return $this->userAccess->user_access_type_id;
        $data = [];

        foreach ($this->userAccess as $userAccessOne) {
            $user_access_type = $this->userAccess ? UserAccessType::findOne($userAccessOne->user_access_type_id) : null;
            $data[] =
                $user_access_type ? $user_access_type->table_name::findOne(['id' => $userAccessOne->table_id]) : [];
        }

        return $data;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess[0]->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess[0]->table_id]) : [];
    }
}
