<?php

require dirname(__FILE__, 2) . '/vendor/autoload.php';
require 'dotenv.php';


class Zabbix
{
    private $url;
    private $token;
    public function __construct()
    {
        $this->url = $_SERVER["ZABBIX_URL"] . "/api_jsonrpc.php";
        $this->token = $_SERVER["ZABBIX_AUTH_TOKEN"];
    }

    public function host_get($params)
    {
        $data = [

            "jsonrpc" => "2.0",
            "method" => "host.get",
            "params" => $params,
            "id" => 1,
            "auth" => $this->token

        ];
        $response = $this->post_request($data);
        $filtered = [];
        foreach ($response['result'] as $result) {
            array_push($filtered, ["name" => $result['host'], "ip" => $result['interfaces'][0]['ip']]);
        }
        return $filtered;
    }

    private function post_request($data)
    {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($data)
            )
        ));
        $response = file_get_contents($this->url, FALSE, $context);
        if (!$response)
            throw new Exception('Falha na requisição ao Zabbix.');
        $responseData = json_decode($response, TRUE);
        return $responseData;
    }
}
