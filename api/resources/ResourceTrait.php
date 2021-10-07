<?php

namespace api\resources;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

trait ResourceTrait
{

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_on',
                'updatedAtAttribute' => 'updated_on',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    
    public function loadApi($data)
    {
        return $this->load($data, '');
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}