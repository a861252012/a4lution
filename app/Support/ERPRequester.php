<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ERPRequester
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(
        string $url,
        string $serviceName,
        array  $customParam = []
    ): array {
        $jsonParams = json_encode($customParam);

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.reqJSON]" . $jsonParams);

        $ebSoapRequest = $this->genXML(
            $jsonParams,
            config('services.erp.ebAccount'),
            config('services.erp.ebPwd'),
            $serviceName
        );

        $res = $this->client->request(
            'POST',
            $url,
            [
                'body' => $ebSoapRequest
            ]
        )
            ->getBody()
            ->getContents();

        $analyzedRes = json_decode($this->analyzeSOAP($res), true);

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.resJSON]" . json_encode($analyzedRes));

        return $analyzedRes;
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

        // converting to XML and get response
        return simplexml_load_string($soapForm)->Body->response->__toString();
    }
}
