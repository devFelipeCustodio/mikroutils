<?php

require dirname(__FILE__, 2) . '/vendor/autoload.php';
require 'zabbix.php';
require 'dotenv.php';
require 'manufacturer.php';

use \RouterOS\Client;
use \RouterOS\Config;
use \RouterOS\Query;


class Search
{
    private $clients = [];
    public $gateways;
    private $gatewaysfilter = [];
    public $zabbix_error;
    public $client_errors = [];
    public function __construct()
    {
        ini_set('max_execution_time', '0');

        $zabbix = new Zabbix();

        try {
            $this->gateways = $zabbix->host_get(["output" => ["host"], "selectInterfaces" => ["ip"]]);
        } catch (\Throwable $th) {
            $this->zabbix_error = $th->getMessage();
            return;
        }
    }

    public function getClients(){


        $this->gatewaysfilter = $this->gateways;
        if (isset($_GET['gateway']) && $_GET['gateway'] != "todos") {
            $selectedGateway = $_GET['gateway'];
            foreach ($this->gatewaysfilter as $key => $value) {
                if ($key == $selectedGateway) {
                    $this->gatewaysfilter = [$value];
                    break;
                }
            }
        }

        foreach ($this->gatewaysfilter as $gw) {
            $config = new Config(
                [
                    'host' => $gw["ip"],
                    'user' => $_SERVER["LOGIN"],
                    'pass' => $_SERVER["PASSWORD"],
                    'port' => 8728,
                    'attempts' => 1
                ]
            );

            try {
                array_push(
                    $this->clients,
                    [
                        "gw_name" => $gw['name'],
                        "gw_ip" => $gw['ip'],
                        "instance" => new Client($config)
                    ]
                );
            } catch (\Throwable $th) {
                array_push($this->client_errors, [
                    "gw_name" => $gw['name'],
                    "error_message" => $th->getMessage()
                ]);
            }
        }
    }

    public function findUserByFilter($value, $filter)
    {
        $this->getClients();
        $filtered = [];
        $query = new Query('/ppp/active/print');
        $responses = [];

        foreach ($this->clients as $client) {
            $response = $client['instance']->query($query)->read();
            array_push(
                $responses,
                [
                    "gw_name" => $client['gw_name'],
                    "gw_ip" => $client['gw_ip'],
                    "results" => $response
                ]
            );
        }

        foreach ($responses as $response) {
            foreach ($response['results'] as $result) {
                $match = false;
                switch ($filter) {
                    case 'mac':
                        $match = preg_match("/" . $value . "/i", $result['caller-id']);
                        break;
                    case 'ip':
                        $match = preg_match("/" . $value . "/i", $result['address']);
                        break;
                    case 'name':
                        $match = preg_match("/" . $value . "/i", $result['name']);
                        break;
                }
                if ($match) {
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
