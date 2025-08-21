<?php

namespace api\resources;

use common\models\model\Circle as CommonCircle;
use common\models\model\Translate;
use Yii;

class Circle extends CommonCircle
{
    use ResourceTrait;

    public function getInfoRelation()
    {
        self::$selected_language = Yii::$app->request->get('lang') ?? 'uz';
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public function getInfo()
    {
        return ($this->infoRelation) ? $this->infoRelation[0] : null;
    }
}

