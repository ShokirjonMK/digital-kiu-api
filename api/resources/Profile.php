<?php

namespace api\resources;

use common\models\Profile as CommonProfile;

class Profile extends CommonProfile
{
    
    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'gender'], 'required'],
            [['dob', 'phone', 'phone_secondary'], 'safe'],
        ];
    }



    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            'firstname', 
            'lastname', 
            'gender',
            'dob' => function($model) {
                return date("Y-m-d", strtotime($model->dob));
            }, 
            'phone',
            'phone_secondary',
            'avatar' => 'image'
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
        $extraFields =  [

        ];

        return $extraFields;
    }

}