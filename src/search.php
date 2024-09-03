<?php

require '../vendor/autoload.php';
require 'zabbix.php';

use \RouterOS\Client;
use \RouterOS\Config;
use \RouterOS\Query;
use Spatie\Fork\Fork;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

class Search
{
    private $clients = [];
    private $gateways;
    public function __construct()
    {
        $zabbix = new Zabbix();
        $this->gateways = $zabbix->host_get(["output" => ["host"], "selectInterfaces" => ["ip"]]);
        foreach ($this->gateways as $gw) {
            $config = new Config(
                [
                'host' => $gw["ip"],
                'user' => $_ENV["LOGIN"],
                'pass' => $_ENV["PASSWORD"],
                'port' => 8728,
                'attempts' => 1
                ]
            );
            try {
                array_push(
                    $this->clients, [
                    "gw_name" => $gw['name'],
                    "gw_ip" => $gw['ip'],
                    "instance" => new Client($config)
                    ]
                );
            } catch (Exception $e) {

            }

        }
    }

    public function findUserByName($value)
    {
        $responses = [];
        $filtered = [];
        $query = new Query('/ppp/active/print');
        $functions = [];

        foreach ($this->clients as $client) {
            array_push(
                $functions, 
                function () use (&$client, &$query) {
                    $response = $client['instance']->query($query)->read();
                    return [
                        "gw_name" => $client['gw_name'],
                        "gw_ip" => $client['gw_ip'],
                        "results" => $response
                    ];
                }
            );
        }

        $responses = Fork::new()->run(
            ...$functions
        );

        foreach ($responses as $response) {
            foreach ($response['results'] as $result) {
                if (preg_match("/" . $value . "/i", $result['name'])) {
                    array_push(
                        $filtered,
                        [
                            'gw_name' => $response["gw_name"],
                            'gw_ip' => $response['gw_ip'],
                            'name' => $result['name'],
                            'address' => $result['address'],
                            'caller_id' => $result['caller-id'],
                            'uptime' => $result['uptime']
                        ]
                    );
                }
            }
        }
        return $filtered;
    }
}

// TODO: tentar implementar parelelismo
