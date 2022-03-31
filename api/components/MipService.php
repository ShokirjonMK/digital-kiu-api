<?php

namespace api\components;

use SoapClient;


class MipService
{
    public $user_number = '';
    public $numbers_array = [];


    public static function getPhotoService1()
    {

        $pinpp = "30111975890051";
        $doc_give_date = "2014-12-09";


        $data = null;
        $url = 'https://apimgw.egov.uz:8243/gcp/photoservice/v1';

        $form = $this->serviceForm($this->service_name, $pinfl, $passport);

        try {
            $params = [
                'verifypeer' => false,
                'verifyhost' => false,
                //                http://10.190.2.36
                //                http://10.0.42.3:9444
                //                http://10.0.42.3:8243
                // 'host' => '10.0.42.3',
                // 'port' => '9444',
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ])
            ];

            $soap = new SoapClient($url, $params);
            $response = $soap->__soapCall($function, $form);
            $array = [];
            if (!$this->errors($response)) {
                return $array;
            } else {
                if ($this->service_name == 'passport_info') {
                    $data = simplexml_load_string($response->Data);
                } else {
                    $data = $response;
                }
                if ($this->service_name == 'passport_info') {
                    $data = (array)$data;
                    $row = (array)$data['row'];
                    $array['document'] = $row['document'];
                    $array['surname_latin'] = $row['surname_latin'];
                    $array['name_latin'] = $row['name_latin'];
                    $array['patronym_latin'] = $row['patronym_latin'];
                    // $array['surname_engl'] = $row['surname_engl'];
                    //    $array['name_engl'] = $row['name_engl'];
                    $array['birth_date'] = $row['birth_date'];
                    $array['birth_place'] = $row['birth_place'];
                    //    $array['birth_country'] = $row['birth_country'];
                    $array['nationality'] = $row['nationality'];
                }
                if ($this->service_name == "address_by_prop") {
                    $array['propiska_region'] = $data->PinppAddressResult->Data->PermanentRegistration->Region->Value;
                    $array['propiska_tuman'] = $data->PinppAddressResult->Data->PermanentRegistration->District->Value;
                    //    $array['propis_country'] = $data->PinppAddressResult->Data->PermanentRegistration->Country->Value;
                    $array['Cadastre'] = $data->PinppAddressResult->Data->PermanentRegistration->Cadastre;
                    $array['Address'] = $data->PinppAddressResult->Data->PermanentRegistration->Address;
                    $array['RegistrationDate'] = $data->PinppAddressResult->Data->PermanentRegistration->RegistrationDate;
                }
            }
            return $array;
        } catch (SoapFault $soapFault) {
            $service = IpsService::find()->where(['like', 'service_name', $this->service_name])->one();
            if ($service) {
                //  $service->is_working = 0;
                $service->save(false);
            }
        }




        $xml = "<?xml version='1.0' encoding=\"utf-8\"?>
                        <DataCEPRequest>
                             <pinpp>$pinpp</pinpp>
                             <document>$doc_give_date</document>
                             <langId>3</langId>
                        </DataCEPRequest>";
        $array = [
            'AuthInfo' => [
                'WS_ID' => '',
                'LE_ID' => '',
            ],
            'Data' => $xml,
            'Signature' => '',
            'PublicCert' => '',
            'SignDate' => '',
        ];
    }
    public static function getPhotoService()
    {

        $pinpp = "30111975890051";
        $doc_give_date = "2014-12-09";


        $xmlMK = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:idm="http://fido.com/IdmsEGMICServices">
   <soapenv:Header/>
   <soapenv:Body>
      <idm:GetDataByPinppRequest>
         <idm:Data><![CDATA[<?xml version="1.0"?>
         <DataByPinppRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="file:///d:/STS/workspaceEASU/IdmsEGMICServices/src/main/resources/xsdData/GetDatabyDoc.xsd">
         <pinpp>' . $pinpp . '</pinpp>
         <doc_give_date>' . $doc_give_date . '</doc_give_date>
         <langId>1</langId>
         <is_consent_pers_data>Y</is_consent_pers_data>
         </DataByPinppRequest>]]></idm:Data>
         <idm:Signature></idm:Signature>
         <idm:PublicCert></idm:PublicCert>
         <idm:SignDate></idm:SignDate>
      </idm:GetDataByPinppRequest>
   </soapenv:Body>
</soapenv:Envelope>';


        $url = 'https://apimgw.egov.uz:8243/gcp/photoservice/v1';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        $token = MipTokenGen::getToken('token');
        // headers
        $headers = array(
            "Authorization: Bearer $token",
            'Content-Type: text/json',
            'Content-Length: 0',
            'Accept: */*'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // Authorization set basic auth
        // curl_setopt($mk_curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // Basic Auth username and password
        // curl_setopt($mk_curl, CURLOPT_USERPWD, $username . ":" . $password);
        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($mk_curl, CURLOPT_POSTFIELDS, $xmlMK);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        // curl execute (get response)


        if (curl_errno($mk_curl)) {
            // moving to display page to display curl errors
            echo curl_errno($mk_curl);
            echo curl_error($mk_curl);
        } else {
            //getting response from server
            $response = curl_exec($mk_curl);
            curl_close($mk_curl);
            return $response;
        }



        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {

            return $response;


            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
    }

    protected function serviceForm($pinfl, $passport)
    {
        $array = [];

        $xml = "<?xml version='1.0' encoding=\"utf-8\"?>
                        <DataCEPRequest>
                             <pinpp>$pinfl</pinpp>
                             <document>$passport</document>
                             <langId>3</langId>
                        </DataCEPRequest>";
        $array = [
            'AuthInfo' => [
                'WS_ID' => '',
                'LE_ID' => '',
            ],
            'Data' => $xml,
            'Signature' => '',
            'PublicCert' => '',
            'SignDate' => '',
        ];


        $result = [
            'DataCEPRequest' => $array
        ];

        return $result;
    }
}
