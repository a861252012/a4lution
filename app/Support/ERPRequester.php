<?php

namespace App\Support;

use GuzzleHttp\Client;

class ERPRequester
{
    private const EB_ACCOUNT = 'IT2';
    private const EB_PWD = 'AbAO@12';
    
    public function send(
        string $url,
        string $serviceName,
        array  $customParam = []
    ): array
    {
        $ebSoapRequest = $this->genXML(
            json_encode($customParam),
            self::EB_ACCOUNT,
            self::EB_PWD,
            $serviceName
        );

        $client = new Client();

        $res = $client->request(
                'POST',
                $url,
                [
                    'body' => $ebSoapRequest
                ]
            )
            ->getBody()
            ->getContents();

        return json_decode($this->analyzeSOAP($res), true);
    }

    private function genXML(string $paramsJson, string $userName, string $userPass, string $serviceName): string
    {
        return <<< EOF
            <?xml version="1.0" encoding="UTF-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
            <SOAP-ENV:Body>
                <ns1:callService>
                <paramsJson>$paramsJson</paramsJson>
                <userName>$userName</userName>
                <userPass>$userPass</userPass>
                <service>$serviceName</service>
                </ns1:callService>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>
            EOF;
    }

    private function analyzeSOAP(string $soapForm): string
    {
        // converting
        $soapForm = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $soapForm);
        $soapForm = str_replace("SOAP-ENV:", "", $soapForm);
        $soapForm = str_replace("<ns1:callServiceResponse>", "", $soapForm);
        $soapForm = str_replace("</ns1:callServiceResponse>", "", $soapForm);

        // converting to XML
        $parser = simplexml_load_string($soapForm);

        // get response
        return $parser->Body->response->__toString();
    }
}
