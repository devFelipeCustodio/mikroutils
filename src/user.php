<?php

use \RouterOS\Client;
use \RouterOS\Config;
use \RouterOS\Query;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

class User
{
    private $router_instance;

    public function __construct()
    {
        $config = new Config([
            'host' => $_GET['gw'],
            'user' => $_ENV["LOGIN"],
            'pass' => $_ENV["PASSWORD"],
            'port' => 8728,
            'attempts' => 1]);
        $this->router_instance = new Client($config);
    }

    public function getUserByName($name){
        $if = $this->getInterfaceDataByName($name);
        $queue = $this->getQueueDataByName($name);
        $traffic = $this->getTrafficDataByName($name);
        return array_merge($if, $queue, $traffic);
    }

    private function getInterfaceDataByName($name)
    {
        $query = (new Query('/interface/pppoe-server/print'))->where('name', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getQueueDataByName($name) {
        $query = (new Query('/queue/simple/print'))->where('name', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getTrafficDataByName($name) {
        $query = (new Query('/interface/print/stats-detail'))->where('name', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }
}
