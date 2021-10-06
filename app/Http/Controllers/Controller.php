<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $description = 'order_data_sync';

    private const EB_ACCOUNT = 'IT2';
    private const EB_PWD = 'AbAO@12';
    private $headers = [
        "Content-type: text/xml;charset=\"utf-8\"",
        'Accept: text/xml',
        'Cache-Control: no-cache',
        'Pragma: no-cache'
    ];

    public function sendERPRequest(string $url, string $serviceName, array $customParam = [], $startDateTime = "", string $endDateTime = "", int $pageSize = 100, int $page = 1): array
    {
        if ($customParam) {
            $jsonParams = json_encode($customParam);
        } else {
            $jsonParams = $this->formatParams($startDateTime, $endDateTime, $page, $pageSize);
        }

        \Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.reqJSON]" . $jsonParams);

        $ebSoapRequest = $this->genXML(
            $jsonParams,
            self::EB_ACCOUNT,
            self::EB_PWD,
            $serviceName
        );

        $client = new Client();

        $res = $client->request(
            'POST',
            $url,
            [
                'headers' => $this->headers,
                'body' => $ebSoapRequest
            ]
        )->getBody()->getContents();

        $analyzedRes = json_decode($this->analyzeSOAP($res), true);

        \Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.resJSON]" . json_encode($analyzedRes));

        return $analyzedRes;
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

    private function formatParams(string $startDateTime, string $endDateTime, int $page = 1, int $pageSize = 100)
    {
        return json_encode([
            'shipDateFor' => $startDateTime,
            'shipDateTo' => $endDateTime,
            "pagination" => [
                "page" => $page, "pageSize" => $pageSize
            ]
        ]);
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
}
