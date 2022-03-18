<?php

namespace api\components;

use common\models\IpsGovUz;
use common\models\IpsService;
use common\models\Logging;
use phpDocumentor\Reflection\Types\False_;
use Yii;
use SoapClient;
use SoapFault;
use yii\console\Exception;

class MipService
{
    public $user_number = '';
    public $numbers_array = [];
    protected $username = 'M_fM1f5fxdS0XXjBBLBH79cJ8kIa';
    protected $password = 'VhOOBPrpPiPI_G00cls0ENP5frUa';

    public function getToken($type = null)
    {
        $url = 'https://iskm.egov.uz:9444/oauth2/token?grant_type=password&username=justice-user2&password=KN8akqXsEg';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // headers
        $headers = array(
            "Authorization: Basic TV9mTTFmNWZ4ZFMwWFhqQkJMQkg3OWNKOGtJYTpWaE9PQlBycFBpUElfRzAwY2xzMEVOUDVmclVh",
            'Content-Type: application/json',
            'Content-Length: 0',
            'Accept: application/json'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // Authorization set basic auth
        curl_setopt($mk_curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // Basic Auth username and password
        curl_setopt($mk_curl, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        // curl execute (get response)
        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);
            if ($type == "token" || $type == "access_token") {
                return $getContentJson->access_token;
            } elseif ($type == "token_type") {
                return $getContentJson->token_type;
            } elseif ($type == "refresh_token") {
                return $getContentJson->refresh_token;
            } elseif ($type == "expires_in" || $type == "expired" || $type == "expired_at") {
                return $getContentJson->expires_in;
            }
            return $getContentJson;
        }
    }

    function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }


    protected function errors($response)
    {
        if (property_exists($response, 'Result')) {
            if ($response->Result != 1) {
                return false;
            }
        }
        if (property_exists($response, 'PinppAddressResult')) {
            if (property_exists($response->PinppAddressResult, 'AnswereId')) {
                if ($response->PinppAddressResult->AnswereId != 1) {
                    return false;
                }
            }
        }
        return true;
    }


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
