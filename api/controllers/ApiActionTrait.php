<?php

namespace api\controllers;

use api\components\HttpBearerAuth;
use app\components\AuthorCheck;
use app\components\PermissonCheck;
use base\ResponseStatus;
use common\models\model\Translate;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

trait ApiActionTrait
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        // User role ining joriy action uchun ruxsati bor yoki yo'qligini tekshiradi.
        $behaviors['permissionCheck'] = [
            'class' => PermissonCheck::class,
            'permission' => $this->getPermission(),
            'allowedRoles' => $this->getAllowedRoles(),
            'referenceControllers' => $this->getReferenceControllers(),
        ];

        // Userning joriy actionga ruxsati bor, lekin mualliflik huquqi bor yoki yo'qligini tekshiradi
        $behaviors['authorCheck'] = [
            'class' => AuthorCheck::class,
        ];

        return $behaviors;
    }

    public function getPermission()
    {
        return Yii::$app->controller->id  . '_' . Yii::$app->controller->action->id;
    }

    public function getAllowedRoles()
    {
        return [];
    }

    public function getReferenceControllers()
    {
        return [
            'nationality',
            'language',
            'science-degree',
            'scientific-title',
            'special-title',
            'basis-of-learning',
            'residence-type',
        ];
    }

    /**
     * Before action
     *
     * @param $action
     * @return void
     */




    public function beforeAction($action)
    {

        // var_dump($action);
        // die();
        // save logs here

        $this->generate_access_key();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->check_access_key()) {
            $data = json_output();
            $data['message'] = 'Incorrect token key!';
            $this->asJson($this->response(0, _e('Incorrect token key! MK'), null, null, ResponseStatus::UNAUTHORIZED));
            return false;
        }

        $lang = Yii::$app->request->get('lang');

        $languages = get_languages();
        $langCodes = [];
        foreach ($languages as $itemLang) {
            $langCodes[] = $itemLang['lang_code'];
        }
        if (!in_array($lang, $langCodes)) {
            $this->asJson($this->response(0, _e('Wrong language code selected (' . $lang . ').'), null, null, ResponseStatus::UPROCESSABLE_ENTITY));
        } else {

            //            $action_logos = new \common\models\model\Action();
            //
            //            $action_logos-> user_id = Yii::$app->user->identity->id();
            //            $action_logos-> controller=Yii::$app->controller->id;
            //            $action_logos-> action=Yii::$app->controller->action->id;
            //            $action_logos-> method= $_SERVER['REQUEST_METHOD'];
            //
            //            var_dump($action_logos);
            //            die();

            Yii::$app->language = $lang;
            return parent::beforeAction($action);
        }
    }


    /**
     * Generate api access key
     *
     * @return void
     */


    public function generate_access_key()
    {
        $api_salt_key = API_SALT_KEY;
        $api_secret_key = API_SECRET_KEY;
        $api_token = $api_salt_key . '-' . $api_secret_key;

        $date1 = gmdate('Y-m-d H:i', strtotime('+1 min'));
        $date2 = gmdate('Y-m-d H:i', strtotime('+2 min'));

        $generated_key_1 = md5($api_token . $date1);
        $generated_key_2 = md5($api_token . $date2);

        $this->token_key = $generated_key_1;
        $this->token_keys = array($generated_key_1, $generated_key_2);
    }

    /**
     * Check api access key
     *
     * @return void
     */

    private function check_access_key()
    {
        return true;
        $token = '';
        $headers = Yii::$app->request->headers;
        $header_token = $headers->get('api-token');
        $param_token = Yii::$app->request->get('token');

        if ($header_token && is_string($header_token)) {
            $token = $header_token;
        }

        if ($param_token && is_string($param_token)) {
            $token = $param_token;
        }

        if (YII_DEBUG && $token == API_MASTER_KEY) {
            return true;
        } elseif ($token && in_array($token, $this->token_keys)) {
            return true;
        }

        return false;
    }

    public function filterAll($query, $model)
    {
        $filter = Yii::$app->request->get('filter');
        $queryfilter = Yii::$app->request->get('filter-like');

        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $model->attributes())) {
                    $query = $query->andFilterWhere([$attribute => $id]);
                }
            }
        }

        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $model->attributes())) {
                    $query = $query->andFilterWhere(['like', $attributeq, '%'.$word.'%', false]);
                }
            }
        }
        return $query;
    }


    public function sort($query)
    {

        if (Yii::$app->request->get('sort')) {

            $sortVal = Yii::$app->request->get('sort');
            if (substr($sortVal, 0, 1) == '-') {
                $sortKey = SORT_DESC;
                $sortField = substr($sortVal, 1);
            } else {
                $sortKey = SORT_ASC;
                $sortField = $sortVal;
            }

            $query->orderBy([$sortField => $sortKey]);
        };

        return $query;
    }

    public function getData($query, $perPage = 20, $validatePage = true)
    {

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
    }

    public function response($status, $message, $data = null, $errors = null, $responsStatusCode = 200)
    {
        Yii::$app->response->statusCode = $responsStatusCode;
        $response = [
            'status' => $status,
            'message' => $message
        ];
        if ($data) $response['data'] = $data;
        if ($errors) $response['errors'] = $errors;
        return $response;
    }

    public function load($model, $data)
    {
        return $model->load($data, '');
    }
}
