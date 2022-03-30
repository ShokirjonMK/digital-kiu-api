<?php

namespace api\controllers;

use api\components\MipService;
use api\components\PersonDataHelper;
use common\models\model\TeacherAccess;
use Yii;
use base\ResponseStatus;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TestGetData';

    public function actions()
    {
        return [];
    }

    public function actionIndex($passport = null, $jshir = null)
    {

       /*  $mk = new MipService();

        return  $mk->getToken();
        return json_decode($mk->getToken()); */

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new PersonDataHelper();
        //  $data = $model->services($jshir, $passport);
        $data = $model->services("30505985280023", "AA7231228");
        if (empty($data)) {
            return 'error-no';
        } else {
            return $data;
        }
    }

    public function actionView()
    {
    }

    public function actionJavacode()
    {
        /*
        OkHttpClient client = Utils.getUnsafeOkHttpClient();
        MediaType JSON = MediaType.parse("application/json;charset=utf-8");
        JSONObject actual = new JSONObject();
        actual.put("pinfl", pinfl);
        okhttp3.RequestBody body = okhttp3.RequestBody.create(JSON, actual.toString());
        Request request = new Request.Builder()
                .addHeader("Authorization", "Bearer " + employeeInfoService.token())
                .url("https://apimgw.egov.uz:8243/minvuz/services/diploma/v1")
                .post(body)
                .build();
        JSONObject jsonObject;
        String s = "Ma'lumot topilmapti...";
        try {
            Response response = client.newCall(request).execute();
            s = response.body().string();
            jsonObject = new JSONObject(s);
        } catch (IOException e) {
            e.printStackTrace();
            return new ResponseEntity(s, OK);
        }

        public String token() {
        OkHttpClient client = Utils.getUnsafeOkHttpClient();
        MediaType JSON = MediaType.parse("application/json;charset=utf-8");
        RequestBody body = RequestBody.create(JSON, "");
        Request request = new Request.Builder()
                .header("Content-Type", "application/json")
                .header("Authorization", "Basic TV9mTTFmNWZ4ZFMwWFhqQkJMQkg3OWNKOGtJYTpWaE9PQlBycFBpUElfRzAwY2xzMEVOUDVmclVh")
                .url("https://iskm.egov.uz:9444/oauth2/token?grant_type=password&username=justice-user2&password=KN8akqXsEg")
                .method("POST", body)
                .build();
        String object = null;
        try {
            Response responseInn = client.newCall(request).execute();
            JSONObject json = new JSONObject(responseInn.body().string());
            object = json.getString("access_token");
        } catch (IOException e) {
            e.printStackTrace();
        }
        return object;
    }
    */
    }
}
